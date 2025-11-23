# Receipt Creation Fix Summary

## Issues Fixed

### 1. Missing `archive_flag` Column
**Problem**: The `receipts` table has an `archive_flag` column that is NOT NULL, but the receipt creation code was not including it in the INSERT statement, causing SQL errors.

**Solution**: 
- Added `archive_flag` field to the receipt creation with default value of `0`
- Updated the SQL insert statement to explicitly include this field

### 2. Attempted UPDATE on VIEW
**Problem**: The code was trying to UPDATE `invoice_financials`, which is a VIEW, not a table. Views cannot be updated directly.

**Solution**:
- Removed the UPDATE statements for `invoice_financials`
- The view automatically recalculates based on the underlying `receipts` table
- Only the `invoices` table is updated (which is the underlying table)

### 3. SQL Injection Prevention
**Problem**: The original code used string concatenation which is vulnerable to SQL injection.

**Solution**:
- Added proper escaping using `real_escape_string()` for all user inputs
- Improved code structure for better security

## Changes Made

### File: `classes/Invoice.php`
- Updated `createReceipt()` method to include `archive_flag` field
- Fixed invoice_financials handling (read from view, don't update it)
- Added proper value escaping for SQL injection prevention
- Improved error handling

### File: `fix_receipt_creation.sql`
- SQL script to ensure table structure is correct
- Sets default values for `archive_flag`
- Adds indexes for better performance

## Table Structure

The `receipts` table now properly includes:
- `id` (AUTO_INCREMENT PRIMARY KEY)
- `invoice_id` (NOT NULL)
- `receipt_number` (NOT NULL, UNIQUE)
- `customer_id` (NOT NULL)
- `amount_paid` (NOT NULL)
- `payment_method` (NOT NULL)
- `payment_reference` (NULL)
- `received_by` (NOT NULL)
- `issued_at` (NOT NULL, DEFAULT CURRENT_TIMESTAMP)
- `acknowledgment_note` (NULL, with default)
- `notes` (NULL)
- `archive_flag` (NOT NULL, DEFAULT 0) ✓ **FIXED**

## How It Works Now

1. **Receipt Creation**:
   - All required fields are included, including `archive_flag`
   - Values are properly escaped to prevent SQL injection
   - Receipt is inserted into the `receipts` table

2. **Invoice Status Update**:
   - The `invoice_financials` VIEW automatically recalculates based on receipts
   - Only the `invoices` table is updated with the new payment status
   - Customer balance is recalculated

3. **View Calculation**:
   - `invoice_financials` is a VIEW that calculates:
     - `total_paid` (sum of all receipts)
     - `balance_remaining` (total_amount - total_paid)
     - `computed_status` (paid/partial/pending/late)
     - Interest, late fees, and arrears

## Testing

To verify the fix works:
1. Try creating a receipt for an invoice
2. Check that the receipt is created successfully
3. Verify the invoice status updates correctly
4. Check that `invoice_financials` view shows the correct totals

## Installation

Run the SQL fix script:
```bash
php apply_receipt_fix.php
```

This will:
- Ensure `archive_flag` has a default value
- Add necessary indexes
- Verify table structure

## Notes

- The `invoice_financials` VIEW automatically updates when receipts are added
- No manual updates to the view are needed
- All calculations are done automatically by the database

