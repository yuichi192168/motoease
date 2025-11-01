# Installment Database Setup Guide

## Overview

This guide will help you add the Installment Invoice System to your MotoEase database with proper foreign key relationships.

## Prerequisites

Before running the installment system setup, ensure you have:
- ✅ Main MotoEase database installed (`motoease_db.sql` or equivalent)
- ✅ Tables: `invoices`, `client_list`, `users` must exist
- ✅ MySQL/MariaDB version 10.3.3 or higher
- ✅ Database user with ALTER, CREATE, INSERT privileges

## Installation Methods

### Method 1: Fresh Installation (Recommended for New Databases)

If you're setting up a fresh database:

**Step 1**: Run your main database structure
```bash
mysql -u root -p if0_40141531_motoease_7 < motoease_db.sql
```

**Step 2**: Add installment system
```bash
mysql -u root -p if0_40141531_motoease_7 < motoease_final_db.sql
```

This will create all tables and foreign keys in one go.

### Method 2: Update Existing Database (For Production)

If you already have a working MotoEase database:

**Step 1**: Add installment tables
```bash
mysql -u root -p if0_40141531_motoease_7 < update_installment_system.sql
```

**Step 2**: Add foreign key constraints
```bash
mysql -u root -p if0_40141531_motoease_7 < add_installment_foreign_keys.sql
```

### Method 3: Manual Installation (For Testing)

Execute SQL files in phpMyAdmin in this order:

1. `update_installment_system.sql` - Creates tables and inserts default data
2. `add_installment_foreign_keys.sql` - Adds foreign key constraints

## Installation Order

**Critical**: Files must be run in this exact order:

```
1. Motoease main database
   └── motoease_db.sql (or your main DB structure)

2. Installment tables
   └── update_installment_system.sql

3. Foreign key constraints  
   └── add_installment_foreign_keys.sql
```

## Verification

After installation, verify everything worked:

### Check Tables Created
```sql
SHOW TABLES LIKE 'installment%';
```

Expected output:
```
+--------------------------------+
| Tables_in_db (installment%)    |
+--------------------------------+
| installment_contracts          |
| installment_payments           |
| installment_plans              |
| installment_schedule           |
+--------------------------------+
```

### Check Foreign Keys
```sql
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME LIKE 'installment%'
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;
```

Expected output:
```
+-----------------------+------------------------------------------+------------------------+--------------------------+
| TABLE_NAME            | CONSTRAINT_NAME                          | REFERENCED_TABLE_NAME  | REFERENCED_COLUMN_NAME   |
+-----------------------+------------------------------------------+------------------------+--------------------------+
| installment_contracts | fk_installment_contracts_customer        | client_list            | id                       |
| installment_contracts | fk_installment_contracts_invoice         | invoices               | id                       |
| installment_contracts | fk_installment_contracts_plan            | installment_plans      | id                       |
| installment_payments  | fk_installment_payments_contract         | installment_contracts  | id                       |
| installment_payments  | fk_installment_payments_created_by       | users                  | id                       |
| installment_payments  | fk_installment_payments_schedule         | installment_schedule   | id                       |
| installment_schedule  | fk_installment_schedule_contract         | installment_contracts  | id                       |
+-----------------------+------------------------------------------+------------------------+--------------------------+
```

### Check Default Plans
```sql
SELECT * FROM installment_plans;
```

Expected output:
```
+----+------------------------+-------------------------+-------+---------------+----------+
| id | plan_name              | description             | ...   | ...           | status   |
+----+------------------------+-------------------------+-------+---------------+----------+
| 1  | 3 Months - No Interest | 3 monthly installments  | 3     | 0.00          | active   |
| 2  | 6 Months - No Interest | 6 monthly installments  | 6     | 0.00          | active   |
| 3  | 12 Months - Low Interest| 12 monthly installments| 12    | 2.00          | active   |
| 4  | 18 Months - Standard   | 18 monthly installments | 18    | 5.00          | active   |
| 5  | 24 Months - Standard   | 24 monthly installments | 24    | 5.00          | active   |
+----+------------------------+-------------------------+-------+---------------+----------+
```

## Foreign Key Relationships

The installment system has the following relationships:

```
┌────────────────────────┐
│   installment_plans    │
│   (Parent)             │
└───────────┬────────────┘
            │
            ↓
┌──────────────────────────────┐
│  installment_contracts       │
│  - plan_id → plans.id        │
│  - invoice_id → invoices.id  │
│  - customer_id → clients.id  │
└───────────┬──────────────────┘
            │
            ↓
┌──────────────────────────┐
│  installment_schedule    │
│  - contract_id →         │
│    contracts.id          │
└───────────┬──────────────┘
            │
            ↓
┌────────────────────────────────┐
│  installment_payments          │
│  - schedule_id → schedule.id   │
│  - contract_id → contracts.id  │
│  - created_by → users.id       │
└────────────────────────────────┘
```

## Foreign Key Actions

| Foreign Key | Action on DELETE | Action on UPDATE |
|-------------|------------------|------------------|
| contracts → invoices | RESTRICT | CASCADE |
| contracts → clients | RESTRICT | CASCADE |
| contracts → plans | RESTRICT | CASCADE |
| schedule → contracts | CASCADE | CASCADE |
| payments → schedule | RESTRICT | CASCADE |
| payments → contracts | RESTRICT | CASCADE |
| payments → users | SET NULL | CASCADE |

### Explanation:
- **RESTRICT**: Prevents deletion of parent record if child records exist
- **CASCADE**: Updates foreign key when parent is updated
- **SET NULL**: Sets foreign key to NULL when parent is deleted (only for created_by)

## Troubleshooting

### Error: "Cannot add foreign key constraint"

**Cause**: Parent table doesn't exist or has incompatible structure

**Solution**:
```sql
-- Check if parent tables exist
SHOW TABLES LIKE 'invoices';
SHOW TABLES LIKE 'client_list';
SHOW TABLES LIKE 'users';
```

### Error: "Column types are different"

**Cause**: Foreign key column type doesn't match referenced column

**Solution**: Ensure data types match:
- `invoice_id` INT(11) matches `invoices.id` INT(11)
- `customer_id` INT(30) matches `client_list.id` INT(30)
- `created_by` INT(30) matches `users.id` INT(50)

### Error: "Duplicate key name"

**Cause**: Index already exists

**Solution**: 
```sql
-- Check existing indexes
SHOW INDEX FROM installment_contracts;
SHOW INDEX FROM installment_payments;

-- Drop duplicate indexes if found
ALTER TABLE installment_contracts DROP INDEX contract_number;
ALTER TABLE installment_payments DROP INDEX payment_reference;
```

### Error: "Foreign key constraint fails"

**Cause**: Trying to insert invalid reference

**Solution**:
```sql
-- Check for orphaned records
SELECT ic.* FROM installment_contracts ic
LEFT JOIN invoices i ON ic.invoice_id = i.id
WHERE i.id IS NULL;

-- Fix orphaned records or their references
```

## Rollback

If you need to remove the installment system:

```sql
-- Drop foreign keys first
ALTER TABLE installment_payments DROP FOREIGN KEY fk_installment_payments_created_by;
ALTER TABLE installment_payments DROP FOREIGN KEY fk_installment_payments_contract;
ALTER TABLE installment_payments DROP FOREIGN KEY fk_installment_payments_schedule;
ALTER TABLE installment_schedule DROP FOREIGN KEY fk_installment_schedule_contract;
ALTER TABLE installment_contracts DROP FOREIGN KEY fk_installment_contracts_plan;
ALTER TABLE installment_contracts DROP FOREIGN KEY fk_installment_contracts_customer;
ALTER TABLE installment_contracts DROP FOREIGN KEY fk_installment_contracts_invoice;

-- Drop tables
DROP TABLE IF EXISTS installment_payments;
DROP TABLE IF EXISTS installment_schedule;
DROP TABLE IF EXISTS installment_contracts;
DROP TABLE IF EXISTS installment_plans;
```

## Performance Considerations

### Indexes Created

The tables automatically have indexes on:
- Primary keys (auto-indexed)
- UNIQUE keys (auto-indexed)
- Foreign keys (for JOIN performance)
- Status columns (for filtering)
- Date columns (for reporting)

### Query Optimization

Foreign keys with indexes ensure:
- Fast JOINs between related tables
- Quick lookups by contract number
- Efficient status filtering
- Optimized payment date queries

### Maintenance

Run periodically:
```sql
-- Check table sizes
SELECT 
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
FROM information_schema.tables
WHERE table_schema = DATABASE()
AND table_name LIKE 'installment%'
ORDER BY size_mb DESC;

-- Analyze tables for optimization
ANALYZE TABLE installment_contracts;
ANALYZE TABLE installment_schedule;
ANALYZE TABLE installment_payments;
```

## Next Steps

After successful installation:

1. ✅ Verify all tables and foreign keys created
2. ✅ Test creating an installment contract
3. ✅ Test processing a payment
4. ✅ Verify receipts generate correctly
5. ✅ Check that data integrity is maintained

## Support

For issues or questions:
- Check error logs: `logs/error.log`
- Review SQL errors in phpMyAdmin
- Verify table structures match documentation
- Test with sample data

---

**Installation Date**: _________________  
**Verified By**: _________________  
**Status**: ⬜ Success ⬜ Issues Found

