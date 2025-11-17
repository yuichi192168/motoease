-- Complete Diagnostic Script for Foreign Key Constraint Issue
-- Run this script to diagnose the exact issue with admin_activity_log table creation

-- ============================================
-- 1. Check database and table existence
-- ============================================
SELECT 
    TABLE_NAME,
    ENGINE,
    TABLE_COLLATION
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME IN ('users', 'admin_activity_log');

-- ============================================
-- 2. Check users table structure
-- ============================================
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    EXTRA,
    COLUMN_KEY
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users'
ORDER BY ORDINAL_POSITION;

-- ============================================
-- 3. Check if users.id is a PRIMARY KEY or has an index
-- ============================================
SELECT 
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE,
    SEQ_IN_INDEX
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users'
AND COLUMN_NAME = 'id';

-- ============================================
-- 4. Check existing foreign key constraints
-- ============================================
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE() 
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ============================================
-- 5. Compare data types between users.id and intended foreign key
-- ============================================
SELECT 
    'users.id' AS column_reference,
    DATA_TYPE,
    COLUMN_TYPE,
    CHARACTER_SET_NAME,
    COLLATION_NAME
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users'
AND COLUMN_NAME = 'id'

UNION ALL

SELECT 
    'admin_activity_log.user_id (intended)' AS column_reference,
    'INT' AS DATA_TYPE,
    'INT(50)' AS COLUMN_TYPE,
    NULL AS CHARACTER_SET_NAME,
    NULL AS COLLATION_NAME;

-- ============================================
-- 6. Check table engines and character sets
-- ============================================
SELECT 
    TABLE_NAME,
    ENGINE,
    TABLE_COLLATION
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'users';

-- ============================================
-- 7. Verify users table has PRIMARY KEY on id
-- ============================================
SHOW CREATE TABLE `users`;

