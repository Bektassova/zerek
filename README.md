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

Set up PHP CS Fixer for PHP code formatting, fixed admin-unit-enroll.php to correctly enroll students with flash messages without errors, and adjusted form handlers’ redirects to return to the appropriate page after saving changes.

Set up PHP CS Fixer, fixed student enrollment and unit edit/delete flows with flash messages, and ensured all redirects go to the correct admin pages with proper navigation.
Implemented multi-lecturer assignment to units, updated Active Academic Structure to show assigned lecturers, and ensured proper redirects and flash messages in admin panel.

Continued improving the Admin Panel, focusing on assigning lecturers to units.

Tested and adjusted the Assign Lecturers form, identified issues with duplicates and database insertion.

Worked on redirects and flash messages for Edit/Delete actions in Unit and Course Management to ensure proper navigation.

Reviewed and analyzed foreign key constraint errors related to lecturer assignments.

Planned improvements for Delete Course logic to match the behavior of Unit Management.

Lecturer Assignment Management: Lecturers can create assignments and upload files for their units. Work is still pending on displaying units in the dropdown, editing and deleting assignments, and ensuring students can view them.
What has been done so far:
Lecturers can now create assignments for their units, including uploading files as assignment briefs. Created assignments are displayed in the My Assignments table with all relevant information: Title, Unit, Due Date, File, and Created date. Flash messages confirm successful creation.

Current status:

+ Lecturers can create assignments and upload files.

+ Assignments are visible in the My Assignments table.

Fixed the Foreign Key constraint error in the lecturer assignment system by switching the identifier from lecturer_id to user_id. This ensures that lecturers are correctly linked to units using their primary system identity from the users table, allowing for accurate data storage and display of names in the academic structure.