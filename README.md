# IT8415 Recipe Platform - Group 2

A secure, database-driven recipe sharing platform developed for the **IT8415 Database Programming 2** course at Bahrain Polytechnic.

## Overview

The Recipe Platform allows users to discover, create, manage, and share recipes through a role-based web application. The system is built using PHP, MySQL, Bootstrap, and AJAX, following a three-tier architecture consisting of a presentation layer, application layer, and database layer.

The platform supports multiple user roles and provides secure authentication, recipe management, commenting, rating, and administrative reporting features.

## Features

### Visitor Features

* Browse published recipes
* Search recipes using full-text search
* View recipe details
* Read ratings and comments

### Creator Features

* User registration and login
* Create and publish recipes
* Save recipes as drafts
* Upload recipe images
* Edit and delete personal recipes
* Manage personal recipe dashboard

### Administrator Features

* Manage users and content
* View platform statistics
* Moderate recipes and comments
* Generate analytical reports using MySQL stored procedures

## Technologies Used

### Frontend

* HTML5
* CSS3
* Bootstrap 5
* JavaScript
* jQuery
* AJAX

### Backend

* PHP 8+
* PHP PDO (Prepared Statements)

### Database

* MySQL
* Stored Procedures
* Full-Text Indexing

### Development Tools

* Apache Server (XAMPP)
* phpMyAdmin
* Git & GitHub

## Database Structure

The application uses the following core tables:

* `dbproj_users`
* `dbproj_categories`
* `dbproj_recipes`
* `dbproj_comments`
* `dbproj_ratings`

Key database features include:

* Foreign key relationships
* Cascading deletes
* Full-text search indexes
* Stored procedures for reporting
* Role-based user management

## Security Features

* Password hashing using bcrypt
* PDO prepared statements
* Session-based authentication
* Role-based access control
* Input validation and sanitization
* Protection against SQL Injection

## Installation

### Prerequisites

* XAMPP or Apache Server
* PHP 8+
* MySQL
* Git

### Clone the Repository

```bash
git clone https://github.com/alihaji647/IT8415_RecipePlatform_Group2.git
```

### Configure Database

1. Create a MySQL database.
2. Import the SQL schema file.
3. Update database credentials in the configuration file.

### Run the Application

1. Copy the project folder to:

```text
xampp/htdocs/
```

2. Start Apache and MySQL using XAMPP.

3. Open your browser and navigate to:

```text
http://localhost/recipe_platform/index.php
```

## Project Structure

```text
recipe_platform/
│
├── admin/
├── creator/
├── ajax/
├── assets/
├── uploads/
├── includes/
├── database/
├── index.php
├── login.php
├── register.php
└── recipe.php
```

## Advanced Features

### AJAX-Based Comments & Ratings

Users can submit comments and ratings without refreshing the page, improving responsiveness and user experience.

### Full-Text Search

MySQL FULLTEXT indexing enables fast and accurate recipe searches.

### Stored Procedure Reporting

Administrative reports are generated using MySQL stored procedures for efficient data aggregation and analytics.

## Team Members

| Name             | Role                         | Student ID |
| ---------------- | ---------------------------- | ---------- |
| Ali Haji         | Group Leader                 | 202201868  |
| Hasan Janahi     | Core Developer               | 202203109  |
| Yusuf Naser      | Database Architecture        | 202002664  |
| Ali Sarhan       | Quality Assurance Specialist | 202204628  |
| Rashed Alsowaidi | Frontend Designer            | 202305787  |

## Course Information

**Course:** IT8415 – Database Programming 2
**Institution:** Bahrain Polytechnic

## Repository

https://github.com/alihaji647/IT8415_RecipePlatform_Group2

## License

This project was developed for educational purposes as part of the IT8415 Database Programming 2 course at Bahrain Polytechnic.
