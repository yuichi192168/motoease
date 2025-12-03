# Test Data Documentation: Late Fees & Installment Validation

## Overview
This document describes the comprehensive test data generated to validate late fee calculations (3% after 7 days overdue), installment payment logic, and various payment scenarios in the BPSMS system.

---

## Test Data Files
- **Insert Script**: `test_data_late_fees_installments.sql` — Contains test orders, invoices, and receipt records using existing database users and clients
- **Validation Script**: `validate_test_data.sql` — Contains 14 verification queries to validate calculations
- **This Documentation**: Explains expected outcomes and validation criteria

## Existing Users & Clients Used
**Admin/Staff Users** (from `users` table):
- User ID 10: Henry Legaspi (admin) — Used for all test invoice generation
- User ID 11: Euniel Bandian (service_admin)
- User ID 12: Mark Pancho (inventory)
- User ID 13: Joshua Cansino (inventory)
- User ID 14: Karen Bautista (service_admin)

**Test Customers** (from `client_list` table):
- Client ID 2: Jazmine Cruz
- Client ID 3: Joshara Carasig
- Client ID 6: Lyra Jamaica Vergara
- Client ID 8: Aljay Plantado

---

## Test Scenarios

### Scenario 1: On-Time Payment (Invoice ID: 5001)
**Purpose**: Verify no late fees applied when payment made on or before due date

**Test Data**:
- Customer: Jazmine Cruz (Client ID: 2) — Existing database customer
- Product: Honda PCX160 CBS
- Invoice Amount: ₱148,736.00 (incl. VAT)
- Due Date: 2025-01-10 (example; 8 days ago from NOW())
- Payment Date: 2025-01-08 (2 days BEFORE due date)
- Payment Method: Cash
- Admin: Henry Legaspi (User ID: 10)

**Expected Results**:
- Payment Status: `paid`
- Late Fees: ₱0.00
- Days Late: -2 (early)
- Validation Query: `SELECT * FROM validate_test_data.sql [Query 1]`

**Verification Criteria**:
✓ Payment status correctly marked as "paid"
✓ No late fee charges added
✓ Total paid equals invoice amount exactly

---

### Scenario 2: Partial Payment (Invoice ID: 5002)
**Purpose**: Test payment tracking across multiple payment cycles

**Test Data**:
- Customer: Joshara Carasig (Client ID: 3) — Existing database customer
- Invoice Amount: ₱124,096.00
- Due Date: 2025-01-10 (13 days ago)
- Payment 1: ₱62,048.00 (50%) on 2025-01-11 (on due date)
- Payment 2: ₱62,048.00 (50%) on 2025-01-06 (5 days after first payment)
- Total Payments: ₱124,096.00
- Admin: Henry Legaspi (User ID: 10)

**Expected Results**:
- Payment Status: `partial` → `paid`
- Receipt Count: 2
- Balance After Payment 1: ₱62,048.00
- Balance After Payment 2: ₱0.00
- Late Fees: ₱0.00 (both payments on time)

**Verification Criteria**:
✓ Multiple receipts correctly linked to single invoice
✓ Running balance correctly calculated after each payment
✓ Final status transitions from "partial" to "paid"
✓ No late fees (payments within grace period)

---

### Scenario 3: Early-Stage Late Payment (Invoice ID: 5003)
**Purpose**: Validate 3% late fee applied when payment made 1-7 days after due date

**Test Data**:
- Customer: Lyra Jamaica Vergara (Client ID: 6) — Existing database customer
- Invoice Amount: ₱120,736.00
- Due Date: 2025-01-05 (18 days ago)
- Payment Date: 2025-01-10 (5 days AFTER due date)
- Payment Amount: ₱124,358.08 (includes late fee)
- Admin: Henry Legaspi (User ID: 10)

**Calculation Details**:
```
Base Invoice: ₱120,736.00
Late Fee (3%): ₱120,736.00 × 0.03 = ₱3,622.08
Total Payment: ₱120,736.00 + ₱3,622.08 = ₱124,358.08
```

**Expected Results**:
- Payment Status: `late`
- Days Late: 5
- Late Fee Applied: ₱3,622.08
- Total Paid: ₱124,358.08

**Verification Criteria**:
✓ Payment date confirmed > 0 and ≤ 7 days after due date
✓ Late fee calculated as 3% of invoice amount
✓ Late fee added to total payment amount
✓ Invoice status marked as "late"

---

### Scenario 4: Severe Late Payment with Cumulative Fees (Invoice ID: 5004)
**Purpose**: Test cumulative late fee structure for payments >7 days overdue

**Test Data**:
- Customer: Aljay Plantado (Client ID: 8) — Existing database customer
- Invoice Amount: ₱83,776.00
- Due Date: 2024-12-28 (35 days ago)
- Payment Date: 2025-01-11 (14 days AFTER due date)
- Payment Amount: ₱86,364.68 (includes cumulative late fees)
- Admin: Henry Legaspi (User ID: 10)

**Calculation Details**:
```
Base Invoice: ₱83,776.00
First Late Fee (0-7 days): ₱83,776.00 × 0.03 = ₱2,513.28
Cumulative Fee (7-14 days): ₱2,513.28 × 0.03 = ₱75.40
Total Late Fees: ₱2,513.28 + ₱75.40 = ₱2,588.68
Total Payment: ₱83,776.00 + ₱2,588.68 = ₱86,364.68
```

**Expected Results**:
- Payment Status: `late`
- Days Late: 14
- Initial Late Fee: ₱2,513.28
- Cumulative Additional Fee: ₱75.40
- Total Late Fees: ₱2,588.68
- Total Paid: ₱86,364.68

**Verification Criteria**:
✓ Payment date confirmed > 7 days after due date
✓ Initial late fee (3%) calculated on base amount
✓ Cumulative fee (3% of initial fee) added
✓ Total fees represent "increasing penalty" structure
✓ Severe late status applied

---

### Scenario 5: 12-Month Installment Plan (Invoice ID: 5005, Contract ID: 100)
**Purpose**: Validate amortization schedule with correct principal/interest split and declining balance

**Customer**: Jazmine Cruz (Client ID: 2) — Existing database customer
**Admin**: Henry Legaspi (User ID: 10)

**Motorcycle Details**:
- Product: Honda PCX160 CBS
- SRP: ₱132,800.00
- Down Payment: ₱39,900.00
- Financed Amount: ₱92,900.00
- Term: 12 months
- Monthly Interest Rate: 2.0%
- Monthly Payment: ₱11,039.00

**Amortization Schedule** (Declining Balance):
| Month | Due Date | Principal | Interest | Monthly Payment | Remaining Balance |
|-------|----------|-----------|----------|-----------------|-------------------|
| 1 | 2025-01-02 | ₱9,775.00 | ₱1,264.00 | ₱11,039.00 | ₱83,125.00 |
| 2 | 2025-01-09 | ₱9,930.00 | ₱1,109.00 | ₱11,039.00 | ₱73,195.00 |
| 3 | 2025-01-16 | ₱10,088.00 | ₱951.00 | ₱11,039.00 | ₱63,107.00 |
| 4 | 2025-01-23 | ₱10,250.00 | ₱789.00 | ₱11,039.00 | ₱52,857.00 |
| 5-12 | Future | (Pending) | (Pending) | ₱11,039.00 | (Decreasing) |

**Total Calculation**:
```
Total Payments (12 × ₱11,039.00): ₱132,468.00
Total Interest Paid: ₱39,568.00
Total Principal: ₱92,900.00
Verification: ₱92,900.00 + ₱39,568.00 = ₱132,468.00 ✓
```

**Current State**:
- Down Payment: ✓ Paid (₱39,900.00)
- Installments 1-4: ✓ Paid (₱44,156.00 total)
- Installments 5-12: ⏳ Pending (₱88,312.00 total)

**Expected Results**:
- Contract Status: `active`
- Paid Count: 4
- Pending Count: 8
- Total Principal Matched: ✓
- Total Interest Calculated: ✓
- Declining Interest Pattern: ✓

**Verification Criteria**:
✓ Principal increases each month (declining interest rate)
✓ Interest decreases each month (as balance declines)
✓ Monthly payment constant at ₱11,039.00
✓ Sum of principal = financed amount (₱92,900.00)
✓ Remaining balance correctly calculated
✓ Payment dates exactly 7 days apart

---

### Scenario 6: Spare Parts with Loyalty Discount (Invoice ID: 5006)
**Purpose**: Test 2% loyalty discount display for parts orders with loyalty card

**Test Data**:
- Customer: Joshara Carasig (Client ID: 3, Loyalty Card: Yes) — Existing database customer
- Transaction Type: `motorcycle_parts_purchase`
- Items: 3 spare parts items at ₱15,000.00 each
- Subtotal: ₱45,000.00
- VAT (12%): ₱5,400.00
- Invoice Total: ₱50,400.00
- Payment: ₱25,200.00 (50%, partial)
- Remaining: ₱25,200.00 (pending)
- Admin: Henry Legaspi (User ID: 10)

**Loyalty Discount Calculation** (Display-Only):
```
Invoice Total: ₱50,400.00
Loyalty Discount (2%): ₱50,400.00 × 0.02 = ₱1,008.00
Adjusted Total (for display): ₱50,400.00 - ₱1,008.00 = ₱49,392.00
```

**Expected Results**:
- Transaction Type: `motorcycle_parts_purchase`
- Loyalty Card: ✓ Yes
- Eligibility: ✓ 2% discount applicable
- Discount Amount: ₱1,008.00
- Discount Display: ✓ Shown in receipt/invoice
- Stored Invoice Total: ₱50,400.00 (unchanged)
- Display Total: ₱49,392.00 (with discount applied)

**Verification Criteria**:
✓ Transaction type correctly identified
✓ Loyalty card status verified
✓ Discount only applied to parts (not motorcycles)
✓ Discount percentage = 2% exactly
✓ Discount calculated on total amount including VAT
✓ Frontend displays discount; backend maintains original total
✓ Multiple partial payments tracked independently

---

### Scenario 7: Mixed Cart Transaction (Invoice ID: 5007)
**Purpose**: Verify mixed transaction classification and late fee application

**Test Data**:
- Customer: Lyra Jamaica Vergara (Client ID: 6, Loyalty Card: Yes) — Existing database customer
- Order Contains:
  - 1x Motorcycle (PCX160): ₱132,800.00
  - 2x Spare Parts: ₱12,500.00 each = ₱25,000.00
  - 1x Oil: ₱500.00
- Subtotal: ₱158,300.00
- VAT (12%): ₱18,996.00
- Invoice Total: ₱177,296.00
- Due Date: 5 days ago
- Payment Date: NOW() - 5 days (7 days late by payment date)
- Admin: Henry Legaspi (User ID: 10)

**Late Fee Calculation**:
```
Base Invoice: ₱177,296.00
Days Late: 7 (triggers 3% late fee)
Late Fee (3%): ₱177,296.00 × 0.03 = ₱5,318.88
Total Payment: ₱177,296.00 + ₱5,318.88 = ₱182,614.88
```

**Expected Results**:
- Transaction Type: `motorcycle_purchase` (primary item type)
- Payment Status: `late`
- Late Fee Applied: ₱5,318.88
- Total Paid: ₱182,614.88
- Days Late: Exactly 7
- Loyalty Discount: Not applicable (no loyalty card)

**Verification Criteria**:
✓ Mixed items correctly grouped under motorcycle_purchase type
✓ Late fee triggered at 7-day threshold
✓ Late fee calculation accurate (3% of total)
✓ Multiple item types coexist in invoice_items
✓ Payment status correctly marked as "late"
✓ Loyalty discount not applied (customer has no loyalty card)

---

### Scenario 8: Overdue Installment with Late Fee (Invoice ID: 5008, Contract ID: 101)
**Purpose**: Test late fee application to individual installment payments

**Customer**: Aljay Plantado (Client ID: 8) — Existing database customer
**Admin**: Henry Legaspi (User ID: 10)

**Product Details**:
- Motorcycle: Honda PCX160 CBS
- SRP: ₱107,800.00
- Down Payment: ₱32,400.00
- Financed Amount: ₱102,828.00
- Term: 24 months
- Monthly Interest Rate: 5.0%
- Monthly Payment: ₱5,490.00

**Payment Schedule State**:
- Down Payment (₱32,400.00): ✓ Paid
- Installment 1 (₱5,490.00, due 30 days ago): ✗ **OVERDUE**
- Installment 2 (₱5,490.00, due 23 days ago): ✓ Paid
- Installment 3 (₱5,490.00, due 16 days ago): ✓ Paid
- Installment 4 (₱5,490.00, due 9 days ago): ✓ Paid
- Installment 5 (₱5,490.00, due 2 days ago): ⏳ Pending

**Overdue Installment 1 Details**:
```
Due Date: 30 days ago
Current Status: OVERDUE
Days Overdue: 30 days
Principal: ₱4,248.00
Interest: ₱1,242.00
Monthly Payment: ₱5,490.00
Late Fee (3%): ₱5,490.00 × 0.03 = ₱164.70
Total Required: ₱5,490.00 + ₱164.70 = ₱5,654.70
```

**Payment of Overdue Installment**:
- Receipt #: RCPT-TEST-008-INST1
- Amount Paid: ₱5,654.70
- Note: "Installment 1 payment (30 days late) - includes 3% late fee (₱164.70)"

**Expected Results**:
- Installment 1 Status: `overdue` (remains until paid)
- Late Fee Applied: ₱164.70
- Total Payment Required: ₱5,654.70
- Subsequent Payment: Installment 5 pending (now due)
- Running Balance: Decreasing with each payment

**Verification Criteria**:
✓ Installment 1 correctly flagged as overdue (>7 days)
✓ Late fee = 3% of monthly installment amount
✓ Late fee applied only to overdue installment
✓ On-time installments (2-4) show ₱0 late fee
✓ Down payment receipt separate from installment receipts
✓ Contract remains active despite one overdue installment
✓ Future installments remain pending until due

---

## Database Structure for Test Data

### Customer Records Inserted
| Client ID | Name | Loyalty Card | Account Balance | Role |
|-----------|------|--------------|-----------------|------|
| 2 | Jazmine Cruz | Yes | ₱0.00 | Buyer |
| 3 | Joshara Carasig | Yes | ₱0.00 | Buyer |
| 6 | Lyra Jamaica Vergara | Yes | ₱0.00 | Buyer |
| 8 | Aljay Plantado | Yes | ₱0.00 | Buyer |

**Note**: All existing customers from the database (no new customer records created)

### Invoice Summary
| Invoice ID | Order # | Customer | Transaction Type | Total | Due Date | Payment Status | Days Late |
|-----------|---------|----------|-----------------|-------|----------|---|---|
| 5001 | ORD-TEST-001 | Johnny Santos | motorcycle_purchase | ₱148,736 | 10 days ago | paid | -2 |
| 5002 | ORD-TEST-002 | Maria Cruz | motorcycle_purchase | ₱124,096 | 13 days ago | paid | 0-5 |
| 5003 | ORD-TEST-003 | Roberto Fernandez | motorcycle_purchase | ₱120,736 | 18 days ago | late | 5 |
| 5004 | ORD-TEST-004 | Carlos Mendez | motorcycle_purchase | ₱83,776 | 28 days ago | late | 14 |
| 5005 | ORD-TEST-005 | Ana Gonzalez | motorcycle_purchase | ₱172,841 | 23 days ago | partial | 0 (inst.) |
| 5006 | ORD-TEST-006 | Patricia Lopez | motorcycle_parts_purchase | ₱50,400 | 2 days ago | partial | 0 |
| 5007 | ORD-TEST-007 | Diego Torres | motorcycle_purchase | ₱177,296 | 5 days ago | late | 7 |
| 5008 | ORD-TEST-008 | Valentina Reyes | motorcycle_purchase | ₱135,228 | 38 days ago | partial | var. |

### Installment Contracts
| Contract ID | Customer | Term | Monthly Payment | Status | Down Payment Paid | Installments Paid |
|-----------|----------|------|-----------------|--------|-------------------|-------------------|
| 100 | Ana Gonzalez | 12 months | ₱11,039.00 | active | Yes | 4 of 12 |
| 101 | Valentina Reyes | 24 months | ₱5,490.00 | active | Yes | 3 of 24 (1 late) |

---

## Running the Test Data

### 1. Insert Test Data
```sql
-- Load test customer, order, and invoice records
SOURCE c:\xampp\htdocs\bpsms\test_data_late_fees_installments.sql;
```

### 2. Validate Each Scenario
```sql
-- Run all 14 validation queries
SOURCE c:\xampp\htdocs\bpsms\validate_test_data.sql;
```

### 3. Expected Query Results

**Query 1 (On-Time Payment)**:
- Should show: days_early = 2, payment_status = "paid", validation = "PASS"

**Query 2 (Partial Payments)**:
- Should show: payment_count = 2, total_paid = invoice total, validation = "PASS"

**Query 3 (Early-Stage Late)**:
- Should show: days_late = 5, late_fee = ₱3,622.08, validation = "PASS"

**Query 4 (Severe Late)**:
- Should show: days_late = 14, total_late_fee = ₱2,588.68, validation = "PASS"

**Query 5 (Installment Summary)**:
- Should show: 12 schedule items, paid_count = 4, pending_count = 8, validation = "PASS"

**Query 6 (Monthly Breakdown)**:
- Should show: 12 rows with declining interest, principal increasing

**Query 7 (Loyalty Discount)**:
- Should show: has_loyalty = Yes, eligible_discount = ₱1,008.00, validation = "PASS"

**Query 8 (Mixed Transaction)**:
- Should show: 3 item types, days_late = 7, validation = "PASS"

**Query 9 (Overdue Installment)**:
- Should show: days_overdue = 30, late_fee = ₱164.70, status = "overdue"

**Query 10 (Summary)**:
- Should show: All 8 invoices, status distribution, total late fees calculated

**Query 11 (Payment Timeline)**:
- Should show: 8+ payments with category (Early/Late/Severely Late)

**Query 12 (Interest Verification)**:
- Should show: 2 contracts, total_interest matching expected (PASS)

**Query 13 (Late Fee Summary)**:
- Should show: Total late fees across all scenarios = ~₱14,177.54

**Query 14 (Loyalty Eligibility)**:
- Should show: Only parts transactions with eligible discounts

---

## Verification Checklist

After running test data, verify the following:

### ✓ On-Time Payment Logic
- [ ] Invoice 5001 shows payment_status = "paid"
- [ ] No late fees applied
- [ ] Total paid = ₱148,736.00

### ✓ Partial Payment Tracking
- [ ] Invoice 5002 has 2 receipts
- [ ] First receipt: ₱62,048.00, second receipt: ₱62,048.00
- [ ] Status correctly changes to "paid" after full payment

### ✓ Late Fee Calculations (Early Stage)
- [ ] Invoice 5003: Late fee = ₱3,622.08 (exactly 3%)
- [ ] Days late = 5 (within 7-day window)
- [ ] Total paid = ₱124,358.08

### ✓ Late Fee Calculations (Severe)
- [ ] Invoice 5004: Total late fee = ₱2,588.68 (3% + cumulative)
- [ ] Days late = 14 (exceeds 7-day threshold)
- [ ] Initial fee + cumulative fee correctly calculated

### ✓ Installment Amortization (12-Month)
- [ ] Contract 100: 12 schedule items created
- [ ] Principal total = ₱92,900.00 (financed amount)
- [ ] Interest total ≈ ₱39,568.00
- [ ] Principal increases, interest decreases each month
- [ ] First 4 installments marked "paid", remaining "pending"

### ✓ Loyalty Discount Eligibility
- [ ] Invoice 5006: Transaction type = "motorcycle_parts_purchase"
- [ ] Customer 205: loyalty_card = 1
- [ ] Eligible discount = ₱1,008.00 (2% of total)
- [ ] Discount displayed in receipt/invoice output

### ✓ Mixed Transaction Processing
- [ ] Invoice 5007: Multiple item types (motorcycle, parts, oil)
- [ ] Transaction type = "motorcycle_purchase" (primary)
- [ ] Late fee applied correctly (7 days late)
- [ ] No loyalty discount (customer has no card)

### ✓ Overdue Installment Handling
- [ ] Contract 101, Installment 1: Status = "overdue"
- [ ] Late fee = ₱164.70 (3% of monthly payment)
- [ ] Other installments (2-4): Marked "paid", no late fee
- [ ] Installment 5: Pending future payment

---

## Key Test Validations

### Late Fee Logic
```
Rule 1: If payment_date - due_date ≤ 0 days → No late fee
Rule 2: If 0 < (payment_date - due_date) ≤ 7 days → Late fee = 3% of amount
Rule 3: If (payment_date - due_date) > 7 days → Late fee = 3% + (3% of 3%)
```

### Amortization Schedule
```
Rule: For each payment:
  - Interest = Remaining_Balance × Monthly_Interest_Rate
  - Principal = Payment - Interest
  - Remaining_Balance -= Principal
  - Result: Principal increases, Interest decreases each month
```

### Loyalty Discount Eligibility
```
Rule: IF transaction_type LIKE '%part%' AND (loyalty_card=1 OR has_loyalty=1)
      THEN discount = 2% of invoice_total
      ELSE no discount
```

---

## Troubleshooting

### Common Issues

**Problem**: Late fees showing incorrect amounts
- **Check**: DATEDIFF calculation (PHP `strtotime` vs MySQL function)
- **Fix**: Use consistent MySQL date functions in all queries

**Problem**: Installment interest totals don't match
- **Check**: Declining balance calculation vs. simple interest
- **Fix**: Verify formula uses remaining balance each month, not initial

**Problem**: Loyalty discount not displaying
- **Check**: Transaction type classification and loyalty field names
- **Fix**: Verify `transaction_type` includes "part" substring; verify loyalty field exists

**Problem**: Multiple late fee applications
- **Check**: Receipt record linking and invoice update logic
- **Fix**: Ensure one late fee per invoice, not per receipt

---

## Notes for Development Team

1. **Late Fee Display**: Currently calculated in frontend during display. Consider moving to backend stored value for consistency.

2. **Installment Status Tracking**: Current implementation tracks per-installment status. Monitor for edge cases with partial payments across multiple installments.

3. **Loyalty Discount**: Display-only implementation prevents double-charging. Consider adding explicit discount_applied flag to invoice record for audit trail.

4. **Database Indexes**: Add indexes on `invoices.due_date`, `receipts.issued_at`, and `installment_schedule.contract_id` for query performance.

5. **Test Data Refresh**: Scripts use INSERT...ON DUPLICATE KEY UPDATE to allow safe re-runs. Remove if test data persistence not desired.

---

## Files Reference

- **Test Data Insertion**: `test_data_late_fees_installments.sql` (600+ lines)
- **Validation Queries**: `validate_test_data.sql` (400+ lines)
- **This Documentation**: `TEST_DATA_DOCUMENTATION.md`

**Total Test Records Inserted**:
- Customers: 8
- Orders: 8
- Invoices: 8
- Invoice Items: 10+
- Receipts: 11
- Installment Contracts: 2
- Installment Schedules: 17

**Total Test Scenarios Covered**: 8 (on-time, partial, early-late, severe-late, 12mo-installment, loyalty-discount, mixed-transaction, overdue-installment)

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-01-20 | Initial comprehensive test data package with 8 scenarios and 14 validation queries |

---

**Last Updated**: 2025-01-20
**Status**: Ready for deployment
