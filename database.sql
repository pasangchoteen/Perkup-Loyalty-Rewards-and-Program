-- Create the database
CREATE DATABASE IF NOT EXISTS perkup_db;
USE perkup_db;

-- Users table (shared for both customers and businesses)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('customer', 'business', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_email VARCHAR(100) NOT NULL UNIQUE,
    customer_first_name VARCHAR(50) NOT NULL,
    customer_last_name VARCHAR(50) NOT NULL,
    membership_status ENUM('Regular', 'Silver', 'Gold', 'Platinum') DEFAULT 'Regular',
    membership_points INT DEFAULT 0,
    profile_image VARCHAR(255) DEFAULT 'default.jpg',
    phone_number VARCHAR(20),
    address TEXT,
    referral_code VARCHAR(10) UNIQUE,
    FOREIGN KEY (customer_email) REFERENCES users(email) ON DELETE CASCADE
);

-- Businesses table
CREATE TABLE IF NOT EXISTS businesses (
    business_id INT AUTO_INCREMENT PRIMARY KEY,
    business_email VARCHAR(100) NOT NULL UNIQUE,
    business_name VARCHAR(100) NOT NULL,
    business_description TEXT,
    business_address TEXT,
    business_phone VARCHAR(20),
    business_logo VARCHAR(255) DEFAULT 'default_business.jpg',
    business_category VARCHAR(50),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    geo_radius INT DEFAULT 500, -- Radius in meters for geo-fencing
    FOREIGN KEY (business_email) REFERENCES users(email) ON DELETE CASCADE
);

-- Reward categories
CREATE TABLE IF NOT EXISTS reward_categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL,
    category_description TEXT
);

-- Rewards table
CREATE TABLE IF NOT EXISTS rewards (
    reward_id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    category_id INT,
    reward_name VARCHAR(100) NOT NULL,
    reward_description TEXT,
    points_required INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES reward_categories(category_id)
);

-- User-Business relationship (for tracking which customers are associated with which businesses)
CREATE TABLE IF NOT EXISTS user_businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    business_id INT NOT NULL,
    joined_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(business_id) ON DELETE CASCADE,
    UNIQUE KEY (customer_id, business_id)
);

-- Reward history (for tracking reward earning and redemption)
CREATE TABLE IF NOT EXISTS reward_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    reward_id INT,
    reward_type ENUM('Earned', 'Redeemed') NOT NULL,
    points_earned INT DEFAULT 0,
    points_redeemed INT DEFAULT 0,
    reward_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES rewards(reward_id) ON DELETE SET NULL
);

-- Referrals table
CREATE TABLE IF NOT EXISTS referrals (
    referral_id INT AUTO_INCREMENT PRIMARY KEY,
    referrer_id INT NOT NULL,
    referred_id INT NOT NULL,
    status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    referral_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    FOREIGN KEY (referrer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    FOREIGN KEY (referred_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type VARCHAR(50) NOT NULL,
    notification_message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample reward categories
INSERT INTO reward_categories (category_name, category_description) VALUES
('Food & Drinks', 'Rewards related to restaurants, cafes, and food services'),
('Retail', 'Rewards for retail shopping and products'),
('Services', 'Rewards for various services like spa, salon, etc.'),
('Entertainment', 'Rewards for movies, events, and other entertainment'),
('Travel', 'Rewards related to travel and accommodation');

-- Insert admin user
INSERT INTO users (username, email, password, user_type) VALUES
('admin', 'admin@perkup.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); -- password is 'password'

-- Insert sample businesses
INSERT INTO users (username, email, password, user_type) VALUES
('coffeeshop', 'brew@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business'),
('bookstore', 'books@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business'),
('restaurant', 'food@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'business');

INSERT INTO businesses (business_email, business_name, business_description, business_address, business_phone, business_category, latitude, longitude) VALUES
('brew@example.com', 'Brew & Bean Coffee Shop', 'A cozy coffee shop with a variety of specialty coffees and pastries.', '123 Coffee St, Beanville, CA 90210', '555-123-4567', 'Food & Drinks', 34.052235, -118.243683),
('books@example.com', 'Page Turner Books', 'An independent bookstore with a wide selection of books and reading events.', '456 Book Ave, Readington, NY 10001', '555-987-6543', 'Retail', 40.712776, -74.005974),
('food@example.com', 'Tasty Bites Restaurant', 'A family-friendly restaurant serving delicious meals made with fresh ingredients.', '789 Food Blvd, Flavortown, IL 60601', '555-456-7890', 'Food & Drinks', 41.878113, -87.629799);

-- Insert sample customers
INSERT INTO users (username, email, password, user_type) VALUES
('johndoe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('janedoe', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer'),
('bobsmith', 'bob@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer');

INSERT INTO customers (customer_email, customer_first_name, customer_last_name, membership_status, membership_points, referral_code) VALUES
('john@example.com', 'John', 'Doe', 'Gold', 500, 'JD500'),
('jane@example.com', 'Jane', 'Doe', 'Silver', 250, 'JD250'),
('bob@example.com', 'Bob', 'Smith', 'Regular', 100, 'BS100');

-- Associate customers with businesses
INSERT INTO user_businesses (customer_id, business_id) VALUES
(1, 1), -- John with Brew & Bean
(1, 2), -- John with Page Turner
(2, 1), -- Jane with Brew & Bean
(3, 3); -- Bob with Tasty Bites

-- Insert sample rewards
INSERT INTO rewards (business_id, category_id, reward_name, reward_description, points_required, is_active, start_date, end_date) VALUES
(1, 1, 'Free Coffee', 'Get a free coffee of your choice', 100, TRUE, '2023-01-01', '2023-12-31'),
(1, 1, 'Coffee Discount', 'Get 50% off on any coffee', 50, TRUE, '2023-01-01', '2023-12-31'),
(2, 2, 'Book Discount', 'Get 20% off on any book', 150, TRUE, '2023-01-01', '2023-12-31'),
(3, 1, 'Free Dessert', 'Get a free dessert with any meal', 200, TRUE, '2023-01-01', '2023-12-31');

-- Insert sample reward history
INSERT INTO reward_history (customer_id, reward_id, reward_type, points_earned) VALUES
(1, NULL, 'Earned', 100), -- John earned 100 points
(2, NULL, 'Earned', 50),  -- Jane earned 50 points
(3, NULL, 'Earned', 100); -- Bob earned 100 points

INSERT INTO reward_history (customer_id, reward_id, reward_type, points_redeemed) VALUES
(1, 1, 'Redeemed', 0), -- John redeemed a free coffee
(2, 2, 'Redeemed', 0); -- Jane redeemed a coffee discount
