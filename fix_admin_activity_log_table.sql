-- ============================================
-- Fix admin_activity_log Table Structure
-- This script ensures the table structure matches the PHP code
-- The code expects 'id' as the primary key column (not 'log_id')
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Step 1: Check if table exists with 'log_id' column and rename it to 'id'
-- This handles the case where the table was created with 'log_id' as primary key
SET @log_id_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND COLUMN_NAME = 'log_id'
);

-- If log_id exists, rename it to id
SET @sql = IF(@log_id_exists > 0,
    'ALTER TABLE `admin_activity_log` CHANGE COLUMN `log_id` `id` INT(11) NOT NULL AUTO_INCREMENT',
    'SELECT "Column log_id does not exist, skipping rename" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Create table if it doesn't exist with correct structure
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL COMMENT 'Reference to users.id - ID of admin performing the action',
  `role` VARCHAR(50) NOT NULL DEFAULT 'admin' COMMENT 'Role of the user (should always be admin)',
  `action` TEXT NOT NULL COMMENT 'Detailed action description',
  `module` VARCHAR(100) DEFAULT NULL COMMENT 'Module where action occurred (e.g., Orders, Invoices, Customer Accounts, Inventory)',
  `reference_id` INT(11) DEFAULT NULL COMMENT 'Optional ID for related record (invoice, order, user, etc.)',
  `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the action',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores all admin actions for audit tracking';

-- Step 3: Ensure primary key is AUTO_INCREMENT
ALTER TABLE `admin_activity_log` 
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;

-- Step 4: Add missing columns if they don't exist
-- Check and add user_id
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND COLUMN_NAME = 'user_id'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD COLUMN `user_id` INT(11) NOT NULL COMMENT "Reference to users.id" AFTER `id`',
    'SELECT "user_id exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add role
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND COLUMN_NAME = 'role'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD COLUMN `role` VARCHAR(50) NOT NULL DEFAULT "admin" COMMENT "Role of the user" AFTER `user_id`',
    'SELECT "role exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add action
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND COLUMN_NAME = 'action'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD COLUMN `action` TEXT NOT NULL COMMENT "Detailed action description" AFTER `role`',
    'SELECT "action exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add module
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND COLUMN_NAME = 'module'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD COLUMN `module` VARCHAR(100) DEFAULT NULL COMMENT "Module where action occurred" AFTER `action`',
    'SELECT "module exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add reference_id
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND COLUMN_NAME = 'reference_id'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD COLUMN `reference_id` INT(11) DEFAULT NULL COMMENT "Optional ID for related record" AFTER `module`',
    'SELECT "reference_id exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add timestamp
SET @col_exists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND COLUMN_NAME = 'timestamp'
);
SET @sql = IF(@col_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD COLUMN `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "Timestamp of the action" AFTER `reference_id`',
    'SELECT "timestamp exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Add indexes (check if they exist first, then add if missing)
-- Index on user_id
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND INDEX_NAME = 'idx_user_id'
);
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD INDEX `idx_user_id` (`user_id`)',
    'SELECT "idx_user_id exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index on role
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND INDEX_NAME = 'idx_role'
);
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD INDEX `idx_role` (`role`)',
    'SELECT "idx_role exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index on module
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND INDEX_NAME = 'idx_module'
);
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD INDEX `idx_module` (`module`)',
    'SELECT "idx_module exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index on reference_id
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND INDEX_NAME = 'idx_reference_id'
);
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD INDEX `idx_reference_id` (`reference_id`)',
    'SELECT "idx_reference_id exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index on timestamp
SET @idx_exists = (
    SELECT COUNT(*) 
    FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_activity_log' 
    AND INDEX_NAME = 'idx_timestamp'
);
SET @sql = IF(@idx_exists = 0,
    'ALTER TABLE `admin_activity_log` ADD INDEX `idx_timestamp` (`timestamp`)',
    'SELECT "idx_timestamp exists" AS message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

-- Step 6: Verify the table structure
SELECT 
    '=== TABLE STRUCTURE ===' AS info;
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_KEY,
    COLUMN_DEFAULT,
    EXTRA
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'admin_activity_log'
ORDER BY ORDINAL_POSITION;

-- Step 7: Show indexes
SELECT 
    '=== INDEXES ===' AS info;
SELECT 
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'admin_activity_log'
ORDER BY INDEX_NAME, SEQ_IN_INDEX;

-- Step 8: Final verification message
SELECT 
    '=== VERIFICATION COMPLETE ===' AS status,
    'The admin_activity_log table is now configured with "id" as the primary key.' AS message,
    'This matches the PHP code which uses: aal.id as log_id' AS note;
