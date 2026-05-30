8415 Recipe Platform

A Secure, Role-Partitioned Data-Driven Web Application

Course: IT8415 Database Programming 2 | Institution: Bahrain Polytechnic | Environment: Apache Server (localhost) / MySQL / PHP (PDO)

Project Overview
This project is a fully functional, enterprise-grade web application designed to fulfill the requirements of IT8415 Database Programming 2. It serves as a centralized Recipe Sharing Platform where users can browse, create, and review culinary content. The system strictly adheres to a Three-Tier Architecture: Presentation Layer (Responsive HTML/Bootstrap), Business Logic Layer (PHP/PDO), and Data Layer (MySQL). Security measures include SQL Injection protection via prepared statements, bcrypt password hashing, and role-partitioned authentication (Visitor, Creator, Administrator).

Team Members
Name

Role

Student ID

Ali Haji

Group Leader

202201868

Hasan Janahi

Core Developer

202203109

Yusuf Naser

Database Architecture

202002664

Ali Sarhan

Quality Assurance Specialist

202204628

Rashed Alsowaidi

Frontend Designer

202305787

Key Features
1. Role-Based Access Control: Visitors (read-only), Content Creators (private dashboard for drafting/publishing), Administrators (global control and analytics). 2. Advanced Search: MySQL FULLTEXT indexing for high-performance content querying. 3. AJAX Interactions: Asynchronous comments and ratings using jQuery without page reloads. 4. Administrative Reporting: Custom MySQL stored procedures (e.g., GetPopularRecipes) for data aggregation.

Tech Stack
Frontend: HTML5, CSS3, JavaScript, jQuery, Bootstrap 5 | Backend: PHP 8.x (PDO) | Database: MySQL | Server: Apache (XAMPP/WAMP)

Database Schema (DDL)
sql

Copy code
CREATE TABLE dbproj_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('viewer','creator','admin') DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE dbproj_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE dbproj_recipes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    ingredients TEXT,
    instructions TEXT,
    image_path VARCHAR(255),
    user_id INT,
    category_id INT,
    status ENUM('draft','published') DEFAULT 'draft',
    views INT DEFAULT 0,
    rating_avg DECIMAL(2,1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES dbproj_users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES dbproj_categories(id) ON DELETE RESTRICT,
    FULLTEXT(title, description)
);
Installation Guide
Prerequisites: XAMPP/WAMP installed, PHP 7.4+. Setup: 1. Clone repository to htdocs folder: git clone https://github.com/alihaji647/IT8415_RecipePlatform_Group2.git. 2. Open phpMyAdmin and create database. 3. Run the SQL schema above. 4. Navigate to http://localhost/recipe_platform/index.php.

Project Structure

Copy code
/admin - Admin dashboard scripts
/creator - Content Creator dashboard
/ajax - Async handlers
/assets - CSS, JS, Images
/includes - Shared PHP logic
index.php - Main Landing Page
login.php - Authentication
register.php - User Registration
recipe.php - Individual Recipe View
Repository
Source URL: https://github.com/alihaji647/IT8415_RecipePlatform_Group2 | Local URL: http://localhost/recipe_platform/index.php
