# Invoice Arrears and Calculation Fixes Implementation

## Overview
This implementation adds comprehensive arrears computation and fixes all invoice calculations including interest, penalties, late fees, and scheduled payment amounts.

## What Was Implemented

### 1. Enhanced Invoice Financials View
The `invoice_financials` view has been updated to calculate:
- **Interest Amount**: Total interest from unpaid installment schedules
- **Late Fee Amount**: Daily late fees for overdue non-installment invoices
- **Arrears Amount**: Accumulated penalties from overdue installment schedules (3% per month after 7-day grace period)
- **Total Balance Due**: Complete balance including all charges (balance + interest + late fees + arrears)

### 2. Automatic Penalty Calculation
- **Trigger**: Automatically calculates penalties when installment schedules are updated
- **Stored Procedure**: `update_installment_penalties()` to update all overdue installments
- **Grace Period**: 7 days before penalties apply (configurable)
- **Penalty Rate**: 3% per month (configurable)

### 3. Updated Invoice Class
The `Invoice` class now:
- Retrieves all calculated amounts (interest, late fees, arrears, total balance)
- Handles missing columns gracefully (backward compatible)
- Returns detailed installment schedule information including penalties

### 4. Enhanced Frontend Display
The invoice display now shows:
- Interest amounts
- Late fees
- Arrears (penalties)
- Total balance due (including all charges)
- Detailed installment schedule with penalty breakdown

## Installation

### Step 1: Run the SQL Script
Execute the SQL script to update the database:

```sql
SOURCE fix_invoice_calculations_with_arrears.sql;
```

Or run it directly in your MySQL client:
```bash
mysql -u username -p database_name < fix_invoice_calculations_with_arrears.sql
```

### Step 2: Update Penalties for Existing Records
Run the stored procedure to update penalties for all existing overdue installments:

```sql
CALL update_installment_penalties();
```

### Step 3: Verify Installation
Run the verification script:

```bash
php verify_invoice_calculations.php
```

## Configuration

### Invoice Settings
The system uses the following configurable settings in the `invoice_settings` table:

| Setting Key | Default | Description |
|------------|---------|-------------|
| `late_fee_daily_rate` | 0.50 | Daily late fee rate in percent (0.50 = 0.5%/day) |
| `penalty_rate_monthly` | 3.00 | Monthly penalty rate for overdue installments (3.00 = 3%/month) |
| `penalty_grace_period_days` | 7 | Grace period in days before penalty applies |

To update settings:
```sql
UPDATE invoice_settings 
SET setting_value = '5.00' 
WHERE setting_key = 'penalty_rate_monthly';
```

## How It Works

### Arrears Calculation
Arrears are calculated for each overdue installment schedule item:
1. Check if due date has passed
2. Check if payment status is pending, overdue, or partial
3. Check if grace period (7 days) has elapsed
4. Calculate penalty: `(remaining_due) × (penalty_rate) × (months_overdue)`

Example:
- Amount Due: ₱10,000
- Paid: ₱0
- Days Overdue: 45 days (1.5 months)
- Penalty: ₱10,000 × 3% × 2 months = ₱600

### Total Balance Due
The total balance due includes:
```
Total Balance Due = Balance Remaining + Interest + Late Fees + Arrears
```

Where:
- **Balance Remaining**: Original invoice amount minus payments
- **Interest**: Interest from unpaid installment schedules
- **Late Fees**: Daily late fees for overdue invoices (non-installment)
- **Arrears**: Accumulated penalties from overdue installments

## Scheduled Payment Amounts

Installment schedules now show:
- **Amount Due**: Original scheduled amount
- **Penalty Amount**: Calculated penalty for overdue payments
- **Late Fee**: Additional late fees (if applicable)
- **Total Due**: Amount due including all penalties

## Automatic Updates

### Triggers
- **Before Update on installment_schedule**: Automatically calculates penalties when schedule is updated
- **After Insert/Update/Delete on receipts**: Updates invoice timestamps and customer balances

### Manual Updates
To manually update all penalties:
```sql
CALL update_installment_penalties();
```

## API Changes

### Invoice Object Structure
The invoice object now includes:
```json
{
  "id": 1,
  "invoice_number": "INV-2025-0001",
  "total_amount": 100000.00,
  "balance_remaining": 50000.00,
  "interest_amount": 2000.00,
  "late_fee_amount": 500.00,
  "arrears_amount": 1500.00,
  "total_balance_due": 54000.00,
  "installment_schedule": [
    {
      "due_date": "2025-01-15",
      "amount_due": 10000.00,
      "penalty_amount": 300.00,
      "late_fee": 0.00,
      "total_due_with_penalties": 10300.00,
      "status": "overdue"
    }
  ]
}
```

## Testing

### Verification Script
Run the verification script to check all calculations:
```bash
php verify_invoice_calculations.php
```

This will:
1. Check if all required columns exist
2. Verify invoice calculations
3. Verify installment schedule calculations
4. Check configuration settings

### Manual Testing
1. Create an invoice with installment payment
2. Wait for due date to pass (or manually set past due date)
3. Check that penalties are calculated automatically
4. Verify total balance due includes all charges

## Troubleshooting

### Penalties Not Calculating
1. Check if grace period has elapsed (default 7 days)
2. Verify installment schedule status is 'pending', 'overdue', or 'partial'
3. Run manual update: `CALL update_installment_penalties();`
4. Check invoice_settings for correct penalty_rate_monthly value

### Missing Columns
If you see errors about missing columns:
1. Run the SQL script again: `fix_invoice_calculations_with_arrears.sql`
2. Check if view was created: `SHOW CREATE VIEW invoice_financials;`
3. Verify columns: `SHOW COLUMNS FROM invoice_financials;`

### Incorrect Calculations
1. Verify settings in `invoice_settings` table
2. Check if installment contracts are marked as 'active'
3. Verify due dates are set correctly
4. Run verification script to identify issues

## Backward Compatibility

The implementation is backward compatible:
- Missing columns default to 0
- Old invoices without installment schedules work normally
- Existing code continues to function

## Future Enhancements

Potential improvements:
1. Email notifications for overdue payments
2. Automatic daily penalty updates via cron job
3. Penalty waivers for special cases
4. Payment plans for arrears
5. Dashboard showing total arrears across all customers

## Support

For issues or questions:
1. Check the verification script output
2. Review invoice_financials view structure
3. Verify settings configuration
4. Check error logs for SQL errors

