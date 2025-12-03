# Test Data Update Summary

## Changes Made
Updated `test_data_late_fees_installments.sql` to use existing database users and clients instead of creating new test records.

## Existing Users & Clients Used

### Admin/Staff Users (from `users` table)
- **User ID 10**: Henry Legaspi (admin) — Used for all test invoice generation
- User ID 11: Euniel Bandian (service_admin)
- User ID 12: Mark Pancho (inventory)
- User ID 13: Joshua Cansino (inventory)
- User ID 14: Karen Bautista (service_admin)

### Test Customers (from `client_list` table)
| Client ID | Name | Status | Loyalty Card |
|-----------|------|--------|--------------|
| 2 | Jazmine Cruz | Active | Yes |
| 3 | Joshara Carasig | Active | Yes |
| 6 | Lyra Jamaica Vergara | Active | Yes |
| 8 | Aljay Plantado | Active | Yes |

## Test Scenario Distribution

| Scenario | Invoice ID | Customer | Admin | Purpose |
|----------|-----------|----------|-------|---------|
| 1 | 5001 | Jazmine Cruz (2) | Henry Legaspi (10) | On-time payment |
| 2 | 5002 | Joshara Carasig (3) | Henry Legaspi (10) | Partial payments |
| 3 | 5003 | Lyra Jamaica Vergara (6) | Henry Legaspi (10) | Early-stage late (5 days) |
| 4 | 5004 | Aljay Plantado (8) | Henry Legaspi (10) | Severe late (14 days) |
| 5 | 5005 | Jazmine Cruz (2) | Henry Legaspi (10) | 12-month installment |
| 6 | 5006 | Joshara Carasig (3) | Henry Legaspi (10) | Spare parts + loyalty discount |
| 7 | 5007 | Lyra Jamaica Vergara (6) | Henry Legaspi (10) | Mixed cart (motorcycle + parts + oil) |
| 8 | 5008 | Aljay Plantado (8) | Henry Legaspi (10) | Overdue installment with late fee |

## Products Used
All test orders reference:
- **Product ID 5** (assumed existing motorcycle product in database)
- Alternative: Adjust `product_id` in INSERT statements to match actual product IDs in your database

## Database Integration
- ✅ No new user records created
- ✅ No new client records created
- ✅ No duplicate customer/admin entries
- ✅ Uses existing admin user (Henry Legaspi - ID 10)
- ✅ Uses existing customer records (IDs 2, 3, 6, 8)
- ✅ Follows existing database constraints

## Quick Deployment
```bash
# Load updated test data
mysql -u root -p database_name < test_data_late_fees_installments.sql

# Verify scenarios created successfully
mysql -u root -p database_name < validate_test_data.sql
```

## Verification Commands
```sql
-- Verify all test invoices created
SELECT invoice_number, customer_id, transaction_type, payment_status, due_date 
FROM invoices 
WHERE id BETWEEN 5001 AND 5008;

-- Verify all test receipts linked correctly
SELECT r.id, r.receipt_number, r.invoice_id, i.invoice_number, c.firstname, c.lastname
FROM receipts r
LEFT JOIN invoices i ON r.invoice_id = i.id
LEFT JOIN client_list c ON r.customer_id = c.id
WHERE r.id BETWEEN 5001 AND 5010;

-- Verify installment contracts created
SELECT id, customer_id, number_of_installments, status, start_date
FROM installment_contracts
WHERE id IN (100, 101);
```

## Notes
- Product ID referenced as `5` in INSERT statements — verify this exists in your `product_list` table
- All dates are calculated relative to `NOW()` for dynamic execution
- Test data can be safely re-run using `INSERT...ON DUPLICATE KEY UPDATE`
- All scenarios are isolated to invoice IDs 5001-5008 to avoid production conflicts
