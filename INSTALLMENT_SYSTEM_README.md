# Installment Invoice Receipt System Documentation

## Overview

This installment invoice system extends the existing MotoEase invoice management system to support flexible payment plans for motorcycle purchases. Customers can now opt for installment payments with configurable plans, interest rates, and payment schedules.

## Features

### Core Functionality

1. **Installment Plans Management**
   - Multiple payment plans (3, 6, 12, 18, 24 months)
   - Configurable interest rates
   - Customizable down payment percentages
   - Plan activation/deactivation

2. **Contract Creation**
   - Automatic contract generation from pending invoices
   - Payment schedule calculation with interest
   - Down payment handling
   - Principal and interest breakdown

3. **Payment Processing**
   - Individual installment payment tracking
   - Multiple payment methods (Cash, Card, Bank Transfer, Check)
   - Receipt generation for each payment
   - Automatic status updates (pending → paid → completed)
   - Late fee calculation for overdue payments

4. **Receipts & Reports**
   - Professional installment payment receipts
   - Complete payment schedule documents
   - Overdue payment tracking
   - Contract status monitoring

## Database Schema

### Tables Created

1. **installment_plans**
   - Stores available payment plans
   - Fields: plan_name, number_of_installments, interest_rate, down_payment_percentage, status

2. **installment_contracts**
   - Main contract records linking invoices to payment plans
   - Fields: contract_number, invoice_id, customer_id, plan_id, amounts, dates, status

3. **installment_schedule**
   - Individual payment due dates and amounts
   - Fields: contract_id, installment_number, due_date, amounts, status, late_fees

4. **installment_payments**
   - Payment transaction records
   - Fields: payment_reference, receipt_number, amounts, payment_method, dates

## Installation

### Step 1: Create Database Tables

Run the SQL script to create all necessary tables:

```bash
mysql -u username -p database_name < create_installment_tables.sql
```

Or manually execute the SQL file in phpMyAdmin.

### Step 2: Verify File Structure

Ensure the following files are in place:

```
classes/
├── InstallmentManager.php
├── InstallmentPaymentProcessor.php
├── InstallmentReceiptGenerator.php
└── Master.php (updated)

admin/
└── installment_payments/
    └── index.php

create_installment_tables.sql
INSTALLMENT_SYSTEM_README.md
```

### Step 3: Set Permissions

Ensure your web server has read/write permissions for:
- Classes directory
- Admin directory
- Database tables

## Usage Guide

### For Administrators

#### Creating an Installment Contract

1. Navigate to **Installment Payments** in the admin panel
2. Click **"Create Installment Contract"**
3. Select a pending invoice
4. Choose an installment plan
5. Click **"Create Contract"**
6. The system automatically:
   - Calculates down payment and balance
   - Generates payment schedule
   - Updates invoice payment type to "installment"

#### Processing a Payment

1. Navigate to **Installment Payments**
2. Find the contract and click **"View Contract"**
3. Click **"Pay"** on the desired installment
4. Enter payment details:
   - Amount paid
   - Payment method
   - Optional notes
5. Click **"Process Payment"**
6. Receipt is automatically generated

#### Viewing Reports

- **Active Contracts**: All ongoing installment contracts
- **Completed Contracts**: Fully paid contracts
- **Overdue Payments**: Installments past due date
- **Total Balance**: Outstanding installment balance

### For Customers

Customers can view their installment contracts in their portal:
- Payment schedule with due dates
- Payment history
- Download receipts
- View contract details

## API Endpoints

### Installment Manager

```
GET  /classes/InstallmentManager.php?action=get_plans
POST /classes/InstallmentManager.php?action=create_contract
GET  /classes/InstallmentManager.php?action=get_contract&contract_id={id}
GET  /classes/InstallmentManager.php?action=get_customer_contracts&customer_id={id}
```

### Payment Processor

```
POST /classes/InstallmentPaymentProcessor.php?action=process_payment
GET  /classes/InstallmentPaymentProcessor.php?action=get_payments&contract_id={id}
GET  /classes/InstallmentPaymentProcessor.php?action=get_overdue&contract_id={id}
```

### Receipt Generator

```
GET /classes/InstallmentReceiptGenerator.php?action=generate_receipt&payment_id={id}
GET /classes/InstallmentReceiptGenerator.php?action=generate_schedule&contract_id={id}
```

### Master (Utilities)

```
GET /classes/Master.php?f=get_all_installment_contracts
GET /classes/Master.php?f=get_installment_stats
```

## Configuration

### Default Installment Plans

The system includes 5 pre-configured plans:

1. **3 Months - No Interest** (30% down payment)
2. **6 Months - No Interest** (30% down payment)
3. **12 Months - Low Interest** (20% down, 2% interest)
4. **18 Months - Standard** (20% down, 5% interest)
5. **24 Months - Standard** (20% down, 5% interest)

To modify or add plans, edit the `installment_plans` table.

### Interest Calculation

The system uses **compound interest** formula:

```
Monthly Payment = Principal × (r(1+r)ⁿ) / ((1+r)ⁿ - 1)
Where:
- r = monthly interest rate (annual rate / 12)
- n = number of payments
```

### Late Fees

Overdue payments incur a 5% daily late fee calculated as:
```
Late Fee = Amount Due × 0.05 × Days Overdue
```

## Integration with Existing System

### Invoice Integration

- Installment invoices are marked with `payment_type = 'installment'`
- Regular invoice receipts work alongside installment receipts
- Invoice status updates automatically when contracts are completed

### Order Integration

- Orders with installment invoices maintain normal workflow
- Completion status syncs when contract is fully paid
- Inventory deduction happens on order confirmation, not payment

### Customer Integration

- Installment contracts linked via `customer_id`
- Account balance includes installment balances
- Payment history tracks all transactions

## Security Considerations

1. **Authentication**: All endpoints require admin authentication
2. **Data Validation**: All inputs are sanitized and validated
3. **Transaction Safety**: Database transactions ensure data consistency
4. **Audit Trail**: All payments recorded with timestamps and staff IDs

## Troubleshooting

### Common Issues

**Issue**: Contract not created
- **Solution**: Verify invoice is pending and payment_type is 'cash'

**Issue**: Payment not processed
- **Solution**: Check installment schedule exists and status is 'pending'

**Issue**: Balance not updating
- **Solution**: Run payment processor manually to recalculate

**Issue**: Receipt not generating
- **Solution**: Verify payment record exists and has valid receipt_number

### Maintenance Tasks

**Daily**:
- Update overdue status: `UPDATE installment_schedule SET status='overdue' WHERE due_date < CURDATE() AND status='pending'`

**Weekly**:
- Apply late fees (handled automatically by `applyLateFees()`)
- Generate overdue reports

**Monthly**:
- Review completion rates
- Adjust plan configurations
- Archive completed contracts

## Testing

### Test Scenarios

1. **Create Contract**: Verify all calculations are correct
2. **Process Payment**: Check balance updates and receipt generation
3. **Complete Contract**: Ensure status changes and invoice marks as paid
4. **Late Payments**: Verify late fees are calculated correctly
5. **Partial Payments**: Test partial payment handling

### Test Data

Use the following test invoice data:
- Invoice ID: Test with a pending invoice
- Customer: Use existing customer from client_list
- Amount: ₱100,000+
- Plan: 6 Months - No Interest

## Future Enhancements

Possible improvements:

1. **Online Payment Integration** - Stripe, PayPal, GCash
2. **Email Notifications** - Due date reminders, payment confirmations
3. **Mobile App** - Customer self-service portal
4. **Advanced Reporting** - Cash flow forecasting, analytics
5. **Credit Checking** - Integration with credit bureaus
6. **Flexible Payments** - Custom installment amounts
7. **Grace Period** - Configurable grace period before late fees

## Support

For issues or questions:

1. Check this documentation
2. Review error logs in `admin/logs/`
3. Contact system administrator
4. Check database connections and permissions

## Credits

Developed for **MotoEase - Star Honda Calamba**
Motorcycle Management System
Version 1.0

## License

Proprietary - All rights reserved

