-- Database Alignment Fixes for Appointments and Service Requests
-- Run this script to fix alignment issues between client and admin interfaces

-- 1. Fix appointments table auto-increment issue
-- First, let's see if there are any appointments with id=0
SELECT COUNT(*) as zero_ids FROM appointments WHERE id = 0;

-- Fix the auto-increment issue by updating zero IDs
-- This is a temporary fix - in production, you'd want to be more careful
SET @row_number = 0;
UPDATE appointments 
SET id = (@row_number:=@row_number+1) + (SELECT COALESCE(MAX(id), 0) FROM appointments WHERE id > 0)
WHERE id = 0;

-- Ensure auto-increment is properly set
ALTER TABLE `appointments` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- 2. Add missing status option to appointments
ALTER TABLE `appointments` 
MODIFY `status` enum('pending','confirmed','in_progress','cancelled','completed') 
NOT NULL DEFAULT 'pending';

-- 3. Add missing date_updated field to service_requests
ALTER TABLE `service_requests` 
ADD `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp();

-- 4. Standardize mechanic_id data types
ALTER TABLE `appointments` MODIFY `mechanic_id` int(30) DEFAULT NULL;
ALTER TABLE `service_requests` MODIFY `mechanic_id` int(30) DEFAULT NULL;

-- 5. Add indexes for better performance
ALTER TABLE `appointments` ADD INDEX `idx_client_id` (`client_id`);
ALTER TABLE `appointments` ADD INDEX `idx_status` (`status`);
ALTER TABLE `appointments` ADD INDEX `idx_appointment_date` (`appointment_date`);

ALTER TABLE `service_requests` ADD INDEX `idx_client_id` (`client_id`);
ALTER TABLE `service_requests` ADD INDEX `idx_status` (`status`);
ALTER TABLE `service_requests` ADD INDEX `idx_date_created` (`date_created`);

-- 6. Verify the fixes
SELECT 'Appointments table structure:' as info;
DESCRIBE appointments;

SELECT 'Service Requests table structure:' as info;
DESCRIBE service_requests;

-- 7. Check for any remaining zero IDs
SELECT 'Remaining zero IDs in appointments:' as info;
SELECT COUNT(*) as zero_ids FROM appointments WHERE id = 0;

-- 8. Show sample data to verify alignment
SELECT 'Sample appointments data:' as info;
SELECT id, client_id, status, appointment_date, appointment_time, date_created 
FROM appointments 
ORDER BY date_created DESC 
LIMIT 5;

SELECT 'Sample service requests data:' as info;
SELECT id, client_id, status, date_created, date_updated 
FROM service_requests 
ORDER BY date_created DESC 
LIMIT 5;

