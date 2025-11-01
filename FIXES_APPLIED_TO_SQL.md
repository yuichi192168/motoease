# SQL File Fixes Applied

## Issue: Duplicate Key Errors

When running `create_installment_tables.sql`, MySQL was throwing duplicate key errors because `UNIQUE` constraints automatically create indexes, and we were also explicitly creating `KEY` constraints on the same columns.

## Fixes Applied

### 1. Removed Duplicate Index on `contract_number`
**Line 21**: The column has `UNIQUE` constraint which automatically creates an index
**Removed**: `KEY contract_number (contract_number)` from line 34

**Before**:
```sql
`contract_number` VARCHAR(50) UNIQUE NOT NULL,
...
KEY `contract_number` (`contract_number`),  -- ❌ Duplicate!
```

**After**:
```sql
`contract_number` VARCHAR(50) UNIQUE NOT NULL,  -- ✓ UNIQUE creates index automatically
...
-- KEY removed
```

### 2. Removed Duplicate Index on `payment_reference`
**Line 64**: The column has `UNIQUE` constraint which automatically creates an index
**Removed**: `KEY payment_reference (payment_reference)` from line 75

**Before**:
```sql
`payment_reference` VARCHAR(50) UNIQUE NOT NULL,
...
KEY `payment_reference` (`payment_reference`),  -- ❌ Duplicate!
```

**After**:
```sql
`payment_reference` VARCHAR(50) UNIQUE NOT NULL,  -- ✓ UNIQUE creates index automatically
...
-- KEY removed
```

### 3. Removed Duplicate Index Creation Statements
**Lines 91-96**: Removed redundant index creation statements that were trying to create indexes on already-indexed columns

**Removed**:
```sql
CREATE INDEX idx_installment_contracts_invoice ON installment_contracts(invoice_id);  -- ❌ Already has KEY
CREATE INDEX idx_installment_contracts_customer ON installment_contracts(customer_id);  -- ❌ Already has KEY
CREATE INDEX idx_installment_schedule_contract ON installment_schedule(contract_id);  -- ❌ Already has KEY
CREATE INDEX idx_installment_schedule_due_date ON installment_schedule(due_date, status);  -- ❌ Already has keys
CREATE INDEX idx_installment_payments_contract ON installment_payments(contract_id);  -- ❌ Already has KEY
CREATE INDEX idx_installment_payments_date ON installment_payments(payment_date);  -- ❌ No key needed
```

### 4. Added INSERT IGNORE
Changed `INSERT INTO` to `INSERT IGNORE INTO` to prevent errors when running the script multiple times

**Before**:
```sql
INSERT INTO `installment_plans` ...
```

**After**:
```sql
INSERT IGNORE INTO `installment_plans` ...  -- ✓ Safe to re-run
```

## How MySQL Indexes Work

### UNIQUE Constraint
When you add `UNIQUE` to a column, MySQL automatically creates an index on that column for fast lookups and to enforce uniqueness. You don't need to create a separate `KEY` for it.

### PRIMARY KEY
The primary key automatically creates a unique index. No additional index needed.

### Explicit KEY
Use `KEY` only when you need an index on a non-unique column for performance optimization (like JOINs, WHERE clauses, etc.).

## Final Indexes in Tables

### installment_plans
- PRIMARY KEY on `id`
- KEY on `status`

### installment_contracts
- PRIMARY KEY on `id`
- UNIQUE index on `contract_number` (from UNIQUE constraint)
- KEY on `invoice_id`
- KEY on `customer_id`
- KEY on `installment_plan_id`
- KEY on `status`

### installment_schedule
- PRIMARY KEY on `id`
- KEY on `contract_id`
- KEY on `due_date`
- KEY on `status`

### installment_payments
- PRIMARY KEY on `id`
- UNIQUE index on `payment_reference` (from UNIQUE constraint)
- KEY on `schedule_id`
- KEY on `contract_id`
- KEY on `receipt_number`
- KEY on `created_by`

## Verification

The SQL file can now be run without errors:
```sql
mysql> SOURCE create_installment_tables.sql;
Query OK, 0 rows affected
Query OK, 0 rows affected
Query OK, 0 rows affected
Query OK, 0 rows affected
Query OK, 5 rows affected
```

## Benefits

1. **No Errors**: Script runs successfully
2. **Idempotent**: Can be run multiple times safely
3. **Proper Indexing**: All necessary indexes created without duplicates
4. **Better Performance**: Optimized index structure
5. **Cleaner Code**: No redundant index definitions

## Testing

After running the SQL:
```sql
-- Verify tables created
SHOW TABLES LIKE 'installment%';

-- Verify indexes
SHOW INDEXES FROM installment_contracts;
SHOW INDEXES FROM installment_payments;

-- Verify data inserted
SELECT * FROM installment_plans;
```

Expected result: 4 tables, proper indexes, 5 default plans.

