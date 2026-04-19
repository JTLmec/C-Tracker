CREATE DATABASE IF NOT EXISTS carbon_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE carbon_tracker;

DROP TABLE IF EXISTS activities;
DROP TABLE IF EXISTS activity_types;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE activity_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(40) NOT NULL,
    name VARCHAR(100) NOT NULL,
    unit VARCHAR(40) NOT NULL,
    emission_factor DECIMAL(10,4) NOT NULL,
    recommendation TEXT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE activities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    activity_type_id INT UNSIGNED NOT NULL,
    activity_date DATE NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    emission_factor DECIMAL(10,4) NOT NULL,
    carbon_kg DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activities_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_activities_type
        FOREIGN KEY (activity_type_id) REFERENCES activity_types(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_activities_user_date ON activities(user_id, activity_date);
CREATE INDEX idx_activity_types_category ON activity_types(category);

INSERT INTO activity_types (category, name, unit, emission_factor, recommendation) VALUES
('travel', 'Car travel', 'km', 0.1920, 'Try carpooling, public transportation, biking, or combining trips to reduce travel emissions.'),
('travel', 'Bus ride', 'km', 0.0890, 'Public transport is usually better than solo driving. Plan routes ahead to make it easier to use.'),
('travel', 'Motorcycle ride', 'km', 0.1030, 'Avoid unnecessary short rides and keep the vehicle maintained for better fuel efficiency.'),
('electricity', 'Electricity use', 'kWh', 0.7100, 'Turn off unused appliances, use LED bulbs, and unplug chargers when they are not needed.'),
('food', 'Meat-based meal', 'meal', 3.3000, 'Replace some meat-based meals with vegetables, grains, or plant-based protein options.'),
('food', 'Plant-based meal', 'meal', 0.9000, 'Keep choosing lower-impact meals and reduce food waste by planning portions.'),
('waste', 'Mixed household waste', 'kg', 0.5700, 'Separate recyclables, reuse containers, and avoid single-use items when possible.'),
('water', 'Water use', 'liter', 0.0003, 'Fix leaks, shorten showers, and turn off taps while washing or brushing teeth.');
