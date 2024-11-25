from PIL import Image
import cv2
import torch
import math
import function.utils_rotate as utils_rotate
from IPython.display import display
import os
import time
import argparse
import function.helper as helper
import mysql.connector
from mysql.connector import Error

# MySQL connection configuration
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
        
        # Truy vấn vehicleID từ tblvehicle
        vehicle_query = """SELECT ID FROM tblvehicle WHERE RegistrationNumber = %s"""
        cursor.execute(vehicle_query, (license_plate,))
        result = cursor.fetchone()
        
        if result:
            vehicle_id = result[0]
            
            # Chèn dữ liệu vào tblvehiclelogs
            insert_query = """
                INSERT INTO tblvehiclelogs (VehicleID, RegistrationNumber, Intime, Status)
                VALUES (%s, %s, %s, "In")
            """
            cursor.execute(insert_query, (vehicle_id, license_plate, timestamp))
            connection.commit()
            print(f"License Plate '{license_plate}' with VehicleID '{vehicle_id}' inserted into MySQL database.")
        else:
            print(f"License Plate '{license_plate}' not found in tblvehicle.")
    
    except Error as e:
        print(f"Error inserting data: {e}")
    
    finally:
        cursor.close()


# Check out a vehicle by updating status to "Out" and recording checkout time
def checkout_license_plate(connection, license_plate, timestamp):
    try:
        cursor = connection.cursor()
        # Check if the vehicle is already "In"
        check_query = """SELECT * FROM tblvehiclelogs WHERE RegistrationNumber = %s AND Status = "In" ORDER BY Intime DESC LIMIT 1"""
        cursor.execute(check_query, (license_plate,))
        result = cursor.fetchone()

        if result:
            # Update the status to "Out" with checkout time
            update_query = """UPDATE tblvehiclelogs SET Outtime = %s, Status = "Out" WHERE RegistrationNumber = %s AND Status = "In" ORDER BY Intime DESC LIMIT 1"""
            cursor.execute(update_query, (timestamp, license_plate))
            connection.commit()
            print(f"License Plate '{license_plate}' checked out and updated in MySQL database.")
        else:
            print(f"License Plate '{license_plate}' not found or already checked out.")
    except Error as e:
        print(f"Error updating data: {e}")
    finally:
        cursor.close()

connection = create_connection()

# Load YOLOv5 models
yolo_LP_detect = torch.hub.load('yolov5', 'custom', path='model/LP_detector_nano_61.pt', force_reload=True, source='local')
yolo_license_plate = torch.hub.load('yolov5', 'custom', path='model/LP_ocr_nano_62.pt', force_reload=True, source='local')
yolo_license_plate.conf = 0.60  # Set confidence threshold

prev_frame_time = 0
new_frame_time = 0

# Initialize video capture (use 0 for default camera or replace with video file path)
vid = cv2.VideoCapture(1)
# vid = cv2.VideoCapture("1.mp4")

# Time delay between detecting consecutive plates
detection_delay = 5  # seconds
last_detection_time = 0
output_dir = "detected_frames"

while(True):
    ret, frame = vid.read()
    
    if not ret:
        print("Failed to grab frame.")
        break
    
    # Get the current time to calculate the delay
    current_time = time.time()
    
    # Only process frames if the delay has passed
    if current_time - last_detection_time >= detection_delay:
        # Detect license plates in the frame
        plates = yolo_LP_detect(frame, size=640)
        list_plates = plates.pandas().xyxy[0].values.tolist()
        list_read_plates = set()
        
        for plate in list_plates:
            flag = 0
            x_min = int(plate[0])  # xmin
            y_min = int(plate[1])  # ymin
            x_max = int(plate[2])  # xmax
            y_max = int(plate[3])  # ymax
            w = x_max - x_min
            h = y_max - y_min
            
            # Crop the detected license plate region
            crop_img = frame[y_min:y_min + h, x_min:x_min + w]
            cv2.rectangle(frame, (x_min, y_min), (x_max, y_max), color=(0, 0, 225), thickness=2)
            
            # Attempt to read the license plate with different rotations
            lp = ""
            for cc in range(0, 2):
                for ct in range(0, 2):
                    rotated_img = utils_rotate.deskew(crop_img, cc, ct)
                    lp = helper.read_plate(yolo_license_plate, rotated_img)
                    if lp != "unknown":
                        list_read_plates.add(lp)
                        # Annotate the plate number on the frame
                        cv2.putText(frame, lp, (x_min, y_min - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.9, (36, 255, 12), 2)
                        flag = 1
                        break
                if flag == 1:
                    break
            
            # Detect license plate and save the image and details
            if lp != "unknown":
                timestamp = time.strftime("%y-%m-%d-%H:%M:%S")
                image_filename = f"{output_dir}/detected_plate_{timestamp}.jpg"
                cv2.imwrite(image_filename, frame)
                print(f"Frame saved as {image_filename}")

                # Check if the vehicle is checking out
                if connection is not None:
                    cursor = connection.cursor()
                    cursor.execute("SELECT Status FROM tblvehiclelogs WHERE RegistrationNumber = %s ORDER BY Intime DESC LIMIT 1", (lp,))
                    status = cursor.fetchone()
                    if status and status[0] == "In":
                        checkout_license_plate(connection, lp, timestamp)
                    else:
                        insert_license_plate(connection, lp, timestamp)
                
                with open("detected_plates.txt", "a") as file:
                    file.write(f"{timestamp} - {lp}\n")
                print(f"License Plate '{lp}' written to detected_plates.txt with timestamp")

                last_detection_time = current_time
                break
    
    # Calculate and display FPS
    new_frame_time = time.time()
    fps = 1 / (new_frame_time - prev_frame_time) if prev_frame_time != 0 else 0
    prev_frame_time = new_frame_time
    fps_text = f"FPS: {int(fps)}"
    cv2.putText(frame, fps_text, (7, 70), cv2.FONT_HERSHEY_SIMPLEX, 3, (100, 255, 0), 3, cv2.LINE_AA)
    
    # Display the frame
    cv2.imshow('License Plate Detection', frame)
    
    # Exit if 'q' is pressed
    if cv2.waitKey(1) & 0xFF == ord('q'):
        print("Exiting.")
        break

# Release video capture and close windows
vid.release()
cv2.destroyAllWindows()

# Close MySQL connection
if connection is not None and connection.is_connected():
    connection.close()
    print("MySQL connection closed.")
