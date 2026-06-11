# UNISTAY
# MKU Hostel Management System

A web-based hostel booking system built with PHP, MySQL, HTML, CSS, and JavaScript as part of the **BIT3208: Advanced Web Design and Development** coursework. The system allows students to browse available hostel rooms, book them, and view their booking history. Administrators can manage rooms, confirm or cancel bookings, and view live statistics.

---

## Table of Contents

- [Technologies Used](#technologies-used)
- [Folder Structure](#folder-structure)
- [Setup Instructions](#setup-instructions)
- [Database Schema](#database-schema)
- [User Roles & Permissions](#user-roles--permissions)
- [Features Overview](#features-overview)
- [How the System Works](#how-the-system-works)
- [Screenshot Mapping for Logbook](#screenshot-mapping-for-logbook)
- [Known Issues & Future Improvements](#known-issues--future-improvements)
- [GitHub Repository](#github-repository)

---

## Technologies Used

- **Backend:** PHP 8.0+ (PDO for database access, password hashing with bcrypt)
- **Frontend:** HTML5, CSS3 (custom forest green & brass gold theme), vanilla JavaScript (validation, password strength meter)
- **Database:** MySQL (via phpMyAdmin or MySQL CLI)
- **Server Environment:** XAMPP (Apache + MySQL) on Windows, or any LAMP stack
- **Version Control:** Git & GitHub (weekly folders for incremental development)

---

## Folder Structure

UNISTAY/
├── Week1/ # Environment setup, Hello World test
├── Week2/ # Wireframes (no code)
├── Week3/ # Basic frontend: login form, JS validation, DB connection test
│ ├── login.php
│ ├── config/
│ │ └── connection.php
│ ├── assets/
│ │ ├── css/main.css
│ │ └── js/main.js
│ └── sql/hostel_db.sql (initial schema)
├── Week4/ # Backend authentication, sessions, login processing
│ ├── login.php (merged with PHP backend)
│ ├── includes/
│ │ └── auth_check.php
│ ├── logout.php
│ └── ... (same assets and config as Week3)
├── Week5/ # Full CRUD system, dashboards, booking flow
│ ├── login.php # Login with PHP processing
│ ├── register.php # Student registration
│ ├── logout.php
│ ├── config/
│ │ └── connection.php
│ ├── includes/
│ │ ├── auth_check.php
│ │ └── layout.php # Shared header, footer, navigation
│ ├── assets/
│ │ ├── css/main.css
│ │ └── js/main.js
│ ├── admin/
│ │ ├── dashboard.php # Stats + recent bookings, confirm/cancel
│ │ ├── rooms.php # CRUD for rooms
│ │ └── bookings.php # All bookings management
│ ├── student/
│ │ ├── dashboard.php # Personal booking summary
│ │ ├── rooms.php # Browse available rooms + booking form
│ │ └── bookings.php # Own booking history + cancel
│ └── sql/
│ └── hostel_db.sql # Final schema with seed data
└── README.md


---

## Setup Instructions

### Prerequisites

- XAMPP (or any Apache + MySQL + PHP stack) installed.
- Web browser (Chrome, Firefox, Edge).

### Steps to Run the Project

1. **Start Apache and MySQL**  
   - Open XAMPP Control Panel → Start **Apache** and **MySQL**.

2. **Copy the project folder**  
   - Place the `UNISTAY` folder inside your web root:  
     - For XAMPP on Windows: `C:\xampp\htdocs\UNISTAY`
     - For LAMP on Linux: `/var/www/html/UNISTAY`

3. **Import the database**  
   - Go to `http://localhost/phpmyadmin`
   - Create a new database named `hostel_db`
   - Click **Import** → choose `UNISTAY/Week5/sql/hostel_db.sql` → click **Go**
   - This creates all tables and inserts the default admin user.

4. **Insert a test student user (optional)**  
   - In phpMyAdmin, run:
     ```sql
     INSERT INTO hostel_db.users (full_name, student_id, email, password, role)
     VALUES ('Stephen Muriithi', 'BSCCS/2024/54462', 'student@mku.ac.ke',
             '$2y$10$Iq0Jf2mVeMxZ8FqXaM5aOeAFbMl7yDIs2jqkXQ5TjPm6E.1uxvF0u', 'student');

Password is password.

Update absolute paths (if folder name is not UNISTAY/wk5)

Open login.php, includes/layout.php, and register.php.



Access the system

Visit http://localhost/UNISTAY/wk5/login.php 














































