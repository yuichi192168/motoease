-- Add missing columns to service_requests table
ALTER TABLE `service_requests` 
ADD COLUMN `vehicle_type` varchar(100) DEFAULT NULL AFTER `client_id`,
ADD COLUMN `vehicle_model` varchar(100) DEFAULT NULL AFTER `vehicle_registration_number`;
