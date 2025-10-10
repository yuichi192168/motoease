-- Add Business Services to BPSMS
-- This script adds comprehensive business services with pricing and estimated hours

-- First, add estimated_hours column if it doesn't exist
ALTER TABLE `service_list` 
ADD COLUMN IF NOT EXISTS `estimated_hours` DECIMAL(4,2) DEFAULT NULL COMMENT 'Estimated completion time in hours' 
AFTER `description`;

-- Add service_amount column for pricing
ALTER TABLE `service_list` 
ADD COLUMN IF NOT EXISTS `service_amount` DECIMAL(10,2) DEFAULT NULL COMMENT 'Service price in Philippine Pesos' 
AFTER `estimated_hours`;

-- Add service_type column for categorization
ALTER TABLE `service_list` 
ADD COLUMN IF NOT EXISTS `service_type` VARCHAR(100) DEFAULT NULL COMMENT 'Type of service (e.g., TUNE UP, ADJUSTMENT)' 
AFTER `service_amount`;

-- Add min_hours and max_hours columns for time range
ALTER TABLE `service_list` 
ADD COLUMN IF NOT EXISTS `min_hours` DECIMAL(4,2) DEFAULT NULL COMMENT 'Minimum estimated hours' 
AFTER `service_type`;

ALTER TABLE `service_list` 
ADD COLUMN IF NOT EXISTS `max_hours` DECIMAL(4,2) DEFAULT NULL COMMENT 'Maximum estimated hours' 
AFTER `min_hours`;

-- Insert Business Services
INSERT INTO `service_list` (`service`, `description`, `service_type`, `service_amount`, `min_hours`, `max_hours`, `estimated_hours`, `status`, `delete_flag`, `date_created`) VALUES

-- TUNE UP Services
('Minor Tune Up', 'Complete minor tune-up service including basic adjustments and checks', 'TUNE UP', 455.00, 1.30, NULL, 1.30, 1, 0, NOW()),
('Major Tune Up', 'Comprehensive major tune-up service with detailed engine adjustments', 'TUNE UP', 950.00, 3.00, NULL, 3.00, 1, 0, NOW()),
('Minor Tune Up (Change Oil & Tune Up)', 'Minor tune-up combined with oil change service', 'CHANGE OIL & TUNE UP', 455.00, 1.30, NULL, 1.30, 1, 0, NOW()),

-- ADJUSTMENT Services
('Top Overhaul', 'Complete top engine overhaul service', 'ADJUSTMENT', 1505.00, 5.00, NULL, 5.00, 1, 0, NOW()),
('Engine Overhaul', 'Full engine overhaul service', 'ADJUSTMENT', 1505.00, 5.00, NULL, 5.00, 1, 0, NOW()),
('Carburetor Idle Adjustment', 'Carburetor idle speed adjustment and tuning', 'ADJUSTMENT', 50.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Spark Plug Adjustment', 'Spark plug gap adjustment and replacement', 'ADJUSTMENT', 50.00, 0.00, NULL, 0.00, 1, 0, NOW()),
('Shaft Drive Adjustment', 'Shaft drive system adjustment and alignment', 'ADJUSTMENT', 50.00, 0.00, NULL, 0.00, 1, 0, NOW()),

-- REPLACE / REPAIR / CLEAN PARTS Services
('Sprocket (Rear) Replacement', 'Rear sprocket replacement and installation', 'REPLACE / REPAIR / CLEAN PARTS', 50.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Fork Assembly (Rear) Replacement', 'Rear fork assembly replacement and installation', 'REPLACE / REPAIR / CLEAN PARTS', 120.00, 0.33, NULL, 0.33, 1, 0, NOW()),
('Oil Pump Replacement', 'Oil pump replacement and installation', 'REPLACE / REPAIR / CLEAN PARTS', 45.00, 0.20, NULL, 0.20, 1, 0, NOW()),
('Carburetor Replacement', 'Carburetor replacement and installation', 'REPLACE / REPAIR / CLEAN PARTS', 305.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Starter Motor Replacement', 'Starter motor replacement and installation', 'REPLACE / REPAIR / CLEAN PARTS', 150.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Drive Chain / Sprocket Replacement', 'Drive chain and sprocket replacement', 'REPLACE / REPAIR / CLEAN PARTS', 265.00, 0.80, NULL, 0.80, 1, 0, NOW()),
('Oil Seal (Crankshaft Bottom) Replacement', 'Crankshaft bottom oil seal replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Starter Idle Item Replacement', 'Starter idle component replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Ignition Switch Replacement', 'Ignition switch replacement and installation', 'REPLACE / REPAIR / CLEAN PARTS', 280.00, 0.83, NULL, 0.83, 1, 0, NOW()),
('Brake Rear Panel Replacement', 'Rear brake panel replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Seal Oil Pump Cleaner Replacement', 'Oil pump seal cleaner replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Valve (IN/EX) Replacement', 'Intake and exhaust valve replacement', 'REPLACE / REPAIR / CLEAN PARTS', 950.00, 3.00, NULL, 3.00, 1, 0, NOW()),
('Gasket (Cylinder) Replacement', 'Cylinder gasket replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Cover Pulsate Right Replacement', 'Right pulsate cover replacement', 'REPLACE / REPAIR / CLEAN PARTS', 155.00, 1.00, NULL, 1.00, 1, 0, NOW()),
('Bearing Axle Shaft Replacement', 'Axle shaft bearing replacement', 'REPLACE / REPAIR / CLEAN PARTS', 650.00, 1.50, NULL, 1.50, 1, 0, NOW()),
('Arm Brake (Stand Side) Replacement', 'Stand side brake arm replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Gear Starter Idle Replacement', 'Starter idle gear replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Shaft Idle Replacement', 'Idle shaft replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Disc Clutch Friction Replacement', 'Clutch friction disc replacement', 'REPLACE / REPAIR / CLEAN PARTS', 650.00, 1.50, NULL, 1.50, 1, 0, NOW()),
('Cover Gearcase Left Rear Replacement', 'Left rear gearcase cover replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.50, NULL, 0.50, 1, 0, NOW()),
('Cover Crankcase Replacement', 'Crankcase cover replacement', 'REPLACE / REPAIR / CLEAN PARTS', 650.00, 1.50, NULL, 1.50, 1, 0, NOW()),
('Carrier Luggage Replacement', 'Luggage carrier replacement', 'REPLACE / REPAIR / CLEAN PARTS', 650.00, 1.50, NULL, 1.50, 1, 0, NOW()),
('Bearing Idle Shaft Replacement', 'Idle shaft bearing replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Spring Starter Base Replacement', 'Starter base spring replacement', 'REPLACE / REPAIR / CLEAN PARTS', 45.00, 0.50, NULL, 0.50, 1, 0, NOW()),
('Switch Gear Change Replacement', 'Gear change switch replacement', 'REPLACE / REPAIR / CLEAN PARTS', 650.00, 1.50, NULL, 1.50, 1, 0, NOW()),
('Cylinder Front Brake Replacement', 'Front brake cylinder replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Cylinder Front Brake Master Replacement', 'Front brake master cylinder replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Bulb Headlight Replacement', 'Headlight bulb replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Bulb Taillight Replacement', 'Taillight bulb replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Spring Decomp Cam Replacement', 'Decompression cam spring replacement', 'REPLACE / REPAIR / CLEAN PARTS', 950.00, 3.00, NULL, 3.00, 1, 0, NOW()),
('Case Meter Lower Replacement', 'Lower meter case replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Cable Throttle Replacement', 'Throttle cable replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Cable Clutch Replacement', 'Clutch cable replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Switch Starter Replacement', 'Starter switch replacement', 'REPLACE / REPAIR / CLEAN PARTS', 180.00, 0.50, NULL, 0.50, 1, 0, NOW()),
('Cap Spark Plug Replacement', 'Spark plug cap replacement', 'REPLACE / REPAIR / CLEAN PARTS', 45.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Switch Clutch Replacement', 'Clutch switch replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Base Stator Replacement', 'Stator base replacement', 'REPLACE / REPAIR / CLEAN PARTS', 200.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Bracket Handle Lever Left Replacement', 'Left handle lever bracket replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Solenoid Assembly Replacement', 'Solenoid assembly replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 1.00, NULL, 1.00, 1, 0, NOW()),
('Flasher Replacement', 'Flasher unit replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Modified Steering Handle Replacement', 'Modified steering handle replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Brake Rear Drum Replacement', 'Rear brake drum replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Key Set Replacement', 'Key set replacement and programming', 'REPLACE / REPAIR / CLEAN PARTS', 0.00, 0.00, NULL, 0.00, 1, 0, NOW()),
('Switch Ignition and Lock Replacement', 'Ignition switch and lock replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Switch Combination and Lock Replacement', 'Combination switch and lock replacement', 'REPLACE / REPAIR / CLEAN PARTS', 300.00, 0.75, NULL, 0.75, 1, 0, NOW()),
('Bridge Fork Top Replacement', 'Top fork bridge replacement', 'REPLACE / REPAIR / CLEAN PARTS', 100.00, 0.25, NULL, 0.25, 1, 0, NOW()),

-- LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR Services
('General Repair', 'General motorcycle repair and troubleshooting', 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', 650.00, 1.00, NULL, 1.00, 1, 0, NOW()),
('Front/Rear Brakes Repair', 'Front and rear brake system repair', 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', 650.00, 1.00, NULL, 1.00, 1, 0, NOW()),
('Electrical Wiring Repair', 'Electrical wiring system repair and troubleshooting', 'LIGHT DIAGNOSING TROUBLESHOOTING & REPAIR', 650.00, 1.00, NULL, 1.00, 1, 0, NOW()),

-- LUBRICATE Services
('Electrical Wiring Lubrication', 'Electrical wiring lubrication service', 'LUBRICATE', 650.00, 1.00, NULL, 1.00, 1, 0, NOW()),
('Cable Throttle Lubrication', 'Throttle cable lubrication service', 'LUBRICATE', 105.00, 0.25, NULL, 0.25, 1, 0, NOW()),
('Carburetor Idle Lubrication', 'Carburetor idle lubrication service', 'LUBRICATE', 205.00, 1.00, NULL, 1.00, 1, 0, NOW()),

-- TOP OVERHAUL Services
('Cock Assembly (Fuel) Overhaul', 'Fuel cock assembly top overhaul', 'TOP OVERHAUL', 130.00, 0.33, NULL, 0.33, 1, 0, NOW()),
('Arm Valve Exhaust Overhaul', 'Exhaust valve arm top overhaul', 'TOP OVERHAUL', 1105.00, 2.00, NULL, 2.00, 1, 0, NOW()),
('Sprocket (Cam Chain) Overhaul', 'Cam chain sprocket top overhaul', 'TOP OVERHAUL', 1200.00, 3.00, NULL, 3.00, 1, 0, NOW()),
('Sprocket (Cam) Overhaul', 'Cam sprocket top overhaul', 'TOP OVERHAUL', 950.00, 3.00, NULL, 3.00, 1, 0, NOW()),
('Cylinder Overhaul', 'Cylinder top overhaul service', 'TOP OVERHAUL', 1200.00, 3.00, NULL, 3.00, 1, 0, NOW()),
('Valve Spring and/or Stem Seal Overhaul', 'Valve spring and stem seal top overhaul', 'TOP OVERHAUL', 1200.00, 3.00, NULL, 3.00, 1, 0, NOW()),

-- Tensioner Services
('Tensioner (Cam Chain) Replacement', 'Cam chain tensioner replacement', 'REPLACE / REPAIR / CLEAN PARTS', 1200.00, 3.00, NULL, 3.00, 1, 0, NOW());

-- Update existing services with estimated hours if they don't have them
UPDATE `service_list` SET `estimated_hours` = 1.0 WHERE `service` LIKE '%Change Oil%' AND `estimated_hours` IS NULL;
UPDATE `service_list` SET `estimated_hours` = 2.0 WHERE `service` LIKE '%Checkup%' OR `service` LIKE '%Overall%' AND `estimated_hours` IS NULL;
UPDATE `service_list` SET `estimated_hours` = 1.5 WHERE `service` LIKE '%Tune up%' OR `service` LIKE '%Tune-up%' AND `estimated_hours` IS NULL;
UPDATE `service_list` SET `estimated_hours` = 1.5 WHERE `service` LIKE '%Brake%' AND `estimated_hours` IS NULL;
UPDATE `service_list` SET `estimated_hours` = 2.0 WHERE `service` LIKE '%Chain%' OR `service` LIKE '%Sprocket%' AND `estimated_hours` IS NULL;
UPDATE `service_list` SET `estimated_hours` = 1.0 WHERE `service` LIKE '%Battery%' AND `estimated_hours` IS NULL;
UPDATE `service_list` SET `estimated_hours` = 0.5 WHERE `service` LIKE '%Spark Plug%' AND `estimated_hours` IS NULL;

