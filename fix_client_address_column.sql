-- Simple fix for missing address column in client_list table
-- Run this SQL to add the missing address column

ALTER TABLE `client_list` 
ADD COLUMN IF NOT EXISTS `address` TEXT NOT NULL DEFAULT '' 
AFTER `contact`;

-- Update any existing NULL values
UPDATE `client_list` SET `address` = '' WHERE `address` IS NULL;
