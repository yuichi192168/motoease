# Late Fee Calculation Test & Verification Guide

## Late Fee Rule

**IF** a monthly installment payment is **7 or more days late**:
- Apply a **3% penalty charge**
- Based on the **monthly amortization amount** (not on total remaining balance)

### Formula
```
IF days_overdue >= 7 THEN
    late_fee = monthly_amortization_amount × 0.03
ELSE
    late_fee = 0
```

### Example
```
Monthly Amortization: ₱4,375.00
Late Fee (if 7+ days late): ₱4,375.00 × 0.03 = ₱131.25
```

---

## Test Environment Setup

### What's Been Created

1. **Test Script**: `test_late_fees.php`
   - Opens in browser to set up test account
   - Uses the most recent client from your database
   - Creates test order, invoice, and payment account
   - Displays payment schedule for verification

2. **Diagnostic Queries**: `test_late_fees_diagnostic.sql`
   - SQL queries to review accounts
   - Analyze late fee eligibility
   - Verify balance calculations

3. **Code Fix Applied**: `classes/CustomerAccountBalance.php`
   - Updated `checkAndApplyLateFees()` method
   - Now calculates fee based on `monthly_payment_amount` instead of `remaining_balance`
   - Automatically applies fee when payment is 7+ days overdue

---

## How to Run the Test

### Step 1: Set Up Test Account

1. Open browser: `http://localhost/bpsms/test_late_fees.php`
2. Script will:
   - Load latest client from database
   - Create test order with:
     - Downpayment: ₱39,900
     - Monthly Payment: ₱4,375
     - Term: 12 months
     - Total Contract: ₱249,900
   - Create payment schedule
   - Display test setup and next steps

3. **Save the Account ID** shown on the page (you'll need it)

### Step 2: Record Test Payments

#### 2A: Record Downpayment (On-Time)
```
1. Go to: Admin Dashboard → Customer Account Balances
2. Find your test account
3. Click "Add Payment"
4. Amount: 39,900
5. Payment Method: Cash
6. Receipt: TEST-DOWN-xxxxx
7. Submit
```

**Expected Result:**
- Paid Amount: 0 → 39,900 ✓
- Remaining: 249,900 → 210,000 ✓
- No late fee ✓

#### 2B: Record Month 1 Payment (On-Time)
```
1. Click "Add Payment" again
2. Amount: 4,375
3. Select Schedule: Month 1
4. Payment Method: Cash
5. Receipt: TEST-M1-ONTIME
6. Submit
```

**Expected Result:**
- Paid Amount: 39,900 → 44,275 ✓
- Remaining: 210,000 → 205,625 ✓
- Late Fee: ₱0.00 ✓
- Schedule Month 1: PAID ✓

#### 2C: Record Month 2 Payment (5 Days Late - NO FEE)
```
1. Click "Add Payment"
2. Amount: 4,375
3. Select Schedule: Month 2
4. Payment Method: Cash
5. Receipt: TEST-M2-5DAYS
6. Submit
```

**Expected Result:**
- Paid Amount: 44,275 → 48,650 ✓
- Remaining: 205,625 → 200,875 ✓
- Late Fee: ₱0.00 (only 5 days, threshold is 7) ✓
- Schedule Month 2: PAID ✓

#### 2D: Record Month 3 Payment (10 Days Late - WITH FEE)
```
1. Click "Add Payment"
2. Amount: 4,375 (system will add late fee automatically)
3. Select Schedule: Month 3
4. Payment Method: Cash
5. Receipt: TEST-M3-10DAYS
6. Submit
```

**Expected Result:**
- System detects 10 days late (≥7 days) ✓
- Calculates late fee: 4,375 × 0.03 = ₱131.25 ✓
- Paid Amount: 48,650 → 52,1350 + 131.25 = 53,156.25 ✓
- Remaining: 200,875 → 196,499.75 + late fee ✓
- Schedule Month 3: Shows late fee ✓
- Status: LATE ⚠️

---

## Verification Steps

### 1. Check Account Status
```sql
SELECT 
    id,
    item_purchased,
    total_price,
    paid_amount,
    remaining_balance,
    monthly_payment_amount,
    status
FROM customer_account_balances
WHERE id = [YOUR_ACCOUNT_ID];
```

**Expected values:**
- After all 3 months + downpayment recorded:
  - Paid Amount: 61,775 (39,900 + 4,375 × 5 + late fees)
  - Remaining: Less than initial 249,900

### 2. Check Payment Schedule
```sql
SELECT 
    id,
    installment_number,
    due_date,
    amount_due,
    paid_amount,
    remaining_balance,
    late_fee,
    payment_status,
    paid_date
FROM customer_account_schedule
WHERE account_id = [YOUR_ACCOUNT_ID]
ORDER BY installment_number;
```

**Expected values:**
- Month 1: late_fee = 0, payment_status = Paid
- Month 2: late_fee = 0, payment_status = Paid
- Month 3: late_fee = 131.25, payment_status = Late (paid but with fee)

### 3. Check Transaction History
```sql
SELECT 
    id,
    account_id,
    schedule_id,
    transaction_type,
    amount,
    receipt_number,
    notes,
    transaction_date
FROM customer_account_transactions
WHERE account_id = [YOUR_ACCOUNT_ID]
ORDER BY transaction_date DESC;
```

**Expected records:**
- Type: 'downpayment', Amount: 39,900
- Type: 'monthly_payment', Amount: 4,375 (Month 1)
- Type: 'monthly_payment', Amount: 4,375 (Month 2)
- Type: 'monthly_payment', Amount: 4,375 (Month 3)
- Type: 'late_fee', Amount: 131.25 (Month 3)

---

## Test Scenarios Summary

| Scenario | Days Late | Late Fee Rule | Expected Fee | Test Case |
|----------|-----------|---------------|--------------|-----------|
| On-Time | 0 | days < 7 | ₱0.00 | Month 1 |
| Early | -5 | days < 7 | ₱0.00 | N/A |
| Slightly Late | 5 | days < 7 | ₱0.00 | Month 2 |
| Late | 10 | days ≥ 7 | ₱131.25 | Month 3 |
| Very Late | 30 | days ≥ 7 | ₱131.25 | (optional) |

---

## Dashboard Verification

After recording all payments, verify on both dashboards:

### Customer Dashboard (`my_account_balance.php`)
```
Should show:
- Total Price: ₱249,900
- Downpayment: ₱39,900 (preference shown)
- Paid Amount: ₱61,775+ (includes late fees)
- Remaining Balance: ₱187,350+ (deducted for late fees)
- Overdue Payments: 0 (all recorded)
```

### Admin Dashboard (`admin/customer_account_balances/`)
```
Should show same values as customer dashboard
- List view: shows paid/remaining
- Detail view: shows full transaction history with late fees
- Schedule view: shows each month's status including late fees
```

---

## Late Fee Calculation Code

**Location**: `classes/CustomerAccountBalance.php` - `checkAndApplyLateFees()` method

**Key Changes:**
```php
// OLD (INCORRECT - used remaining_balance):
$late_fee_amount = $schedule['remaining_balance'] * $late_fee_rate;

// NEW (CORRECT - uses monthly_payment_amount):
$late_fee_amount = $monthly_amount * $late_fee_rate;
```

**How it works:**
1. When viewing account, system checks all unpaid/partial schedules
2. Calculates days_overdue = today - due_date
3. If days_overdue ≥ 7:
   - Calculates fee = monthly_payment × 0.03
   - Updates schedule: late_fee, amount_due, remaining_balance, status='Late'
   - Updates account: remaining_balance += fee
   - Records transaction: type='late_fee', amount=fee
   - Notifies customer and admin

---

## Troubleshooting

### Late Fee Not Applying

**Check:**
1. Payment is actually 7+ days late?
   ```sql
   SELECT DATEDIFF(CURDATE(), due_date) FROM customer_account_schedule WHERE id = X;
   -- Result should be ≥ 7
   ```

2. Late fee already applied?
   ```sql
   SELECT late_fee FROM customer_account_schedule WHERE id = X;
   -- If > 0, already applied
   ```

3. Monthly payment amount exists?
   ```sql
   SELECT monthly_payment_amount FROM customer_account_balances WHERE id = X;
   -- Should not be 0 or NULL
   ```

### Balance Not Updating

**Check:**
1. Payment was recorded as transaction?
   ```sql
   SELECT * FROM customer_account_transactions WHERE account_id = X;
   -- Should show all payments
   ```

2. Account balance being refreshed?
   - May need to refresh page or wait for sync
   - Check browser console for errors

### Showing Wrong Late Fee Amount

**Verify:**
- Late fee = monthly_payment × 0.03
- Should NOT be remaining_balance × 0.03
- Check database value: `SELECT late_fee FROM customer_account_schedule WHERE id = X;`

---

## Reference Implementation

### Formula in Code
```
Late Fee = monthly_amortization × 0.03

For test account:
Late Fee = 4,375 × 0.03 = 131.25
```

### Timeline Example
```
Order Date: 2025-01-01
Total: 249,900 (39,900 DP + 4,375 × 48 months)

Month 1:
  Due: 2025-02-01
  Payment: 2025-02-01 (ON TIME)
  Late Fee: ₱0.00
  
Month 2:
  Due: 2025-03-01
  Payment: 2025-03-06 (5 DAYS LATE)
  Late Fee: ₱0.00 (< 7 days)
  
Month 3:
  Due: 2025-04-01
  Payment: 2025-04-11 (10 DAYS LATE)
  Late Fee: ₱131.25 (≥ 7 days, calc: 4,375 × 0.03)
```

---

## Files Modified/Created

| File | Purpose | Status |
|------|---------|--------|
| `classes/CustomerAccountBalance.php` | Fixed late fee calculation | ✅ Modified |
| `test_late_fees.php` | Test setup script | ✅ Created |
| `test_late_fees_diagnostic.sql` | SQL diagnostics | ✅ Created |
| `LATE_FEE_TEST_GUIDE.md` | This guide | ✅ Created |

---

## Success Criteria

Test is successful when:
1. ✅ Test account created with installment plan
2. ✅ Downpayment recorded, balance updated correctly
3. ✅ On-time payment recorded, no fee applied
4. ✅ 5-day late payment recorded, no fee applied
5. ✅ 10-day late payment recorded, ₱131.25 fee applied
6. ✅ All dashboards show consistent, correct values
7. ✅ Transaction history shows all payments and fees
8. ✅ Account status shows paid_amount and remaining_balance correctly

---

## Next Steps

1. **Run test_late_fees.php** to set up test environment
2. **Record test payments** following Step 2 instructions
3. **Verify results** using verification queries
4. **Check dashboards** for correct display
5. **Document results** for validation

