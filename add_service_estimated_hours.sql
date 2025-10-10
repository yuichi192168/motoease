-- Add estimated_hours column to service_list table
ALTER TABLE `service_list` 
ADD COLUMN `estimated_hours` DECIMAL(4,2) DEFAULT NULL COMMENT 'Estimated completion time in hours' 
AFTER `description`;

-- Update existing services with default estimated times (optional)
UPDATE `service_list` SET `estimated_hours` = 1.0 WHERE `service` LIKE '%Change Oil%';
UPDATE `service_list` SET `estimated_hours` = 2.0 WHERE `service` LIKE '%Checkup%' OR `service` LIKE '%Overall%';
UPDATE `service_list` SET `estimated_hours` = 1.5 WHERE `service` LIKE '%Tune up%' OR `service` LIKE '%Tune-up%';
UPDATE `service_list` SET `estimated_hours` = 1.5 WHERE `service` LIKE '%Brake%';
UPDATE `service_list` SET `estimated_hours` = 2.0 WHERE `service` LIKE '%Chain%' OR `service` LIKE '%Sprocket%';
UPDATE `service_list` SET `estimated_hours` = 1.0 WHERE `service` LIKE '%Battery%';
UPDATE `service_list` SET `estimated_hours` = 0.5 WHERE `service` LIKE '%Spark Plug%';



