-- ============================================
-- Complete Solution for Admin Activity Log Table
-- This script ensures all prerequisites are met before creating the table
-- ============================================

-- Step 1: Ensure users table exists and has PRIMARY KEY on id
-- ============================================
-- Check if users.id is a PRIMARY KEY, if not, add it
SET @has_primary_key = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'id' 
    AND INDEX_NAME = 'PRIMARY'
);

-- If no PRIMARY KEY exists, add it
SET @sql = IF(@has_primary_key = 0,
    'ALTER TABLE `users` ADD PRIMARY KEY (`id`)',
    'SELECT "PRIMARY KEY already exists on users.id" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Ensure users.id is AUTO_INCREMENT if it should be
-- ============================================
-- Check if id column has AUTO_INCREMENT
SET @has_auto_increment = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'id' 
    AND EXTRA LIKE '%auto_increment%'
);

-- Note: Only modify to AUTO_INCREMENT if it's not already set
-- Uncomment the following if you want to ensure AUTO_INCREMENT:
/*
SET @sql2 = IF(@has_auto_increment = 0,
    'ALTER TABLE `users` MODIFY `id` INT(50) NOT NULL AUTO_INCREMENT',
    'SELECT "AUTO_INCREMENT already set on users.id" AS message'
);

PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;
*/

-- Step 3: Drop the table if it exists (optional - remove if you want to keep existing data)
-- ============================================
-- Uncomment the following line if you want to drop and recreate:
-- DROP TABLE IF EXISTS `admin_activity_log`;

-- Step 4: Create the admin_activity_log table with correct data types
-- ============================================
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(50) NOT NULL COMMENT 'ID of admin performing the action',
  `role` VARCHAR(50) NOT NULL DEFAULT 'admin' COMMENT 'Role of the user (should always be admin)',
  `action` TEXT NOT NULL COMMENT 'Detailed action description',
  `module` VARCHAR(100) DEFAULT NULL COMMENT 'Module where action occurred (e.g., Orders, Invoices, Customer Accounts, Inventory)',
  `reference_id` INT(11) DEFAULT NULL COMMENT 'Optional ID for related record (invoice, order, user, etc.)',
  `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the action',
  PRIMARY KEY (`log_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role` (`role`),
  KEY `idx_module` (`module`),
  KEY `idx_reference_id` (`reference_id`),
  KEY `idx_timestamp` (`timestamp`),
  CONSTRAINT `fk_admin_activity_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores all admin actions for audit tracking';

-- Step 5: Verify the table was created successfully
-- ============================================
SELECT 
    'Table created successfully!' AS status,
    TABLE_NAME,
    ENGINE,
    TABLE_COLLATION
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'admin_activity_log';

-- Verify the foreign key constraint
SELECT 
    'Foreign key constraint verified!' AS status,
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'admin_activity_log'
AND CONSTRAINT_NAME = 'fk_admin_activity_log_user';

