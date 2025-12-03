-- =====================================================
-- Create New System Roles and Permissions
-- =====================================================
-- This script adds three new role types to the users table:
-- 1. system_admin - Full access to all user accounts
-- 2. desk_staff - Handles daily transactions (inventory/service)
-- 3. data_admin - Manages promos, reviews, announcements, content
-- =====================================================

-- Step 1: Update the role_type enum to include the new roles
-- Note: This preserves all existing roles and adds the new ones

ALTER TABLE `users` 
MODIFY COLUMN `role_type` ENUM(
    'admin',
    'branch_supervisor',
    'admin_assistant',
    'stock_admin',
    'service_admin',
    'mechanic',
    'inventory',
    'service_receptionist',
    'system_admin',
    'desk_staff',
    'data_admin'
) DEFAULT 'admin';

-- =====================================================
-- Role Descriptions and Default Permissions
-- =====================================================
-- 
-- SYSTEM ADMIN (system_admin):
-- - Full access to all user accounts (System Users and Customers)
-- - Can manage system users and customers
-- - Recommended permissions: user_management, customer_management
--
-- DESK/STAFF (desk_staff):
-- - Handles daily transactions
-- - Inventory staff: product_management, inventory_management, stock_management
-- - Reception staff: service_requests, mechanics
-- - Recommended permissions: Based on specific staff type (inventory or reception)
--
-- DATA ADMIN (data_admin):
-- - Manages promos, reviews, announcements, content
-- - Recommended permissions: promo_images, customer_images, reviews_management, 
--   announcements_management, content_management
--
-- =====================================================
-- Notes:
-- - Existing roles remain unchanged
-- - Permissions are still stored in JSON format in users.permissions column
-- - Role-based access control is enforced in admin/index.php
-- - Default permissions should be configured per role in admin/user/manage_user.php
-- =====================================================

