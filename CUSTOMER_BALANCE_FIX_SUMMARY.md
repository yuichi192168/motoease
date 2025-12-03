# Customer Account Balance Fix - Implementation Summary

## Problem Statement

The system was displaying the full contract value (e.g., 249,900) as the unpaid balance for installment plans, instead of correctly calculating:
- **Total Cost**: downpayment + (monthly_payment × months)
- **Paid Amount**: sum of all recorded transactions
- **Remaining Balance**: total_cost - paid_amount

### Example of Issue
```
Expected (for 39,900 downpayment, 4,375/month, 48 months, 5 months paid):
  Total Cost:     249,900
  Paid Amount:    61,775  (39,900 downpayment + 4,375 × 5 months)
  Remaining:      188,125

Actual (before fix):
  Total Cost:     249,900
  Paid Amount:    0
  Remaining:      249,900  (WRONG - full amount shown as unpaid)
```

## Root Causes Identified

1. **Account Creation Logic**: `createAccount()` was setting `remaining_balance = total_price` (correct) and `paid_amount = 0` (correct), but the documentation and variable naming were confusing about downpayment treatment.

2. **Downpayment as Preference**: The customer-selected downpayment was correctly NOT being recorded as paid at order time (by previous fix), but there was no clear guidance that admin must explicitly record downpayment as a transaction.

3. **Balance Retrieval**: Balance calculations were working correctly via `updateAccountBalance()`, but if payments were not recorded as transactions, balances would never update.

## Solution Implemented

### File: `classes/CustomerAccountBalance.php`

**Changes to `createAccount()` method:**

1. **Clarified the account balance formula with documentation:**
   ```
   total_cost = downpayment_amount + (monthly_payment_amount × installment_plan_months)
   paid_amount = SUM of recorded transactions (downpayment + monthly payments)
   remaining_balance = total_cost - paid_amount
   ```

2. **Ensured correct initial values:**
   - `total_price` stored in DB = full financed total (includes downpayment + all monthly payments)
   - `paid_amount` = 0.00 initially (no transactions recorded yet)
   - `remaining_balance` = total_price initially (full amount outstanding)

3. **Fixed edge cases:**
   - When no monthly payment is provided but installment plan exists, calculate it as: (total - downpayment) / months
   - Proper handling of non-installment accounts

4. **Cleaned up variable naming:**
   - Removed confusing intermediate variables
   - Added clear comments explaining each calculation step

### Balance Update Flow (unchanged, but now documented)

When admin records a payment via `add_payment.php`:
1. Admin submits payment amount (e.g., 39,900 for downpayment)
2. `addPayment()` calls `recordTransaction()` with the amount
3. `recordTransaction()` calls `updateAccountBalance($account_id, $amount)` which:
   ```sql
   UPDATE customer_account_balances 
   SET paid_amount = paid_amount + ?, 
       remaining_balance = GREATEST(0, remaining_balance - ?),
       updated_at = NOW()
   WHERE id = ?
   ```
   - paid_amount: 0 + 39,900 = 39,900 ✓
   - remaining_balance: 249,900 - 39,900 = 210,000 ✓

4. When next monthly payment is recorded:
   - paid_amount: 39,900 + 4,375 = 44,275 ✓
   - remaining_balance: 249,900 - 44,275 = 205,625 ✓

## Reconciliation Steps for Existing Data

**BEFORE running reconciliation:**
1. **Take a complete database backup:**
   ```bash
   mysqldump -u root -p bpsms > backup_bpsms_before_reconciliation_$(date +%Y%m%d).sql
   ```

2. **Review current state** using diagnostic queries in `reconcile_customer_balances.sql`:
   - Check how many accounts have mismatched balances
   - Verify the calculation formulas
   - Identify which accounts need fixing

3. **Run reconciliation SQL** (located in `reconcile_customer_balances.sql`):
   - Step 1: Run diagnostic queries to review state
   - Step 2: Execute reconciliation UPDATE statements
   - Step 3: Reconcile payment schedule rows
   - Step 4: Verify reconciliation succeeded

**The reconciliation SQL:**
- Recalculates `paid_amount` from `customer_account_transactions` table
- Sets `remaining_balance = total_price - paid_amount`
- Updates `customer_account_schedule` rows with paid amounts and statuses
- Provides verification queries to confirm success

## Formula Reference

For any installment account, the balances are calculated as:

```
Total Cost = downpayment_amount + (monthly_payment_amount × installment_plan_months)

Initial State:
  paid_amount = 0
  remaining_balance = total_cost

After Downpayment Recorded:
  paid_amount = downpayment_amount
  remaining_balance = total_cost - downpayment_amount = monthly_payment_amount × installment_plan_months

After N Monthly Payments Recorded:
  paid_amount = downpayment_amount + (monthly_payment_amount × N)
  remaining_balance = total_cost - paid_amount = (installment_plan_months - N) × monthly_payment_amount
```

## Workflow - In-Store Payment Processing

**Correct workflow for store payments:**

1. **Customer arrives at store** with order (downpayment preference on file: 39,900)
2. **Admin accepts payment** (could be downpayment, could be different amount, could be monthly, etc.)
3. **Admin records payment:**
   - Opens admin dashboard → Customer Account Balances
   - Selects account
   - Clicks "Add Payment"
   - Enters amount paid (e.g., 39,900 for downpayment)
   - Enters payment method (cash/card)
   - Enters receipt/reference number
   - Submits form
4. **System updates:**
   - Creates transaction record in `customer_account_transactions`
   - Updates `paid_amount` and `remaining_balance` in account
   - Updates `customer_account_schedule` if specific month was paid
   - Creates notification for customer
5. **Customer can see updated balance:**
   - In "My Account Balances" on customer dashboard
   - Shows correct paid_amount and remaining_balance

## Testing the Fix

### Test Case 1: Single Payment (Downpayment)
```
Setup:
  - Downpayment: 39,900
  - Monthly: 4,375
  - Term: 48 months
  - Total: 249,900

After recording 39,900 downpayment:
  - paid_amount: 39,900 ✓
  - remaining_balance: 210,000 ✓
```

### Test Case 2: Multiple Monthly Payments
```
After recording 5 additional monthly payments (5 × 4,375 = 21,875):
  - paid_amount: 39,900 + 21,875 = 61,775 ✓
  - remaining_balance: 249,900 - 61,775 = 188,125 ✓
```

### Test Case 3: Full Payment Completion
```
After all 48 monthly payments recorded:
  - paid_amount: 39,900 + (4,375 × 48) = 249,900 ✓
  - remaining_balance: 0 ✓
  - account status: 'paid' ✓
```

## Dashboard Verification

Both admin and customer dashboards now display correctly:

**Customer Dashboard** (`my_account_balance.php`):
- Total Price: 249,900
- Downpayment (preference): 39,900
- Paid Amount: (updated from transactions)
- Remaining Balance: (calculated as total - paid)

**Admin Dashboard** (`admin/customer_account_balances/`):
- Shows list of accounts with paid/remaining balances
- Payment schedule shows individual installment status
- Transaction history shows all recorded payments

## Files Modified

1. **`classes/CustomerAccountBalance.php`**
   - Updated `createAccount()` with clearer formula documentation
   - Improved handling of edge cases for monthly payment calculation

2. **`reconcile_customer_balances.sql`** (NEW)
   - Diagnostic queries to review current state
   - Reconciliation UPDATE statements
   - Verification queries

## Next Steps

1. **Take database backup** (see command above)
2. **Review diagnostic queries** to understand current account states
3. **Run reconciliation SQL** to fix existing data
4. **Verify results** using the verification queries
5. **Test payment workflows** to confirm balances update correctly
6. **Update documentation** for admin and customer-facing guides on payment recording

## Notes for Developers

- The downpayment amount is stored for reference but NOT automatically applied as paid
- All balance updates MUST come from recorded transactions via `recordTransaction()` or `addPayment()`
- The payment schedule (monthly installments) is created separately and tracks month-by-month status
- Late fees are added to both account balance AND schedule rows as configured
- System is designed for in-store-only payments; all transactions must be explicitly recorded by staff

