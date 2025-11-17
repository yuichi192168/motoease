# Admin User Activity Logging Implementation

## Overview
This document describes the complete implementation of automatic admin activity logging for the BPSMS (Bike Parts Sales Management System). All significant admin actions are now automatically logged for accountability and audit tracking.

## Database Structure

### Table: `admin_activity_log`
Created via: `create_admin_activity_log_table.sql`

**Fields:**
- `log_id` (INT, Auto Increment) - Primary key
- `user_id` (INT) - ID of admin performing the action
- `role` (VARCHAR(50)) - Always stores 'admin' for admin actions
- `action` (TEXT) - Detailed action description with admin name
- `module` (VARCHAR(100)) - Module where action occurred (e.g., Orders, Invoices, Customer Accounts, Inventory)
- `reference_id` (INT, nullable) - Optional ID for related record (invoice, order, user, etc.)
- `timestamp` (DATETIME) - System timestamp of the action

**Indexes:**
- Primary key on `log_id`
- Indexes on `user_id`, `role`, `module`, `reference_id`, and `timestamp` for fast filtering

## ActivityLogger Class

**File:** `classes/ActivityLogger.php`

A helper class that provides automatic logging functionality with the following methods:

### Core Methods:
- `log($action, $module, $reference_id)` - Generic logging method
- `logLogin()` - Logs admin login
- `logLogout()` - Logs admin logout
- `logOrderApproval($order_id)` - Logs order approval
- `logOrderRejection($order_id, $reason)` - Logs order rejection with optional reason
- `logAccountBalanceUpdate($customer_id, $new_balance)` - Logs account balance updates
- `logOnsitePayment($customer_id, $amount)` - Logs onsite payment recording
- `logPaymentStatusChange($invoice_id, $status)` - Logs payment status changes
- `logArchive($record_type, $record_id)` - Logs archive actions
- `logORCRUpload($customer_id, $document_type)` - Logs OR/CR document uploads
- `logInvoiceCreation($invoice_id, $customer_id)` - Logs invoice generation
- `logReceiptCreation($receipt_id, $customer_id)` - Logs receipt creation
- `logStockUpdate($product_id, $details)` - Logs inventory/stock updates
- `logSystemSettingsUpdate($setting_key)` - Logs system settings modifications

## Integrated Actions

### 1. Authentication
**Files Modified:**
- `classes/Login.php`
  - Login: Logs when admin successfully logs in
  - Logout: Logs when admin logs out

### 2. Order Management
**Files Modified:**
- `classes/Master.php` - `update_order_status()`
  - Logs order approval (status = 1)
  - Logs order rejection (status = 5) with optional reason

### 3. Customer Account Management
**Files Modified:**
- `classes/Master.php` - `adjust_client_balance()`
  - Logs account balance updates with new balance amount
- `admin/customer_account_balances/add_payment.php`
  - Logs onsite payment recording with amount

### 4. Invoice & Receipt Management
**Files Modified:**
- `classes/Invoice.php` - `createInvoiceFromOrder()`
  - Logs invoice creation with invoice ID and customer ID
- `classes/Invoice.php` - `createReceipt()`
  - Logs receipt creation with receipt ID and customer ID

### 5. Inventory Management
**Files Modified:**
- `classes/Master.php` - `save_stock()`
  - Logs stock additions and updates with quantity changes
- `classes/Master.php` - `update_stock()`
  - Logs stock movements (IN/OUT/ADJUSTMENT) with details

### 6. Document Management
**Files Modified:**
- `classes/Master.php` - `upload_client_orcr()`
  - Logs OR/CR document uploads with document type

### 7. System Settings
**Files Modified:**
- `classes/SystemSettings.php` - `update_settings_info()`
  - Logs system settings modifications

## User Activity Log Display

**File:** `admin/user_log_history.php`

### Features:
1. **Display Admin Logs**: Shows all admin activity logs alongside staff and customer activities
2. **Filtering Options**:
   - User Type: Admin, Staff, Customer, or All
   - User: Filter by specific admin user
   - Activity Type: Admin Actions, Service Requests, Orders, etc.
   - Module: Orders, Invoices, Customer Accounts, Inventory, OR/CR Documents, System Settings, Authentication
   - Date Range: Filter by date from/to
3. **Visual Indicators**:
   - Admin actions have red border and light red background
   - Badge colors: Admin (red), Staff (blue), Customer (green)
   - Module badges for easy identification
4. **Export Functionality**: Export filtered logs to CSV with module information

### Sample Log Output Format:
```
[2025-11-15 14:22:31] Admin John Santos approved Order #2045.
[2025-11-15 14:25:10] Admin John Santos recorded an onsite payment of ₱3,000.00 for Customer #50013.
[2025-11-15 14:30:48] Admin John Santos uploaded OR/CR document for Customer #50013.
[2025-11-15 14:33:19] Admin John Santos generated Invoice #INV-2025-0123 for Customer #50013.
[2025-11-15 14:35:42] Admin John Santos updated stock for Product #123. Added 50 units. New total: 150.
[2025-11-15 14:40:15] Admin John Santos modified system settings.
```

## Installation Steps

1. **Create Database Table:**
   ```sql
   SOURCE create_admin_activity_log_table.sql;
   ```
   Or run the SQL file in your database management tool.

2. **Verify Files:**
   - `classes/ActivityLogger.php` exists
   - All modified files are in place

3. **Test Logging:**
   - Log in as admin (should create login log)
   - Perform various admin actions
   - Check `admin/user_log_history.php` to see logs

## System Behavior

### Automatic Logging
- Logging happens automatically when admin actions are performed
- No manual intervention required
- Logs are created immediately after successful operations

### Log Immutability
- Logs cannot be edited or deleted (only archived by superadmin in future)
- All logs are timestamped with system timezone
- Logs include full context (admin name, action details, reference IDs)

### Performance
- Indexed fields ensure fast filtering and searching
- Logs are limited to last 30 days by default in display (configurable)
- Database indexes optimize query performance

## Future Enhancements

Potential improvements:
1. Archive functionality for old logs (superadmin only)
2. Log retention policies
3. Advanced search with full-text search
4. Email notifications for critical actions
5. Dashboard widget showing recent admin activities
6. Export to PDF functionality

## Notes

- All admin actions are logged with the format: "Admin {name} {action description}"
- Module names are standardized for consistent filtering
- Reference IDs link logs to related records (orders, invoices, customers, etc.)
- The system automatically retrieves admin names from the users table
- Logs are displayed in chronological order (newest first)

