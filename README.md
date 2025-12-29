Zerek - School Management System (SMS)
Zerek is a web-based educational management platform designed to streamline academic administration. The project focuses on managing the relationship between courses, academic units, and student timetables.

 Project Overview
This project was developed as a practical application of PHP and MySQL, focusing on CRUD (Create, Read, Update, Delete) operations, secure database interactions, and a responsive admin dashboard.

Key Features
1. Academic Structure Management
Course Creation: Admins can create major courses (e.g., Business Studies, Economics).

Course Protection: Integrated logic prevents the deletion of courses that have active units assigned to them.

Unit Management: A dedicated system to add, edit, and delete academic units (subjects) linked to specific courses.

Description System: Detailed descriptions for each unit to provide clarity on the academic syllabus.

2. Timetable System
Admin Controls: Ability to create and manage weekly schedules.

Student View: A clean interface for students to view their upcoming lessons and timings.

3. User & Profile Management
Secure Authentication: Role-based access control for Admins and Students.

Profile Editing: Users can update their personal information and manage account settings.

 Tech Stack
Frontend: HTML5, CSS3, Bootstrap 5 (for responsive design), FontAwesome (icons).

Backend: PHP 8.x.

Database: MySQL / MariaDB.

Server: MAMP / Localhost.

 Database Architecture
The project utilizes a relational database with the following core tables:

users: Stores credentials and roles (Admin/Student).

courses: Stores the main academic programs.

units: Stores specific subjects linked to courses (includes unit_description).

timetables: Manages the scheduling logic.

 Installation & Setup
Clone the repository to your local server directory (e.g., htdocs in MAMP).

Import the zerek_db.sql (or your database file) into phpMyAdmin.

Configure includes/dbh.php with your local database credentials.

Open the project in your browser via localhost/zerek.

Today I implemented the Student Enrollment Management functionality
in the Admin Control Panel.

Key features added:
- Admin can assign a course to each student
- Students table now supports course_id with foreign key constraint
- Course filter added to easily view students by course
- Flash messages added for successful and failed actions
- Proper session handling and access control for Admin role
- Cleaned up includes folder structure and fixed routing issues

This brings the project closer to a real-world school management system
with clear admin workflows and data integrity.
