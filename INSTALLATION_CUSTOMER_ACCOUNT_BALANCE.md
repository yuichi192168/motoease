# Customer Account Balance System - Installation Guide

## Quick Start

### 1. Database Installation

Run the SQL file to create all required tables:

```sql
SOURCE create_customer_account_balance_tables.sql;
```

Or manually execute the SQL in `create_customer_account_balance_tables.sql` through phpMyAdmin or your database client.

### 2. Files Created/Modified

#### ✅ New Files Created:

**Database:**
- `create_customer_account_balance_tables.sql` - Database schema

**Classes:**
- `classes/CustomerAccountBalance.php` - Core business logic class

**Admin Interface:**
- `admin/customer_account_balances/index.php` - Account listing page
- `admin/customer_account_balances/view_account.php` - Account details page
- `admin/customer_account_balances/add_payment.php` - Payment recording API
- `admin/customer_account_balances/load_schedule.php` - Schedule loader API

**Customer Interface:**
- `my_account_balance.php` - Customer account balance page
- `load_account_schedule.php` - Payment schedule loader for customers
- `load_account_transactions.php` - Transaction history loader for customers

**System:**
- `cron_account_notifications.php` - Daily notification cron job

**Documentation:**
- `CUSTOMER_ACCOUNT_BALANCE_IMPLEMENTATION.md` - Implementation details
- `INSTALLATION_CUSTOMER_ACCOUNT_BALANCE.md` - This file

#### ✅ Files Modified:

- `classes/Master.php` - Added auto-account creation hook in `place_order()` method
- `admin/inc/navigation.php` - Added navigation link for new account balances page

### 3. Verify Installation

1. **Check database tables exist:**
   ```sql
   SHOW TABLES LIKE 'customer_account%';
   ```
   
   Should show:
   - customer_account_balances
   - customer_account_schedule
   - customer_account_transactions
   - customer_account_notifications

2. **Test order placement:**
   - Place a motorcycle order with installment payment
   - Check `customer_account_balances` table for new record

3. **Test admin interface:**
   - Navigate to: Admin → Customer Accounts → Account Balances (New)
   - Should see account listing page

4. **Test customer interface:**
   - Login as customer
   - Navigate to: My Account Balance
   - Should see customer's accounts

### 4. Setup Cron Job (Optional but Recommended)

For automatic late fee calculation and notifications, set up a daily cron job:

**Via Crontab:**
```bash
# Edit crontab
crontab -e

# Add this line (runs daily at 9 AM)
0 9 * * * /usr/bin/php /path/to/bpsms/cron_account_notifications.php
```

**Via Web Access:**
You can also call the cron script via web with a security token:
```
https://your-domain.com/cron_account_notifications.php?token=your_secure_token_here
```

⚠️ **Important:** Change the token in `cron_account_notifications.php` to a secure value!

### 5. Navigation Setup

The admin navigation has been updated automatically. The new menu item appears under:
- **Admin → Customer Accounts → Account Balances (New)**

For customers, add a link to `my_account_balance.php` in your customer navigation menu.

## Features Overview

### Automatic Account Creation
- ✅ Created when motorcycle orders are placed
- ✅ Includes customer info, item purchased, total price (no VAT)
- ✅ Links to orders and invoices
- ✅ Handles installment plans automatically

### Payment Management
- ✅ Admin can record payments manually (onsite)
- ✅ Supports monthly payments or general payments
- ✅ Tracks payment methods and receipt numbers
- ✅ Updates balances automatically
- ✅ Records all transactions in history

### Late Fee Calculation
- ✅ Automatic 3% late fee after 7 days overdue
- ✅ Applied to remaining balance
- ✅ Status updates to "Late"
- ✅ Notification sent to customer

### Customer Dashboard
- ✅ View all account balances
- ✅ See payment schedules
- ✅ View transaction history
- ✅ See upcoming due dates
- ✅ View late fees

### Admin Dashboard
- ✅ View all customer accounts
- ✅ Add/modify payments
- ✅ View detailed payment schedules
- ✅ View transaction history
- ✅ Print reports

### Notifications
- ✅ Upcoming due date alerts (3 days before)
- ✅ Late payment notifications (>7 days)
- ✅ Payment received confirmations
- ✅ Automated via cron job

## Testing Checklist

After installation, test these scenarios:

- [ ] Place motorcycle order with installment plan
- [ ] Verify account record created in database
- [ ] Record downpayment via admin interface
- [ ] Record monthly payment via admin interface
- [ ] View account in customer dashboard
- [ ] View payment schedule as customer
- [ ] Test late fee calculation (set due date to past)
- [ ] Verify notification creation
- [ ] Test cron job execution

## Troubleshooting

### Issue: Account not created when order is placed
**Solution:**
- Check if order contains motorcycle product
- Check if `CustomerAccountBalance.php` class is accessible
- Check error logs for any exceptions

### Issue: Late fees not applying
**Solution:**
- Ensure `checkAndApplyLateFees()` is called
- Verify due date is >7 days in past
- Check payment status is 'Unpaid' or 'Partial'

### Issue: Notifications not working
**Solution:**
- Verify cron job is running
- Check database for notification records
- Verify customer_id matches in accounts

### Issue: Navigation link not showing
**Solution:**
- Clear browser cache
- Verify `admin/inc/navigation.php` was updated
- Check user role has access (admin, branch_supervisor, admin_assistant)

## Configuration Options

### Late Fee Settings
**File:** `classes/CustomerAccountBalance.php`

Change these values to adjust late fee behavior:
```php
$late_fee_rate = 0.03; // 3% - change to your preferred rate
$days_before_late = 7; // days - change if needed
```

### Notification Timing
**File:** `cron_account_notifications.php`

Adjust notification timing:
```php
// Upcoming due date alert (currently 3 days before)
cas.due_date = DATE_ADD('{$today}', INTERVAL 3 DAY)

// Late payment notification (currently 7 days after due)
cas.due_date < DATE_SUB('{$today}', INTERVAL 7 DAY)
```

## Support

For issues or questions:
1. Check `CUSTOMER_ACCOUNT_BALANCE_IMPLEMENTATION.md` for detailed documentation
2. Review error logs for specific issues
3. Verify database tables and data integrity

## Next Steps

After installation:
1. ✅ Test with a sample order
2. ✅ Train admin staff on payment recording
3. ✅ Inform customers about account balance features
4. ✅ Set up cron job for notifications
5. ✅ Monitor for any issues in first week

---

**System Version:** 1.0  
**Last Updated:** 2025  
**Compatible With:** BPSMS/MotoEase System


