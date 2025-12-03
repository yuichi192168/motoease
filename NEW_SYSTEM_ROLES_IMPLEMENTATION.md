# New System Roles Implementation

## Overview
This document describes the implementation of three new system roles and their permissions in the BP SMS system.

## New Roles Added

### 1. System Admin (`system_admin`)
**Description:** Full access to all user accounts including System Users and Customers.

**Default Permissions:**
- `user_management` - Manage system users
- `customer_management` - Manage customer accounts

**Use Case:**
- User account administration
- Customer account management
- Access to both system users and customer data

### 2. Desk/Staff (`desk_staff`)
**Description:** Handles daily transactions. Inventory staff manage product stocks, while Reception staff manage service appointments.

**Default Permissions:**
- `product_management` - Manage products
- `inventory_management` - Inventory operations
- `stock_management` - Stock level management
- `service_requests` - Handle service requests
- `mechanics` - Manage mechanics
- `order_management` - Process orders

**Use Case:**
- Daily operational tasks
- Inventory management (for inventory staff)
- Service appointment handling (for reception staff)
- Order processing

### 3. Data Admin (`data_admin`)
**Description:** Manages promos, reviews, announcements, content and other data-driven modules.

**Default Permissions:**
- `promo_images` - Manage promotional images
- `customer_images` - Manage customer images
- `reviews_management` - Manage customer reviews
- `announcements_management` - Manage announcements
- `content_management` - Manage content

**Use Case:**
- Promotional content management
- Customer review moderation
- Announcement creation and management
- Content updates and maintenance

## Database Changes

### SQL Script
Run the SQL script to add the new roles to the database:

```sql
-- File: create_new_system_roles.sql
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
    'system_admin',      -- NEW
    'desk_staff',        -- NEW
    'data_admin'         -- NEW
) DEFAULT 'admin';
```

## New Permissions Added

Three new permission keys have been added to the system:

1. **`reviews_management`** - Reviews Management
   - Allows management of customer reviews
   - Maps to page: `reviews`

2. **`announcements_management`** - Announcements Management
   - Allows management of system announcements
   - Maps to page: `announcements`

3. **`content_management`** - Content Management
   - Allows management of content modules
   - Maps to page: `content`

## Files Modified

### 1. `create_new_system_roles.sql`
- SQL script to add new roles to the database enum

### 2. `admin/user/manage_user.php`
- Added new role options to the Staff Position dropdown
- Added new permission keys to the permissions list
- Added new "Content & Data" permission group
- Updated auto-selection logic for new roles

### 3. `admin/index.php`
- Added page-to-permission mappings for new content modules
- Added role-based access control logic for new roles
- System Admin: Access to user and customer management pages
- Desk/Staff: Access to inventory and service pages
- Data Admin: Access to content and data management pages

### 4. `admin/inc/navigation.php`
- Added role display name mapping for better readability
- Added "Content & Data" menu section for data admin
- Updated Images Management to be accessible by data admin
- Added menu items for reviews, announcements, and content

## Role-Based Access Control

### System Admin Access
- User Management (System Users)
- Customer Management
- Any page with `user_management` or `customer_management` permissions

### Desk/Staff Access
- Product Management
- Inventory Management
- Stock Management
- Service Requests
- Mechanics Management
- Order Management
- Any page with related permissions

### Data Admin Access
- Promo Images
- Customer Images
- Reviews Management
- Announcements Management
- Content Management
- Any page with content/data permissions

## Permission System

The system uses a hybrid approach:
1. **Permission-based access** (primary) - Users have specific permissions
2. **Role-based fallback** - If permissions are not set, role defaults apply

### Permission Storage
Permissions are stored as JSON in the `users.permissions` column:
```json
["user_management", "customer_management", "reviews_management"]
```

### Auto-Selection
When a role is selected in the user management form, relevant permissions are automatically checked:
- **System Admin**: User and customer management permissions
- **Desk/Staff**: Inventory, service, and order permissions
- **Data Admin**: Promo, reviews, announcements, and content permissions

## Usage Instructions

### For Administrators

1. **Run the SQL Script:**
   ```sql
   -- Execute create_new_system_roles.sql
   ```

2. **Create a New User:**
   - Go to Admin Panel → User Management → System Users
   - Click "Create New"
   - Select one of the new roles from "Staff Position" dropdown
   - Permissions will auto-select based on role
   - Adjust permissions as needed
   - Save

3. **Update Existing User:**
   - Edit user
   - Change "Staff Position" to new role
   - Permissions will auto-update
   - Save

### For Developers

#### Adding New Permissions
1. Add permission key to `$permissions_list` in `admin/user/manage_user.php`
2. Add to appropriate `$permission_groups` category
3. Add page mapping in `admin/index.php` `$page_to_permission` array
4. Add permission check in navigation menu if needed

#### Checking Permissions in Code
```php
// Check if user has permission
if(hasPermission('reviews_management')){
    // User can manage reviews
}

// Check role type
$role_type = $_settings->userdata('role_type');
if($role_type === 'data_admin'){
    // User is data admin
}
```

## Navigation Menu Changes

New menu sections added:
- **Content & Data** - Appears for Data Admin and users with content permissions
  - Reviews
  - Announcements
  - Content Management

Updated sections:
- **Images Management** - Now accessible by Data Admin role
- Role display names improved (e.g., "System Admin" instead of "system_admin")

## Testing Checklist

- [ ] Run SQL script successfully
- [ ] Create new user with System Admin role
- [ ] Verify System Admin can access user and customer management
- [ ] Create new user with Desk/Staff role
- [ ] Verify Desk/Staff can access inventory and service pages
- [ ] Create new user with Data Admin role
- [ ] Verify Data Admin can access content management pages
- [ ] Test permission auto-selection when changing roles
- [ ] Verify navigation menu shows correct items per role
- [ ] Test permission-based access control
- [ ] Verify existing roles still work correctly

## Notes

- Existing roles remain unchanged and functional
- New roles use the same permission system as existing roles
- Permissions can be customized per user regardless of role
- Role-based access is a fallback when permissions are not set
- Admin role always has full access regardless of permissions

## Future Enhancements

Consider implementing:
- Role hierarchy system
- Permission templates
- Role-based dashboard customization
- Audit logging for role/permission changes

