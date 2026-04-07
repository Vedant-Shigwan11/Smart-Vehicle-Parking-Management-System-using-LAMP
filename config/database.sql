CREATE DATABASE IF NOT EXISTS smart_parking;
USE smart_parking;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS parking_slots (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slot_number VARCHAR(20) NOT NULL UNIQUE,
    status ENUM('available', 'maintenance') NOT NULL DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    slot_id INT UNSIGNED NOT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('active', 'completed', 'cancelled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_bookings_slot FOREIGN KEY (slot_id) REFERENCES parking_slots(id) ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role)
SELECT
    'Administrator',
    'admin@smartparking.local',
    '$2y$10$PlQEybybizf7pwcfJUPW2OdXXUlrj96LWqA5awpwBCjTb8rusZ386',
    'admin'
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@smartparking.local'
);

INSERT INTO parking_slots (slot_number, status)
SELECT 'A-101', 'available'
WHERE NOT EXISTS (SELECT 1 FROM parking_slots WHERE slot_number = 'A-101');

INSERT INTO parking_slots (slot_number, status)
SELECT 'A-102', 'available'
WHERE NOT EXISTS (SELECT 1 FROM parking_slots WHERE slot_number = 'A-102');

INSERT INTO parking_slots (slot_number, status)
SELECT 'B-201', 'maintenance'
WHERE NOT EXISTS (SELECT 1 FROM parking_slots WHERE slot_number = 'B-201');
