# Late Fee System - Quick Reference Card

## The Rule
```
IF payment is 7+ days late THEN charge 3% of MONTHLY PAYMENT
```

## Formula
```
late_fee = monthly_payment_amount × 0.03
```

## Example
```
Monthly Payment: ₱4,375.00
Late Fee (if ≥7 days late): ₱4,375.00 × 0.03 = ₱131.25
```

---

## Quick Test (5 minutes)

### 1. Set Up Test Account
```
Open: http://localhost/bpsms/test_late_fees.php
Note the Account ID shown
```

### 2. Record 3 Payments
```
Payment 1: ₱39,900 (downpayment) → No late fee
Payment 2: ₱4,375 (Month 1) → No late fee (on time)
Payment 3: ₱4,375 (Month 3, paid 10 days late) → ₱131.25 late fee
```

### 3. Verify Results
```
Dashboard shows:
✓ Paid Amount: 53,156.25 (includes late fee)
✓ Remaining: 196,743.75
✓ No late fee for early or on-time payments
✓ Late fee applied only for Month 3 (10 days late)
```

---

## How It Works (Behind the Scenes)

1. **Customer Places Order**
   - System creates account with monthly payment amount
   - Example: 4,375/month for 12 months = 249,900 total

2. **Admin Records Payment**
   - Payment recorded as transaction
   - System updates: paid_amount += payment_amount
   - System updates: remaining_balance -= payment_amount

3. **System Checks for Late Fees**
   - Automatically checks when payment is recorded
   - Calculates: days_overdue = today - due_date
   - If days_overdue >= 7:
     - Calculates fee = monthly_payment × 0.03
     - Updates schedule: adds late fee to amount_due
     - Updates account: adds fee to remaining_balance
     - Records fee as transaction (type='late_fee')
     - Notifies customer and admin

4. **Dashboard Shows Final Result**
   - Paid Amount: includes payment + any late fees paid
   - Remaining Balance: reflects all deductions
   - Schedule shows which months have late fees
   - Customer sees accurate balance

---

## Files

| File | What It Does |
|------|---|
| `classes/CustomerAccountBalance.php` | Core logic (FIXED) |
| `test_late_fees.php` | Test setup page |
| `test_late_fees_diagnostic.sql` | SQL queries |
| `LATE_FEE_TEST_GUIDE.md` | Full instructions |
| `LATE_FEE_SYSTEM_SUMMARY.md` | Complete documentation |

---

## Key Numbers

```
Late Fee Percentage: 3% (0.03)
Late Fee Threshold: 7 days (>= 7 days triggers fee)
Test Monthly Payment: 4,375.00
Test Late Fee: 131.25 (= 4,375 × 0.03)
```

---

## Verification Queries

### Check if account has payment schedule
```sql
SELECT COUNT(*) FROM customer_account_schedule WHERE account_id = 1;
```

### Check if any late fees applied
```sql
SELECT late_fee FROM customer_account_schedule WHERE account_id = 1 AND late_fee > 0;
```

### Check account balance
```sql
SELECT paid_amount, remaining_balance FROM customer_account_balances WHERE id = 1;
```

### Check all transactions
```sql
SELECT * FROM customer_account_transactions WHERE account_id = 1;
```

---

## Success Indicators

✅ Late fee calculated as `monthly × 0.03` (not `remaining × 0.03`)
✅ Fee applies only when `days_overdue >= 7`
✅ Fee shown in payment schedule
✅ Fee included in remaining_balance
✅ Both dashboards show same values
✅ Transaction history includes late_fee entries

---

## Troubleshooting

| Issue | Check |
|-------|-------|
| No late fee applied | Is it 7+ days late? Does account have monthly_payment_amount? |
| Wrong fee amount | Verify: fee = monthly × 0.03 |
| Balance not updating | Did you record payment? Refresh page? |
| Dashboard shows old balance | Refresh browser (F5 or Ctrl+R) |

---

## Code Location

**File**: `classes/CustomerAccountBalance.php`
**Method**: `checkAndApplyLateFees()` (~line 355)
**Key Line**: `$late_fee_amount = $monthly_amount * $late_fee_rate;`

This line calculates the late fee correctly as 3% of the monthly payment amount.

---

**Status**: ✅ Ready to Test
**Last Updated**: November 28, 2025

