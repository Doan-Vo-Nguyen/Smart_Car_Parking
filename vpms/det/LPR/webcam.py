import os
import time
import cv2
import torch
from dotenv import load_dotenv
import boto3
import mysql.connector
from mysql.connector import Error
import function.utils_rotate as utils_rotate
import function.helper as helper

# Load environment variables from .env file
load_dotenv()

# AWS Configuration
AWS_ACCESS_KEY = os.getenv("AWS_ACCESS_KEY")
AWS_SECRET_KEY = os.getenv("AWS_SECRET_KEY")
AWS_BUCKET_NAME = os.getenv("AWS_BUCKET_NAME")
AWS_REGION = os.getenv("AWS_REGION")

# MySQL Connection Configuration
def create_connection():
    try:
        connection = mysql.connector.connect(
            host='localhost',
            port=3307,
            user='root',
            password='',
            database='car_parking'
        )
        if connection.is_connected():
            print("Connected to MySQL database")
            return connection
    except Error as e:
        print(f"Error: {e}")
        return None

def insert_license_plate(connection, license_plate, timestamp):
    try:
        cursor = connection.cursor()
        # Query to get vehicle ID from tblvehicle
        vehicle_query = """SELECT ID FROM tblvehicle WHERE RegistrationNumber = %s"""
        cursor.execute(vehicle_query, (license_plate,))
        result = cursor.fetchone()

        if result:
            vehicle_id = result[0]
            # Insert data into tblvehiclelogs
            insert_query = """
                INSERT INTO tblvehiclelogs (RegistrationNumber, VehicleID, Intime, Status)
                VALUES (%s, %s, %s, "In")
            """
            cursor.execute(insert_query, (license_plate, vehicle_id, timestamp))
            connection.commit()
            print(f"License Plate '{license_plate}' with VehicleID '{vehicle_id}' inserted into MySQL database.")
        else:
            print(f"License Plate '{license_plate}' not found in tblvehicle.")
    except Error as e:
        print(f"Error inserting data: {e}")
    finally:
        cursor.close()

def checkout_license_plate(connection, license_plate, timestamp):
    fee = 2000
    try:
        cursor = connection.cursor()
        # Check if the vehicle is already "In"
        check_query = """SELECT * FROM tblvehiclelogs WHERE RegistrationNumber = %s AND Status = "In" ORDER BY Intime DESC LIMIT 1"""
        cursor.execute(check_query, (license_plate,))
        result = cursor.fetchone()

        if result:
            # Update the status to "Out" with checkout time
            update_query = """UPDATE tblvehiclelogs SET Outtime = %s, Status = "Out", ParkingCharge = %s WHERE RegistrationNumber = %s AND Status = "In" ORDER BY Intime DESC LIMIT 1"""
            cursor.execute(update_query, (timestamp, fee, license_plate))
            connection.commit()
            print(f"License Plate '{license_plate}' checked out and updated in MySQL database.")
        else:
            print(f"License Plate '{license_plate}' not found or already checked out.")
    except Error as e:
        print(f"Error updating data: {e}")
    finally:
        cursor.close()

# Function to upload file to S3
def upload_to_s3(file_path, bucket_name, s3_key):
    s3_client = boto3.client(
        's3',
        aws_access_key_id=AWS_ACCESS_KEY,
        aws_secret_access_key=AWS_SECRET_KEY,
        region_name=AWS_REGION
    )
    try:
        s3_client.upload_file(file_path, bucket_name, s3_key)
        print(f"File {file_path} uploaded to S3 at {bucket_name}/{s3_key}")
    except Exception as e:
        print(f"Error uploading file to S3: {e}")

# Load YOLOv5 models
yolo_LP_detect = torch.hub.load('yolov5', 'custom', path='model/LP_detector_2.pt', force_reload=True, source='local')
yolo_license_plate = torch.hub.load('yolov5', 'custom', path='model/LP_ocr_2.pt', force_reload=True, source='local')
yolo_license_plate.conf = 0.60  # Set confidence threshold

connection = create_connection()
prev_frame_time = 0
new_frame_time = 0
detection_delay = 5  # seconds
last_detection_time = 0

# Maintain a record of detected license plates with timestamps
recent_plates = {}

# Track the currently detected license plate and the last detection time
current_plate = None
last_detection_time = 0

def clean_up_recent_plates():
    """Remove entries older than the detection delay."""
    current_time = time.time()
    for plate in list(recent_plates.keys()):
        if current_time - recent_plates[plate] > detection_delay:
            del recent_plates[plate]

# Initialize video capture
vid = cv2.VideoCapture(1)


while True:
    ret, frame = vid.read()
    if not ret:
        print("Failed to grab frame.")
        break

    current_time = time.time()
    # Process if enough time has passed since the last detection
    if current_time - last_detection_time >= detection_delay:
        plates = yolo_LP_detect(frame, size=640)
        list_plates = plates.pandas().xyxy[0].values.tolist()
        detected_plate = None

        # Extract license plates detected in the current frame
        for plate in list_plates:
            x_min, y_min, x_max, y_max = map(int, plate[:4])
            crop_img = frame[y_min:y_max, x_min:x_max]

            for cc in range(0, 2):
                for ct in range(0, 2):
                    rotated_img = utils_rotate.deskew(crop_img, cc, ct)
                    detected_plate = helper.read_plate(yolo_license_plate, rotated_img)
                    if detected_plate != "unknown":
                        cv2.putText(frame, detected_plate, (x_min, y_min - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.9, (36, 255, 12), 2)
                        break
                if detected_plate != "unknown":
                    break

            # If a valid license plate is detected, break the loop
            if detected_plate != "unknown":
                break

        # Process the detected plate
        if detected_plate:
            if detected_plate != current_plate:
                # New license plate detected
                print(f"New license plate detected: {detected_plate}")

                # Save the plate image
                timestamp = time.strftime("%Y-%m-%d_%H-%M-%S")
                image_filename = f"detected_plate_{timestamp}.jpg"
                cv2.imwrite(image_filename, frame)

                # Upload image to S3
                s3_key = f"logs/{timestamp}/{image_filename}"
                upload_to_s3(image_filename, AWS_BUCKET_NAME, s3_key)
                os.remove(image_filename)  # Remove local file after uploading

                # Database operations
                if connection:
                    cursor = connection.cursor()
                    cursor.execute("SELECT Status FROM tblvehiclelogs WHERE RegistrationNumber = %s ORDER BY Intime DESC LIMIT 1", (detected_plate,))
                    status = cursor.fetchone()
                    if status and status[0] == "In":
                        # If the vehicle is already "In", check it out
                        checkout_license_plate(connection, detected_plate, timestamp)
                    else:
                        # Otherwise, insert as a new entry
                        insert_license_plate(connection, detected_plate, timestamp)

                # Update the current plate and detection time
                current_plate = detected_plate
                last_detection_time = current_time
            else:
                # Same vehicle still in the detection area; do nothing
                print(f"License plate '{detected_plate}' is still in the detection area. No action taken.")
        else:
            # No valid license plate detected
            current_plate = None

    # Display FPS on the video frame
    new_frame_time = time.time()
    fps = 1 / (new_frame_time - prev_frame_time) if prev_frame_time != 0 else 0
    prev_frame_time = new_frame_time
    fps_text = f"FPS: {int(fps)}"
    cv2.putText(frame, fps_text, (7, 70), cv2.FONT_HERSHEY_SIMPLEX, 3, (100, 255, 0), 3, cv2.LINE_AA)
    cv2.imshow('License Plate Detection', frame)

    # Exit condition
    if cv2.waitKey(1) & 0xFF == ord('q'):
        print("Exiting.")
        break

# Cleanup
vid.release()
cv2.destroyAllWindows()

if connection and connection.is_connected():
    connection.close()
    print("MySQL connection closed.")