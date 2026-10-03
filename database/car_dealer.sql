-- =====================================================
-- AutoDeal Car Sales / Dealership Website
-- Database: car_dealer
-- Import this file in phpMyAdmin (XAMPP) to set everything up.
-- =====================================================

CREATE DATABASE IF NOT EXISTS car_dealer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE car_dealer;

-- -----------------------------------------------------
-- Table: users  (admin accounts that manage listings)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','agent') NOT NULL DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin login -> username: admin | password: admin123
-- (change this password after first login!)
INSERT INTO users (full_name, username, password, role) VALUES
('System Administrator', 'admin', '$2b$12$qDHMHQmDBPHfZ0HRXuKBIO0vBo6Y2SA8dr9kqDO7s.1jr9ekWaZYW', 'admin');

-- -----------------------------------------------------
-- Table: cars (main listings)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    make VARCHAR(60) NOT NULL,
    model VARCHAR(60) NOT NULL,
    year INT NOT NULL,
    price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    mileage INT DEFAULT NULL,
    transmission ENUM('automatic','manual') NOT NULL DEFAULT 'automatic',
    fuel_type ENUM('petrol','diesel','hybrid','electric') NOT NULL DEFAULT 'petrol',
    body_type VARCHAR(40) DEFAULT NULL,
    color VARCHAR(30) DEFAULT NULL,
    location VARCHAR(100) DEFAULT NULL,
    status ENUM('available','reserved','sold') NOT NULL DEFAULT 'available',
    featured TINYINT(1) NOT NULL DEFAULT 0,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: car_images (gallery, multiple photos per car)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS car_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: inquiries (leads from the public "Contact seller" form)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(120) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    status ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Demo data — 6 sample listings with placeholder photos
-- -----------------------------------------------------
INSERT INTO cars (title, make, model, year, price, mileage, transmission, fuel_type, body_type, color, location, status, featured, description) VALUES
('Toyota Axio 2015', 'Toyota', 'Axio', 2015, 1150000.00, 78000, 'automatic', 'petrol', 'Sedan', 'White', 'Nairobi', 'available', 1, 'Clean, well-maintained Toyota Axio. Single owner, full service history, non-accident. Ready for immediate transfer.'),
('Mazda Demio 2016', 'Mazda', 'Demio', 2016, 950000.00, 62000, 'automatic', 'petrol', 'Hatchback', 'Red', 'Mombasa', 'available', 1, 'Fuel-efficient and compact — perfect first car or city runabout. Alloy rims, well serviced.'),
('Subaru Forester 2017', 'Subaru', 'Forester', 2017, 2450000.00, 55000, 'automatic', 'petrol', 'SUV', 'Green', 'Nairobi', 'available', 1, 'All-wheel drive, spacious family SUV. Excellent for both town and upcountry roads.'),
('Toyota Prado 2019', 'Toyota', 'Land Cruiser Prado', 2019, 6800000.00, 40000, 'automatic', 'diesel', 'SUV', 'Grey', 'Nairobi', 'available', 1, 'Top-of-the-range Prado TX-L. Leather interior, sunroof, reverse camera. Immaculate condition.'),
('Nissan Note 2014', 'Nissan', 'Note', 2014, 780000.00, 91000, 'automatic', 'petrol', 'Hatchback', 'Silver', 'Kisumu', 'reserved', 0, 'Economical hybrid engine, great fuel consumption. Ideal for Uber/Bolt business.'),
('Honda Fit 2016', 'Honda', 'Fit', 2016, 990000.00, 70000, 'automatic', 'hybrid', 'Hatchback', 'Blue', 'Nakuru', 'sold', 0, 'Reliable hybrid hatchback, spacious boot, smooth drive. Recently sold — similar units available on order.');

INSERT INTO car_images (car_id, image_path, is_primary) VALUES
(1, 'assets/uploads/cars/demo-1.jpg', 1),
(2, 'assets/uploads/cars/demo-2.jpg', 1),
(3, 'assets/uploads/cars/demo-3.jpg', 1),
(4, 'assets/uploads/cars/demo-4.jpg', 1),
(5, 'assets/uploads/cars/demo-5.jpg', 1),
(6, 'assets/uploads/cars/demo-6.jpg', 1);

INSERT INTO inquiries (car_id, name, phone, email, message, status) VALUES
(1, 'James Mwangi', '0722000111', 'james@example.com', 'Is the Axio still available? Can I view it this weekend?', 'new'),
(4, 'Grace Wanjiru', '0733000222', 'grace@example.com', 'Interested in the Prado. What is the last price?', 'contacted');
