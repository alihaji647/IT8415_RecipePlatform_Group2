-- ============================================
-- dbproj_complete.sql - COMPLETE DATABASE SETUP
-- ============================================

-- 1. CREATE TABLES
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
    FOREIGN KEY (user_id) REFERENCES dbproj_users(id),
    FULLTEXT(title, description)
);

CREATE TABLE dbproj_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipe_id INT,
    user_id INT,
    comment TEXT NOT NULL,
    rating INT CHECK (rating >=1 AND rating <=5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recipe_id) REFERENCES dbproj_recipes(id),
    FOREIGN KEY (user_id) REFERENCES dbproj_users(id)
);

CREATE TABLE dbproj_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipe_id INT,
    user_id INT,
    rating INT CHECK (rating >=1 AND rating <=5),
    FOREIGN KEY (recipe_id) REFERENCES dbproj_recipes(id),
    FOREIGN KEY (user_id) REFERENCES dbproj_users(id),
    UNIQUE KEY unique_user_recipe (user_id, recipe_id)
);

-- 2. INSERT TEST DATA
INSERT INTO dbproj_categories VALUES 
(1,'Desserts','Sweet treats'),
(2,'Main Course','Lunch/Dinner'),
(3,'Appetizers','Starters');

INSERT INTO dbproj_users VALUES 
(NULL,'admin1','admin@test.com',SHA2('admin123',256),'admin',NOW()),
(NULL,'creator1','creator@test.com',SHA2('creator123',256),'creator',NOW()),
(NULL,'viewer1','viewer@test.com',SHA2('viewer123',256),'viewer',NOW());

INSERT INTO dbproj_recipes (title,description,image_path,user_id,category_id,status) VALUES
('Chocolate Cake','Rich chocolate cake','cake.jpg',2,1,'published'),
('Pasta Carbonara','Classic Italian pasta','pasta.jpg',2,2,'published');

-- 3. CREATE STORED PROCEDURE
DELIMITER //
CREATE PROCEDURE GetPopularRecipes(IN start_date DATE, IN end_date DATE)
BEGIN
    SELECT r.title, r.views, COUNT(c.id) as comments, AVG(ra.rating) as rating
    FROM dbproj_recipes r
    LEFT JOIN dbproj_comments c ON r.id=c.recipe_id
    LEFT JOIN dbproj_ratings ra ON r.id=ra.recipe_id
    WHERE r.created_at BETWEEN start_date AND end_date
    GROUP BY r.id ORDER BY r.views DESC LIMIT 10;
END //
DELIMITER ;