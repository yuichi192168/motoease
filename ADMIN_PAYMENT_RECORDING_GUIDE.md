# In-Store Payment Recording Guide for Admins

## Quick Reference - How to Record Customer Payments

### Balance Formula Reminder
```
Total Cost = Downpayment + (Monthly Payment × Months)
Paid Amount = Sum of ALL recorded transactions
Remaining Balance = Total Cost - Paid Amount
```

### Recording a Payment (Step-by-Step)

1. **Navigate to Admin Dashboard**
   - Go to: Admin Panel → Customer Account Balances

2. **Select Customer Account**
   - Find the customer in the list
   - Click "View Details" or click on the account row

3. **Click "Add Payment" Button**
   - Opens payment recording form

4. **Fill Payment Details**
   - **Amount**: Enter the amount customer is paying TODAY
     - Example: 39,900 for downpayment
     - Example: 4,375 for monthly payment
     - Example: Any amount up to remaining balance
   
   - **Payment Method**: Select from dropdown
     - Cash
     - Card/Credit
     - Bank Transfer
     - Other
   
   - **Receipt/Reference Number**: (Optional)
     - Document the receipt or transaction ID for reference
   
   - **Notes**: (Optional)
     - e.g., "Downpayment", "3rd monthly installment", etc.
   
   - **Schedule/Installment**: (Optional)
     - If paying a specific month's installment, select it
     - If paying toward total account, leave blank

5. **Submit Payment**
   - Click "Save Payment"
   - System shows confirmation

6. **System Updates Automatically**
   - paid_amount increases
   - remaining_balance decreases
   - Customer receives notification
   - Payment history updated

### Examples

#### Example 1: Downpayment Recording
```
Customer: John Doe
Item: Honda PCX 160
Total Cost: 249,900 (39,900 DP + 4,375 × 48 months)

Payment entered: 39,900
Payment method: Cash
Receipt: RCPT-001

RESULT:
Paid Amount changes from 0 → 39,900 ✓
Remaining changes from 249,900 → 210,000 ✓
```

#### Example 2: First Monthly Payment
```
Same customer, 2 weeks later

Payment entered: 4,375
Payment method: Cash
Receipt: RCPT-002
Select: Month 1 (October 2025)

RESULT:
Paid Amount changes from 39,900 → 44,275 ✓
Remaining changes from 210,000 → 205,625 ✓
Schedule Month 1 marked as: Paid ✓
```

#### Example 3: Partial Monthly Payment (Customer didn't have full amount)
```
Same customer, 1 month later

Payment entered: 2,000 (partial - half of monthly)
Payment method: Cash
Receipt: RCPT-003
Select: Month 2 (November 2025)

RESULT:
Paid Amount: 44,275 + 2,000 = 46,275 ✓
Remaining: 205,625 - 2,000 = 203,625 ✓
Schedule Month 2 marked as: Partial (still owes 2,375) ✓
```

### Payment Status Indicators

When viewing payment schedule:

| Status | Meaning | Action Required |
|--------|---------|---|
| **Paid** | Installment fully paid | None |
| **Partial** | Some payment received, more needed | Follow up for remaining amount |
| **Unpaid** | No payment received | Collect payment |
| **Late** | Payment overdue by 7+ days | Apply 3% late fee (auto) |

### Common Issues & Solutions

**Issue**: "Payment amount exceeds remaining balance"
- **Cause**: Entered amount higher than what's owed
- **Solution**: Verify remaining balance and enter correct amount

**Issue**: "Account not found"
- **Cause**: Invalid account ID or account was deleted
- **Solution**: Make sure you selected the correct customer and account

**Issue**: Payment recorded but not showing on customer dashboard
- **Cause**: Possible cache or database sync delay
- **Solution**: Refresh page (F5), or wait 30 seconds for sync

**Issue**: Customer balance shows same as before payment
- **Cause**: Transaction was recorded but balance update failed
- **Solution**: Contact IT support; check error logs in `/logs/` directory

### Verification

After recording payment, verify:

1. ✓ Paid Amount increased
2. ✓ Remaining Balance decreased
3. ✓ Payment amount = Paid increase = Remaining decrease
4. ✓ Customer received notification
5. ✓ Transaction appears in payment history
6. ✓ Schedule row updated (if specific installment was paid)

### Important Notes

- **Downpayment is preference only**: Customer selected a downpayment amount during ordering, but it's only a preference. Record the ACTUAL amount they pay today.
  - Example: Customer wanted 39,900 downpayment but only has 30,000 today → record 30,000

- **Balance formula is automatic**: Don't calculate manually. Just enter the payment amount and let the system calculate the new balance.

- **Late fees are automatic**: If an installment is 7+ days overdue, 3% late fee is automatically applied when you view the account. Payment must include the late fee.

- **Always in-store only**: All payments must be recorded in-store by staff. The system does NOT auto-record online payments yet.

### Reference Material

For more details, see:
- **Admin Dashboard**: `admin/customer_account_balances/`
- **Balance Logic**: `classes/CustomerAccountBalance.php`
- **Technical Summary**: `CUSTOMER_BALANCE_FIX_SUMMARY.md`

