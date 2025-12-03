-- Add amount_to_pay column to service_requests table
ALTER TABLE `service_requests` 
ADD COLUMN `amount_to_pay` decimal(10,2) DEFAULT NULL AFTER `status`;



