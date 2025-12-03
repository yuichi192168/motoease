# User Permissions System - Implementation Guide

## Overview
The User Permissions System allows administrators to grant granular access control to system users through a checkbox-based permissions interface. Permissions are stored as JSON in the `users.permissions` column and are enforced throughout the system.

## Features Implemented

### 1. Permissions Checklist in User Management Form
- **Location**: `admin/user/manage_user.php`
- **Features**:
  - Organized by category (User & Customer, Inventory & Products, Services, Sales & Orders, Documents, System)
  - Checkbox interface for easy selection
  - "Select All" and "Deselect All" buttons
  - Auto-populates based on role type selection
  - Shows existing permissions when editing a user

### 2. Available Permissions

#### User & Customer
- `user_management` - User Management
- `customer_management` - Customer Management
- `customer_accounts` - Customer Accounts

#### Inventory & Products
- `product_management` - Product Management
- `inventory_management` - Inventory Management
- `stock_management` - Stock Management

#### Services
- `service_requests` - Service Requests
- `mechanics` - Mechanics Management
- `promo_images` - Promo Images
- `customer_images` - Customer Images

#### Sales & Orders
- `order_management` - Order Management
- `invoice_management` - Invoice Management

#### Documents
- `orcr_documents` - OR/CR Documents

#### System
- `reports` - Reports
- `system_settings` - System Settings

### 3. Backend Implementation

#### Database Storage
- Permissions are stored as JSON in the `users.permissions` column (TEXT field)
- Format: `["permission1", "permission2", "permission3"]`

#### Helper Functions (`config.php`)
- `hasPermission($permission_key, $settings, $conn)` - Checks if user has a specific permission
- `getUserPermissions($settings, $conn)` - Returns array of user's permissions

#### Access Control Integration
- **Admin Pages** (`admin/index.php`): Checks permissions before allowing page access
- **Navigation Menu** (`admin/inc/navigation.php`): Menu items only show if user has permission
- **Role-based fallback**: If permissions not set, falls back to role-based access

### 4. Permission Enforcement

#### Page Access Control
The system checks permissions in multiple layers:
1. **Admin Index** (`admin/index.php`): Primary access control
2. **Navigation Menu**: Menu visibility based on permissions
3. **Individual Pages**: Can use `hasPermission()` function for additional checks

#### Permission Hierarchy
- **Admin Role**: Automatically has all permissions (bypasses permission checks)
- **Other Roles**: Must have explicit permission granted
- **No Permissions Set**: Falls back to role-based default access

### 5. User Experience Features

#### Auto-Selection Based on Role
When a role is selected, relevant permissions are automatically checked:
- **Admin**: All permissions selected
- **Inventory**: Product, Inventory, and Stock management permissions
- **Service Admin**: Service requests, promo/customer images, and mechanics permissions

#### Visual Organization
- Permissions grouped by category in cards
- Clear labels and icons
- Easy-to-use checkbox interface

## Usage

### For Administrators

1. **Creating a New User**:
   - Fill in basic user information
   - Select Staff Position (role)
   - Check desired permissions from the checklist
   - Click "Save"

2. **Editing User Permissions**:
   - Open user edit form
   - Modify permissions as needed
   - Permissions are preserved and can be updated independently of role

### For Developers

#### Checking Permissions in Code
```php
// Check if user has permission
if(hasPermission('inventory_management')){
    // User can access inventory management
}

// Get all user permissions
$permissions = getUserPermissions();
if(in_array('reports', $permissions)){
    // User has reports permission
}
```

#### Adding New Permissions
1. Add permission key to `$permissions_list` in `admin/user/manage_user.php`
2. Add to appropriate `$permission_groups` category
3. Add page mapping in `admin/index.php` `$page_to_permission` array
4. Add permission check in navigation menu if needed

## Database Schema

The `users` table already has a `permissions` column (TEXT):
```sql
`permissions` text DEFAULT NULL
```

No additional database changes are required.

## Security Notes

1. **Admin Bypass**: Admin role always has full access regardless of permissions
2. **Permission Validation**: All permission checks are server-side
3. **JSON Storage**: Permissions stored as JSON for flexibility
4. **Backward Compatibility**: System falls back to role-based access if permissions not set

## Testing Checklist

- [ ] Create new user with specific permissions
- [ ] Verify user can only access granted modules
- [ ] Verify navigation menu shows only allowed items
- [ ] Test permission updates on existing users
- [ ] Verify admin has full access regardless of permissions
- [ ] Test role-based auto-selection of permissions
- [ ] Verify permissions persist after user update

