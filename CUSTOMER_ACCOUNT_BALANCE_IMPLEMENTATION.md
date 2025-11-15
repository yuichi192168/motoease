# Customer Account Balance System - Implementation Summary

## Overview
This document describes the complete implementation of the Customer Account Balance system with installment plan tracking, late fee calculation, and payment management.

## Features Implemented

### 1. ✅ Database Schema
**File:** `create_customer_account_balance_tables.sql`

Created the following tables:
- `customer_account_balances` - Main account records
- `customer_account_schedule` - Monthly payment schedule
- `customer_account_transactions` - Payment transaction history
- `customer_account_notifications` - Customer notifications

### 2. ✅ Core Business Logic
**File:** `classes/CustomerAccountBalance.php`

Features:
- Automatic account creation when orders are placed
- Payment recording and tracking
- Late fee calculation (3% after 7 days overdue)
- Payment schedule generation
- Transaction history management
- Notification system

### 3. ✅ Order Processing Integration
**File:** `classes/Master.php` (updated `place_order()` method)

- Automatically creates customer account records when motorcycle orders are placed
- Handles installment plan details
- Links to invoices and contracts

### 4. ✅ Admin Interface
**Files:**
- `admin/customer_account_balances/index.php` - Account listing
- `admin/customer_account_balances/view_account.php` - Account details view
- `admin/customer_account_balances/add_payment.php` - Payment recording API
- `admin/customer_account_balances/load_schedule.php` - Schedule loader API

**Features:**
- View all customer accounts
- Add/modify payments (onsite)
- View payment schedules
- View transaction history
- See late fees and overdue status

### 5. ✅ Customer Dashboard
**Files:**
- `my_account_balance.php` - Customer account balance page
- `load_account_schedule.php` - Payment schedule loader
- `load_account_transactions.php` - Transaction history loader

**Features:**
- View account balances
- View payment schedules
- View transaction history
- See upcoming due dates
- View late fees

### 6. ✅ Notification System
**File:** `cron_account_notifications.php`

**Features:**
- Daily cron job for notifications
- Upcoming due date alerts (3 days before)
- Late payment notifications (>7 days)
- Automatic late fee application

## Database Installation

1. Run the SQL file to create tables:
```sql
SOURCE create_customer_account_balance_tables.sql;
```

## System Behavior

### Order Processing Flow

1. **Customer places motorcycle order** with installment payment option
2. **System automatically creates:**
   - Invoice (via Invoice class)
   - Customer Account Record (via CustomerAccountBalance)
   - Payment Schedule (monthly installments)
   - Initial downpayment transaction (if provided)

### Payment Processing Flow

1. **Customer pays onsite**
2. **Admin records payment** via Admin → Customer Account Balances
3. **System updates:**
   - Account balance (cumulative paid amount)
   - Remaining balance
   - Payment schedule status
   - Transaction history
   - Payment status (Paid/Unpaid/Late)

### Late Fee Calculation

**Trigger:** Payment is >7 days overdue

**Formula:**
```
Late Fee = Remaining Balance × 3% (0.03)
New Balance = Remaining Balance + Late Fee
```

**Status Updates:**
- Status changes to "Late"
- Late fee transaction recorded
- Notification sent to customer

### Payment Status Logic

- **Unpaid:** No payment received, due date not passed
- **Partial:** Some payment received but not full amount
- **Late:** Due date passed and payment >7 days overdue
- **Paid:** Payment fully completed

## Admin Functions

### Viewing Accounts
- Navigate to: **Admin → Customer Accounts → Account Balances (New)**
- View all active customer accounts
- Filter and search capabilities
- Print reports

### Adding Payments
1. Click "Add Payment" button on account
2. Select specific month (optional) or general payment
3. Enter amount, payment method, receipt number
4. System automatically:
   - Checks for late fees
   - Updates balances
   - Records transaction
   - Creates notification

### Viewing Details
- Click "View Details" to see:
  - Complete account summary
  - Payment schedule
  - Transaction history
  - Status information

## Customer Functions

### Viewing Account Balance
- Navigate to: **My Account Balance** (from customer dashboard)
- View all active accounts
- See upcoming payments
- View overdue payments

### Payment Schedule
- Click "View Payment Schedule" on any account
- See all monthly payments
- View due dates and status
- See late fees (if applicable)

### Transaction History
- Click "View Transaction History"
- See all payments recorded
- Filter by date
- View receipt numbers

## Cron Job Setup

### Daily Notification Check

Add to crontab:
```bash
0 9 * * * php /path/to/bpsms/cron_account_notifications.php
```

Or via web access (with security token):
```
https://your-domain.com/cron_account_notifications.php?token=your_secure_token_here
```

**What it does:**
- Checks for upcoming due dates (3 days before)
- Checks for overdue payments (>7 days)
- Applies late fees automatically
- Creates notifications

## Configuration

### Late Fee Settings
**File:** `classes/CustomerAccountBalance.php`

Default: 3% after 7 days
```php
$late_fee_rate = 0.03; // 3%
$days_before_late = 7; // days
```

### Notification Timing
**File:** `cron_account_notifications.php`

- Upcoming due date alert: 3 days before
- Late payment notification: After 7 days overdue

## Key Requirements Met

✅ **Order and Account Creation**
- Automatic creation when motorcycle orders are placed
- Includes customer name, item purchased, total price (no VAT)
- Installment plan details stored
- Downpayment amount recorded

✅ **Installment Plan Tracking**
- Monthly payment amounts stored
- Due dates tracked
- Remaining balance calculated
- Payment status: Paid/Unpaid/Late/Partial

✅ **Payment Updates**
- Manual payment recording by admin (onsite only)
- Cumulative paid amount tracking
- Remaining balance auto-update
- Status auto-update based on due dates
- Transaction history recording

✅ **Late Payment Fee Logic**
- 3% late fee after 7 days
- Automatically applied
- Status becomes "Late"
- Customer notified

✅ **Customer Dashboard**
- View remaining balance
- View monthly dues
- View payment history
- View late fees
- See payment status

✅ **Admin Dashboard**
- Add/modify payments
- View payment history
- Send notifications
- View all accounts

✅ **Notifications**
- Upcoming due dates
- Late payments
- Payment received confirmations

## Testing Checklist

- [ ] Create order with motorcycle and installment plan
- [ ] Verify account record created automatically
- [ ] Record downpayment
- [ ] Record monthly payment
- [ ] Test late fee calculation (set due date to past)
- [ ] Verify notifications created
- [ ] Test customer dashboard views
- [ ] Test admin payment management
- [ ] Test cron job execution

## Notes

- **No VAT applied** to account balances (as per requirements)
- **Payments are onsite only** - no online payment integration
- **Late fees are automatic** - no manual intervention needed
- **Notifications are automated** - via cron job or manual trigger
- **Status synchronization** - Customer dashboard and admin dashboard stay in sync

## Future Enhancements (Optional)

- Email notifications
- SMS notifications
- Payment reminders via email
- Bulk payment processing
- Payment dispute management
- Account statement generation
- Export to PDF/Excel


