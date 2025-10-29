-- Add address column to client_list table if it doesn't exist
-- This fixes the "Unknown column 'address' in 'field list'" error

-- Check if the address column exists, if not add it
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'client_list' 
     AND COLUMN_NAME = 'address') = 0,
    'ALTER TABLE `client_list` ADD COLUMN `address` TEXT NOT NULL DEFAULT "" AFTER `contact`',
    'SELECT "Address column already exists" as message'
));

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing records to have empty address if they are NULL
UPDATE `client_list` SET `address` = '' WHERE `address` IS NULL;
