CREATE DATABASE IF NOT EXISTS hostel_db;
USE hostel_db;


CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    full_name   VARCHAR(120)  NOT NULL,
    student_id  VARCHAR(30)   NOT NULL UNIQUE,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,  
    role        ENUM('student','admin') DEFAULT 'student',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS blocks (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    block_name VARCHAR(60) NOT NULL,
    gender     ENUM('male','female','mixed') NOT NULL
);


CREATE TABLE IF NOT EXISTS rooms (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    block_id           INT NOT NULL,
    room_number        VARCHAR(10) NOT NULL,
    room_type          ENUM('single','double','triple') NOT NULL,
    capacity           TINYINT NOT NULL DEFAULT 1,
    price_per_semester DECIMAL(10,2) NOT NULL,
    status             ENUM('available','full','maintenance')
                       DEFAULT 'available',
    FOREIGN KEY (block_id) REFERENCES blocks(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS bookings (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    student_id     INT NOT NULL,
    room_id        INT NOT NULL,
    semester       VARCHAR(30) NOT NULL,
    check_in_date  DATE NOT NULL,
    check_out_date DATE NOT NULL,
    total_fee      DECIMAL(10,2) NOT NULL,
    status         ENUM('pending','confirmed','cancelled')
                   DEFAULT 'pending',
    booked_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (room_id)    REFERENCES rooms(id) ON DELETE CASCADE
);


INSERT INTO users (full_name, student_id, email, password, role)
VALUES ('Hostel Administrator', 'ADM-001', 'admin@mku.ac.ke',
        '$2y$10$Iq0Jf2mVeMxZ8FqXaM5aOeAFbMl7yDIs2jqkXQ5TjPm6E.1uxvF0u', 'admin');

INSERT INTO blocks (block_name, gender) VALUES
  ('Block A – Male', 'male'),
  ('Block B – Female', 'female'),
  ('Block C – Mixed', 'mixed');

INSERT INTO rooms
  (block_id, room_number, room_type, capacity, price_per_semester) VALUES
  (1, 'A-101', 'single', 1, 15000.00),
  (1, 'A-102', 'double', 2, 10000.00),
  (2, 'B-101', 'single', 1, 15000.00),
  (2, 'B-102', 'double', 2, 10000.00),
  (3, 'C-101', 'double', 2, 11000.00);