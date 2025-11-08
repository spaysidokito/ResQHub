-- ResQHub Database Setup Script
-- Run this script to create the database and user

-- Create database
CREATE DATABASE IF NOT EXISTS resqhub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional - for production)
-- CREATE USER IF NOT EXISTS 'resqhub_user'@'localhost' IDENTIFIED BY 'your_secure_password';
-- GRANT ALL PRIVILEGES ON resqhub.* TO 'resqhub_user'@'localhost';
-- FLUSH PRIVILEGES;

-- Use the database
USE resqhub;

-- Show database info
SELECT 'Database created successfully!' AS status;
SELECT DATABASE() AS current_database;

-- Note: Run Laravel migrations after this:
-- php artisan migrate
