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

Successfully implemented the CRUD (Create, Read, Update, Delete) functionality for the Assignment Management module. The system now correctly identifies lecturers using their primary user_id to ensure secure data handling. It was resolved to address initial routing issues and folder directory mismatches. The next phase involves developing the student-side interface to allow enrolled students to view and download the assignment briefs uploaded by their lecturers."

Successfully implemented the core functionality for the Lecturer and Student portals. This involved creating a secure bridge between the database and the user interface.

🛠 Issues Solved (The "Battle" Log)
Database Synchronization: Fixed a critical mismatch where the system was looking for a legacy lecturer_id instead of the global user_id.

The "404 Not Found" Mystery: After many attempts, we discovered that the timetable.php file had a hidden leading character (a space or invisible symbol) in its name. This caused the web server to ignore the file even though it looked correct to the human eye.

Routing & Paths: Standardized all form actions to use relative paths (includes/filename.php), ensuring the app works perfectly on a local MAMP server.

File Management: Created a robust upload system for assignment briefs and fixed the "View File" links.
Feature,Status,Solution
Lecturer: Create/Edit/Delete,✅ Working,Implemented full CRUD with user_id validation.
File Upload System,✅ Working,Files now save correctly to the /uploads directory.
Student Timetable,✅ Working,Resolved the filename encoding error that caused 404s.
Profile Dashboard,✅ Working,"Dynamic role-based views for Admin, Lecturer, and Student."

Implemented a full Student Assignment Submission system, allowing students to view only their enrolled unit assignments, download briefs, and submit their work with file uploads.
During development, routing, file-path, and permission issues caused 404 errors and invisible files.
These were resolved by correcting database relations, upload directories, and download paths, resulting in a fully working LMS-style submission flow.

Fixed assignment file upload and submission handling.  
Implemented proper multiple-file upload logic, linked uploaded files to submissions via `submission_files`, and corrected Lecturer view to display submitted files correctly.  
Updated Student, Lecturer, Profile, and Upload-related components to ensure consistent file storage and retrieval.
Implemented full grading workflow: lecturers can now assign, update, and delete marks with mandatory feedback.
Connected grading to student dashboards, displaying submission status, grades, and feedback in My Assignments.
Performed database sanitation by removing invalid submissions without files and stabilizing Lecturer Submission View.
Improved dashboard UI spacing and controls for professional LMS usability.
 Profile Customization
* **Profile Pictures:** Implemented a file upload system for profile pictures, allowing both Students and Lecturers to personalize their accounts.
* **Bug Fixes:** Resolved issues with image pathing and directory permissions to ensure avatars display correctly on the dashboard.
 Technical Troubleshooting (The "Hard Parts")

* **Filename Audit:** Identified and fixed a hidden character encoding error in `timetable.php` that was causing persistent 404 errors.
* **Variable Logic:** Fixed a critical bug in the assignment display table where a variable mismatch (`$row` vs `$assignment`) prevented file links from showing up.
Implemented a “My Units” section on the student profile so students can clearly see their assigned/enrolled units and understand what their coursework/assignments are linked to.
Refactored the timetable system from individual student schedules to a 
course-based model. Admin now creates timetable entries per course, and all 
students with the same course_id automatically share the same schedule. 
Student timetable view was updated to show start and end times correctly.
- Fixed deletion logic in **Active Academic Structure**: the Delete button now correctly removes a **unit** instead of a course.  
- A course can be deleted only after all related units have been removed.
Issues were identified with course–timetable relationships and deletion behavior in the admin panel. The system now correctly prevents course deletion when dependent units exist, ensuring data integrity. Orphaned timetable entries created after course removal can be reassigned to a new course, allowing existing schedules to be preserved rather than lost. As a result, timetable visibility for students is restored once course associations are aligned, making the academic structure more consistent and reliable.

HTML Validation

Key pages of the system were validated to ensure correct HTML structure and compliance with web standards.
Validation was performed by opening each page in the browser, selecting View Page Source, copying the rendered HTML, and checking it using the W3C HTML Validator.

The following pages were validated:

login.php

register.php

profile.php

student-assignments.php

lecturer-submissions.php

timetable.php

create-assignment.php

Bootstrap was used for layout and responsiveness.
Any warnings caused by development-only meta cache directives were removed or replaced with proper server headers where necessary.

Footer Layout Fix

An issue occurred where the footer appeared in the middle of the page when the page content was short, and overlapped elements in some dashboards.

This was resolved by implementing a flexbox layout:

The <body> element was set to a vertical flex container with full viewport height.

A <main> wrapper was introduced to allow the page content to expand.

The footer was given mt-auto so it automatically stays at the bottom of the page.

This ensured consistent footer positioning across all pages, regardless of content length.

Update (9 May 2026)
Interface Evolution: From Stacked Layout to 3-Column Dashboard
We have completely overhauled the page structure, moving away from a vertical "stacked" layout (where sections sat on top of each other) to a highly efficient three-column dashboard. This maximizes screen real estate and eliminates unnecessary scrolling.

1. Dashboard Architecture (Main Workspace)
Left Column (Navigation & Units):

Quick access to Dashboard, My Units (e.g., Software Project), and My Timetable.

One-click navigation to assignments and performance analytics.

Center Column (Interactive Workspace):

Performance Level Indicator: A visual achievement scale using dynamic color-coded bands.

Real-time notifications regarding module completion requirements.

Right Column (Widgets & Planning):

Academic Calendar: An interactive view for May 2026.

Events Log: Highlights critical deadlines, such as "Assignment Due (May 22)" and "End of Term (June 1)".

2. Profile Page Optimization (2-Column Layout)
Balanced Design: The Profile page now utilizes a symmetrical two-column grid.

Components: Personal Information (Photo/Details) and the Control Panel (for Lecturers) or Progress Tracker (for Students) now sit side-by-side. This creates a professional, "at-a-glance" user experience.

3. Intelligent Cumulative Progress Logic
Real-Time Summation: Implemented an algorithm that calculates the total score dynamically. The student sees their "success chain" (e.g., 18 + 23 = 41).

Automated Grade Bands: Configured a threshold trigger at 41 points. The system automatically upgrades the status from neutral (⚪) to 🔴 Minimum Pass, displaying the motivational prompt: "You have 41/50 so far. Keep going!"

Thresholds:

🔴 Minimum Pass: 50–61% (Triggered early at 41 for progress tracking).

🟡 Satisfactory: 62–79%.

🟢 Merit: 80–89%.

🔵 Distinction: 90%+.

4. Technical & UX Enhancements (Bug Fixes)
Navigation Sync: Resolved the 404 Not Found error during assignment uploads by synchronizing the Action button paths with the student-submit-assignment.php file.

Column Reordering: Optimized the Assignments table by moving the Action column (Submit/Done) before the Grade column to improve user workflow.

Role-Based Adaptive Padding: Integrated a PHP check to manage layout spacing. Lecturers receive an increased top margin (mt-5 pt-5) to prevent UI overlap with the fixed header, while Students maintain a standard compact view.

Feedback System: Fixed the JavaScript logic for Feedback Bubbles. Teacher comments now toggle correctly within the table without disrupting the grid alignment.
July–August 2026 — Kazakhstan
Timetable UI Refactoring
Refactored the student timetable interface from the previous table-based presentation to a card-based layout.
Improved the visual grouping of individual lessons and timetable information.
Updated lesson time display to clearly show start and end times.
Improved responsive layout and readability of timetable entries.
Lecturer Weekly Teaching Plan

Implemented a lecturer-side Weekly Teaching Plan module connected to academic units.

Lecturer functionality:

Create weekly teaching-plan entries.
Edit existing weekly entries.
Define week number, topic, description, learning objectives and homework.
Upload teaching materials:
Lecture Notes
PowerPoint
Tutorial
Reading Material
Quiz
Store weekly teaching-plan data in thematic_plans and thematic_items.
Added is_published status control.
Implemented Publish / Unpublish toggle functionality.
Student Units & Weekly Plan

Implemented the student-side interface for accessing enrolled units and their published teaching content.

Student functionality:

View enrolled academic units.
Open an individual unit through View Unit.
View published weekly teaching-plan entries.
Access published teaching materials directly from the unit page.
View topic descriptions, learning objectives and homework.
Lecturer–Student Content Integration

Connected the lecturer teaching-plan workflow with the student learning interface:

Lecturer → Weekly Plan → Database → Published Content → Enrolled Student

Only weekly-plan items with is_published = 1 are returned to the student interface, while draft/unpublished items remain unavailable to students.

Technical Changes
Added database-driven publication control through thematic_items.is_published.
Added file upload handling for weekly teaching materials.
Updated routing between lecturer and student weekly-plan pages.
Added enrolled-unit validation before displaying student learning content.
Resolved file-path, database and routing issues during integration.