-- Add missing credit_application_completed column to client_list table
ALTER TABLE `client_list` ADD COLUMN `credit_application_completed` tinyint(1) DEFAULT 0 AFTER `vehicle_model`;


