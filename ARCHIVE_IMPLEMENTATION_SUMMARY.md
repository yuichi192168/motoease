# Archive Functionality Implementation Summary

## Overview
This document summarizes the implementation of archive functionality across the admin interface. Instead of permanently deleting records, the system now archives them, hiding them from active lists while preserving the data for potential restoration.

## Changes Made

### 1. Database Schema Updates
**File:** `add_archive_columns.sql`

Added `delete_flag` column (TINYINT(1), default 0) to the following tables:
- `order_list`
- `invoices`
- `service_requests`
- `appointments`
- `or_cr_documents`
- `stock_list`
- `promo_images`
- `customer_purchase_images`

**Note:** Some tables already had `delete_flag` columns (categories, product_list, service_list, brand_list, mechanics_list, client_list).

### 2. Backend Changes (Master.php)
**File:** `classes/Master.php`

#### Updated Delete Functions (Changed to Archive):
- `delete_order()` - Now archives orders instead of deleting
- `delete_invoice()` - Now archives invoices (simplified, no longer deletes related records)
- `delete_request()` - Now archives service requests
- `delete_appointment()` - Now archives appointments
- `delete_document()` - Now archives OR/CR documents
- `delete_stock()` - Now archives stock entries
- `delete_promo()` - Now archives promo images
- `delete_customer()` - Now archives customer purchase images

#### New Restore Functions Added:
- `restore_order()`
- `restore_invoice()`
- `restore_request()`
- `restore_appointment()`
- `restore_document()`
- `restore_stock()`
- `restore_promo()`
- `restore_customer()`

All restore functions set `delete_flag = 0` to restore archived records.

### 3. Frontend UI Updates

#### Changed "Delete" to "Archive" in:
- `admin/orders/index.php`
- `admin/orders/view_order.php`
- `admin/invoices/index.php`
- `admin/service_requests/index.php`
- `admin/service_management/service_requests.php`
- `admin/service_management/appointments.php`
- `admin/products/index.php`
- `admin/products/view_product.php`
- `admin/mechanics/index.php`
- `admin/promo_management/index.php`
- `admin/orcr_documents/index.php`

#### UI Changes:
- Changed button text from "Delete" to "Archive"
- Changed icon from `fa-trash` (red) to `fa-archive` (warning/yellow)
- Updated confirmation messages to mention archiving and restoration capability
- Updated success messages to say "archived" instead of "deleted"

### 4. Query Updates

Updated all list queries to filter out archived records by adding `WHERE delete_flag = 0` or `AND delete_flag = 0`:

- **Orders:** `admin/orders/index.php` - Added filter to main orders query and pending orders summary
- **Service Requests:** `admin/service_requests/index.php` - Added filter to service requests query
- **Appointments:** `admin/service_management/appointments.php` - Added filter to appointments query
- **OR/CR Documents:** `admin/orcr_documents/index.php` - Added filter to all document queries (counts and list)
- **Promo Images:** `admin/promo_management/index.php` - Added filter to promo images query
- **Customer Images:** `admin/promo_management/index.php` - Added filter to customer purchase images query

## How It Works

1. **Archiving:** When an admin clicks "Archive" on any record:
   - The record's `delete_flag` is set to `1`
   - The record is immediately hidden from active lists
   - All related data is preserved (no cascading deletes)
   - Success message confirms archiving

2. **Filtering:** All active list queries now include `WHERE delete_flag = 0` to exclude archived records

3. **Restoration:** Archived records can be restored using the restore functions (currently backend only - UI can be added later if needed)

## Benefits

1. **Data Preservation:** No data loss - all records are kept for audit and recovery
2. **Reversible Actions:** Mistakes can be corrected by restoring archived records
3. **Better UX:** Clear indication that records are archived, not permanently deleted
4. **Audit Trail:** Archived records remain in database for historical reference

## Next Steps (Optional Enhancements)

1. Add UI for viewing and restoring archived records
2. Add archive date/time tracking
3. Add "View Archived" filter/tab in admin interfaces
4. Add bulk archive/restore functionality
5. Update Invoice.php class to filter archived invoices in `get_all_invoices()` method

## Database Migration

To apply the database changes, run:
```sql
-- Run the SQL file
SOURCE add_archive_columns.sql;
```

Or execute the SQL commands directly in your database management tool.

