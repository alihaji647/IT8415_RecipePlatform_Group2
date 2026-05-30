🍴 IT8415 Recipe Platform
A comprehensive data-driven web application built with PHP and MySQL, designed to provide a secure, role-partitioned recipe sharing platform with user authentication, content management, and administrative analytics.

📋 Table of Contents
Features
Technology Stack
Prerequisites
Installation
Database Setup
Running the Application
Usage
Project Structure
Database Schema
Contributing
License
Support
Version History
Acknowledgments
✨ Features
🔐 Authentication & Security
User Registration: Secure account creation with email validation
User Login/Logout: Authentication system with session management
Password Hashing: Secure bcrypt password encryption
Role-based Access: Protected views with three-tier authorization (Visitor, Creator, Administrator)
SQL Injection Protection: PDO prepared statements for all database queries
Session Management: Secure PHP session handling
📝 Recipe Management
Add Recipes: Create new recipes with title, ingredients, instructions, and image uploads
View Recipes: Browse recipe listings with thumbnail previews
Update Recipes: Edit recipe details including status (draft/published)
Delete Recipes: Remove recipes with cascade deletion
Search Recipes: Advanced full-text search by title or description
👥 User Management
Content Creators: Private dashboard for managing personal recipes
Administrators: Global system control for users and content
User Profiles: Track created recipes and activity
💬 Interactive Features
Comments System: Users can comment on recipes
Rating System: Star-based rating functionality
View Tracking: Track recipe view counts
AJAX Interactions: Asynchronous submissions without page reloads
📊 Administrative Features
Dashboard Analytics: Overview of system metrics
User Management: View and delete user accounts
Content Moderation: Remove inappropriate content
Reporting Tools: Custom stored procedures for data aggregation
Activity Logs: Track recent system activities
🛠️ Technology Stack
Backend
PHP 8.x: Server-side scripting language with PDO
MySQL: Relational database management system
Apache: Web server (XAMPP/WAMP)
Frontend
HTML5: Markup language for web pages
CSS3: Styling and layout
Bootstrap 5: Responsive CSS framework
JavaScript: Client-side scripting
jQuery: JavaScript library for AJAX interactions
Development Tools
Git: Version control system
phpMyAdmin: MySQL database management
XAMPP/WAMP: Local development environment
📋 Prerequisites
Before running this application, ensure you have the following installed:

System Requirements
PHP 7.4+ (preferably PHP 8.x)
MySQL 5.7+ running on localhost:3306
Apache HTTP Server (included in XAMPP/WAMP)
Git for version control
Web browser (Chrome, Firefox, Safari, or Edge)
Environment
XAMPP or WAMP (recommended for Windows)
MAMP (for Mac)
LAMP (for Linux)
🚀 Installation
1. Clone the Repository
bash

Copy code
git clone https://github.com/alihaji647/IT8415_RecipePlatform_Group2.git
cd IT8415_RecipePlatform_Group2
2. Move to Web Server Directory
bash

Copy code
# For XAMPP (Windows)
mv * C:/xampp/htdocs/recipe_platform/

# For WAMP (Windows)
mv * C:/wamp64/www/recipe_platform/

# For MAMP (Mac)
mv * /Applications/MAMP/htdocs/recipe_platform/

# For Linux
mv * /var/www/html/recipe_platform/
3. Start Apache and MySQL
Open XAMPP Control Panel
Start Apache module
Start MySQL module
🗄️ Database Setup
MySQL Configuration
Access phpMyAdmin

Navigate to: http://localhost/phpmyadmin/
Create Database

Click "New" to create a new database
Name: recipe_platform
Collation: utf8mb4_general_ci
Import Schema

Select the created database
Go to "SQL" tab
Run the following SQL commands:
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
Create Test Users
Insert default admin, creator, and viewer accounts via phpMyAdmin
▶️ Running the Application
1. Start the Development Server
Ensure Apache and MySQL are running in XAMPP/WAMP
2. Access the Application
Main Website: http://localhost/recipe_platform/index.php
Login: http://localhost/recipe_platform/login.php
Register: http://localhost/recipe_platform/register.php
3. Default Access Levels
Administrator: Full system access
Content Creator: Recipe management dashboard
Visitor: Read-only access
📖 Usage
For Visitors
Browse Recipes

Visit the homepage to view published recipes
Use the search bar for full-text search
View Recipe Details

Click on any recipe to view full details
Read ingredients and instructions
Search

Use keywords to search recipes
Results ranked by relevance
For Content Creators
Register/Login

Create an account or login with existing credentials
Dashboard

Access personal creator dashboard
View list of your recipes
Add Recipes

Click "Add Recipe" to create new recipes
Fill in title, description, ingredients, instructions
Upload image (optional)
Set status to "draft" or "published"
Edit Recipes

Edit existing recipes
Update content or change status
For Administrators
Admin Dashboard

Access global administrative panel
View system analytics and statistics
User Management

View all registered users
Delete user accounts
Content Management

View all recipes (including drafts)
Delete inappropriate content
Reports

Generate reports using stored procedures
View popular recipes and user activity
📁 Project Structure

Copy code
recipe_platform/
├── admin/                       # Admin dashboard scripts
│   ├── dashboard.php           # Main admin dashboard
│   ├── manage_users.php        # User management
│   ├── reports.php            # Analytics and reports
│   └── delete_user.php        # User deletion handler
├── creator/                    # Content Creator dashboard
│   ├── dashboard.php          # Creator main dashboard
│   ├── add_recipe.php        # Add new recipe form
│   ├── edit_recipe.php       # Edit recipe form
│   └── delete_recipe.php    # Recipe deletion handler
├── ajax/                      # Async handlers (jQuery/AJAX)
│   ├── add_comment.php       # AJAX comment submission
│   └── rate_recipe.php      # AJAX rating submission
├── assets/                    # Static files
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── images/              # Uploaded images
├── includes/                  # Shared PHP logic
│   ├── db_connection.php   # Database connection
│   ├── header.php          # Common header
│   └── footer.php          # Common footer
├── index.php                 # Main homepage
├── login.php                 # User login
├── register.php              # User registration
├── logout.php               # Logout handler
├── recipe.php               # Recipe detail view
├── search.php               # Search results
├── category.php             # Category filter
└── README.md                # This file
🗄️ Database Schema
Users Table (dbproj_users)
Column

Type

Constraints

id

INT

PRIMARY KEY, AUTO_INCREMENT

username

VARCHAR(50)

UNIQUE, NOT NULL

email

VARCHAR(100)

UNIQUE, NOT NULL

password

VARCHAR(255)

NOT NULL

role

ENUM

'viewer', 'creator', 'admin'

created_at

TIMESTAMP

DEFAULT CURRENT_TIMESTAMP

Categories Table (dbproj_categories)
Column

Type

Constraints

id

INT

PRIMARY KEY, AUTO_INCREMENT

name

VARCHAR(50)

NOT NULL

description

TEXT

Recipes Table (dbproj_recipes)
Column

Type

Constraints

id

INT

PRIMARY KEY, AUTO_INCREMENT

title

VARCHAR(200)

NOT NULL

description

TEXT

ingredients

TEXT

instructions

TEXT

image_path

VARCHAR(255)

user_id

INT

FOREIGN KEY -> dbproj_users

category_id

INT

FOREIGN KEY -> dbproj_categories

status

ENUM

'draft', 'published'

views

INT

DEFAULT 0

rating_avg

DECIMAL(2,1)

DEFAULT 0

created_at

TIMESTAMP

DEFAULT CURRENT_TIMESTAMP

🤝 Contributing
This project is part of the IT8415 Database Programming 2 course requirements at Bahrain Polytechnic.

Team Members
Ali Haji (Group Leader) - Student ID: 202201868
Hasan Janahi (Core Developer) - Student ID: 202203109
Yusuf Naser (Database Architecture) - Student ID: 202002664
Ali Sarhan (Quality Assurance Specialist) - Student ID: 202204628
Rashed Alsowaidi (Frontend Designer) - Student ID: 202305787
Development Process
Regular team meetings via WhatsApp
GitHub-based version control
Modular development approach
Peer code reviews
📄 License
This project is submitted as part of the IT8415 Final Project requirements at Bahrain Polytechnic. All rights reserved by the development group.

🆘 Support
If you encounter any issues or have questions:

Review the README documentation
Check the database schema is correctly imported
Ensure Apache and MySQL services are running
Verify file permissions in the htdocs directory
Check PHP configuration for errors
🔄 Version History
Version 1.0.0
Initial release
Complete CRUD operations for recipes
User authentication system (Visitor, Creator, Admin)
Full-text search functionality
AJAX comment and rating system
Administrative dashboard with reporting
MySQL stored procedures integration
Responsive Bootstrap UI
🙏 Acknowledgments
Bahrain Polytechnic for the educational framework
IT8415 Database Programming 2 course instructors
Apache Friends for XAMPP development environment
PHP community for excellent documentation
Bootstrap for responsive UI components
Built with ❤️ by IT8415 Group 2 - Bahrain Polytechnic
