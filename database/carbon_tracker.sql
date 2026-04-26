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
('travel', 'Electric car travel', 'km', 0.0436, 'Charge during off-peak hours and use renewable energy sources to further reduce your EV footprint.'),
('travel', 'Train / rail', 'km', 0.0045, 'Rail is one of the lowest-carbon transport options. Use it whenever available for medium distances.'),
('travel', 'Subway / metro', 'km', 0.0060, 'Mass transit systems are among the most efficient urban transport options. Consider a transit pass for daily commuting.'),
('travel', 'Domestic flight (economy)', 'km', 0.2000, 'For short trips, consider overland alternatives. If you must fly, economy class has about a quarter the footprint of business class.'),
('travel', 'Short-haul flight (economy)', 'km', 0.1860, 'Fly direct when possible — takeoff and landing are the most fuel-intensive parts of a flight.'),
('travel', 'Long-haul flight (economy)', 'km', 0.1480, 'Offset unavoidable long-haul flights and choose economy class to minimize your share of aircraft emissions.'),
('travel', 'Ferry (foot passenger)', 'km', 0.0187, 'Ferries produce far less CO2 than flying the same route. Foot passengers have a much smaller share than car passengers.'),
('travel', 'Jeepney / shared minibus', 'km', 0.0650, 'Shared rides significantly reduce per-passenger emissions. Fill up seats when driving your own vehicle too.'),

('electricity', 'Electricity use', 'kWh', 0.7100, 'Turn off unused appliances, use LED bulbs, and unplug chargers when they are not needed.'),
('electricity', 'LPG / cooking gas use', 'liter', 1.5100, 'Switch to induction cooking for zero direct emissions at the point of use — and it is faster too.'),
('electricity', 'Diesel fuel use', 'liter', 2.6800, 'Combine errands into single trips and keep engines well-maintained to reduce diesel consumption.'),
('electricity', 'Gasoline / petrol use', 'liter', 2.3100, 'Reduce cold-start trips, maintain tyre pressure, and avoid aggressive acceleration.'),
('electricity', 'Natural gas use', 'kWh', 0.2020, 'Insulate your home well so you need less energy for heating and cooking.'),
('electricity', 'Charcoal use', 'kg', 4.3500, 'Charcoal has a very high emission factor due to production inefficiency. Consider switching to LPG or induction for cooking.'),

('food', 'Meat-based meal', 'meal', 3.3000, 'Replace some meat-based meals with vegetables, grains, or plant-based protein options.'),
('food', 'Plant-based meal', 'meal', 0.9000, 'Keep choosing lower-impact meals and reduce food waste by planning portions.'),
('food', 'Beef', 'kg', 60.0000, 'Beef has the largest carbon footprint of any food. Even small reductions in beef consumption make a meaningful impact.'),
('food', 'Lamb / mutton', 'kg', 24.0000, 'Like beef, lamb is a high-emission food due to methane from ruminant digestion. Reduce frequency or portion size.'),
('food', 'Pork', 'kg', 7.6100, 'Pork has a much lower footprint than beef or lamb. Still, plant proteins like lentils are far more climate-friendly.'),
('food', 'Chicken', 'kg', 6.9000, 'Poultry is the lowest-emission meat. Complement with plant-based proteins for a lower-carbon diet.'),
('food', 'Fish (farmed)', 'kg', 5.1500, 'Farmed fish varies widely — shellfish like mussels are very low-carbon; salmon and shrimp are higher.'),
('food', 'Eggs', 'kg', 4.6700, 'Eggs are among the lowest-emission animal products. Good protein with a manageable footprint.'),
('food', 'Cheese', 'kg', 13.5000, 'Cheese has a surprisingly high footprint due to the large amount of milk required. Use it in smaller amounts.'),
('food', 'Milk', 'liter', 2.7900, 'Dairy alternatives like oat milk produce about 70–80% fewer emissions per litre.'),
('food', 'Rice', 'kg', 2.7000, 'Flooded rice paddies produce methane. Pair rice with low-emission legumes and vegetables.'),
('food', 'Bread / wheat', 'kg', 1.4000, 'Whole grain and locally milled bread has a relatively low footprint. Avoid food waste — bake or buy only what you need.'),
('food', 'Tofu / soy products', 'kg', 2.0000, 'Tofu is an excellent low-emission protein alternative to meat.'),
('food', 'Vegetables (average)', 'kg', 0.3700, 'Fresh vegetables are among the most climate-friendly foods you can eat.'),
('food', 'Fruits (average)', 'kg', 0.4300, 'Choose locally and seasonally grown fruit to keep transport and storage emissions low.'),
('food', 'Legumes / lentils', 'kg', 0.9000, 'Legumes fix nitrogen from the air, reducing fertilizer needs. One of the best protein sources for the planet.'),

('waste', 'Mixed household waste', 'kg', 0.5700, 'Separate recyclables, reuse containers, and avoid single-use items when possible.'),
('waste', 'Food waste (landfilled)', 'kg', 0.9500, 'Composting or feeding food scraps to animals avoids the methane from landfill decomposition.'),
('waste', 'Paper / cardboard (landfilled)', 'kg', 0.9100, 'Recycle paper and cardboard — recycling emits far less than landfilling.'),
('waste', 'Plastic waste (landfilled)', 'kg', 0.0100, 'While plastic landfilling has low direct emissions, production is highly carbon-intensive. Avoid single-use plastics.'),
('waste', 'Glass (landfilled)', 'kg', 0.0200, 'Glass is infinitely recyclable. Always put glass in the recycling bin.'),
('waste', 'E-waste / electronics', 'kg', 0.0230, 'Extend device lifespan and recycle through certified e-waste programs to recover valuable materials safely.'),

('water', 'Water use', 'liter', 0.0003, 'Fix leaks, shorten showers, and turn off taps while washing or brushing teeth.'),
('water', 'Hot water use (heated electrically)', 'liter', 0.0720, 'Install a solar water heater or heat pump water heater to drastically cut the footprint of your hot water.');
