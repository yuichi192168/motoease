# Installment System Database Schema

## Complete Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    EXISTING TABLES                          │
└─────────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────┐         ┌──────────────┐
│   invoices   │         │ client_list  │         │    users     │
├──────────────┤         ├──────────────┤         ├──────────────┤
│ id (PK)      │         │ id (PK)      │         │ id (PK)      │
│ invoice_no   │         │ firstname    │         │ firstname    │
│ customer_id  │         │ lastname     │         │ lastname     │
│ total_amount │         │ email        │         │ role_type    │
│ payment_type │         │ contact      │         │ status       │
│ ...          │         │ ...          │         │ ...          │
└──────────────┘         └──────────────┘         └──────────────┘
     ↑                           ↑                        ↑
     │                           │                        │
     │                           │                        │
     │                     FK: customer_id         FK: created_by
     │                           │                        │
┌─────────────────────────────────────────────────────────────┐
│              NEW INSTALLMENT TABLES                         │
└─────────────────────────────────────────────────────────────┘

                            ┌───────────────────────────┐
                            │   installment_plans       │
                            ├───────────────────────────┤
                            │ id (PK)                   │
                            │ plan_name                 │
                            │ num_installments          │
                            │ interest_rate             │
                            │ down_payment_pct          │
                            │ status                    │
                            └────────────┬──────────────┘
                                         │
                                         │ FK: plan_id
                                         ↓
                    ┌──────────────────────────────────────────┐
                    │    installment_contracts                 │
                    ├──────────────────────────────────────────┤
                    │ id (PK)                                  │
                    │ contract_number (UNIQUE)                │
                    │ invoice_id (FK) → invoices.id           │
                    │ customer_id (FK) → client_list.id       │
                    │ installment_plan_id (FK) → plans.id     │
                    │ total_amount                            │
                    │ down_payment_amount                     │
                    │ remaining_balance                       │
                    │ start_date                              │
                    │ end_date                                │
                    │ status                                  │
                    └──────────────┬──────────────────────────┘
                                   │
                                   │ FK: contract_id (CASCADE)
                                   ↓
                            ┌───────────────────────────┐
                            │  installment_schedule     │
                            ├───────────────────────────┤
                            │ id (PK)                   │
                            │ contract_id (FK)          │
                            │ installment_number        │
                            │ due_date                  │
                            │ amount_due                │
                            │ principal_amount          │
                            │ interest_amount           │
                            │ status                    │
                            │ paid_amount               │
                            │ late_fee                  │
                            └────────────┬──────────────┘
                                         │
                                         │ FK: schedule_id
                                         │ FK: contract_id
                                         ↓
                    ┌──────────────────────────────────────────┐
                    │     installment_payments                 │
                    ├──────────────────────────────────────────┤
                    │ id (PK)                                  │
                    │ payment_reference (UNIQUE)              │
                    │ schedule_id (FK) → schedule.id          │
                    │ contract_id (FK) → contracts.id         │
                    │ amount_paid                             │
                    │ payment_date                            │
                    │ payment_method                          │
                    │ receipt_number                          │
                    │ created_by (FK) → users.id (SET NULL)   │
                    └──────────────────────────────────────────┘
```

## Table Details

### 1. installment_plans
**Purpose**: Configuration of available payment plans

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) PK | Primary key |
| plan_name | VARCHAR(100) | Plan display name |
| description | TEXT | Plan details |
| number_of_installments | INT(11) | Total installments |
| interest_rate | DECIMAL(5,2) | Annual interest % |
| down_payment_percentage | DECIMAL(5,2) | Down payment % |
| status | ENUM | active/inactive |
| created_at | TIMESTAMP | Record creation |

**Indexes**:
- PRIMARY KEY (id)
- KEY (status)

**Foreign Keys**: None (root table)

---

### 2. installment_contracts
**Purpose**: Main contract linking invoices to payment plans

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) PK | Primary key |
| contract_number | VARCHAR(50) UNIQUE | Contract reference |
| invoice_id | INT(11) FK | References invoices.id |
| customer_id | INT(30) FK | References client_list.id |
| installment_plan_id | INT(11) FK | References installment_plans.id |
| total_amount | DECIMAL(15,2) | Total contract value |
| down_payment_amount | DECIMAL(15,2) | Down payment |
| remaining_balance | DECIMAL(15,2) | Outstanding amount |
| start_date | DATE | Contract start |
| end_date | DATE | Contract end |
| status | ENUM | Contract status |
| created_at | TIMESTAMP | Record creation |
| updated_at | DATETIME | Last update |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (contract_number)
- KEY (invoice_id)
- KEY (customer_id)
- KEY (installment_plan_id)
- KEY (status)

**Foreign Keys**:
- → invoices(id) ON DELETE RESTRICT ON UPDATE CASCADE
- → client_list(id) ON DELETE RESTRICT ON UPDATE CASCADE
- → installment_plans(id) ON DELETE RESTRICT ON UPDATE CASCADE

---

### 3. installment_schedule
**Purpose**: Individual installment due dates and amounts

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) PK | Primary key |
| contract_id | INT(11) FK | References installment_contracts.id |
| installment_number | INT(11) | Installment sequence |
| due_date | DATE | Payment due date |
| amount_due | DECIMAL(15,2) | Total due |
| principal_amount | DECIMAL(15,2) | Principal portion |
| interest_amount | DECIMAL(15,2) | Interest portion |
| status | ENUM | pending/paid/overdue/partial |
| paid_amount | DECIMAL(15,2) | Amount paid |
| paid_date | DATETIME | Payment date |
| late_fee | DECIMAL(15,2) | Late fee charged |
| created_at | TIMESTAMP | Record creation |
| updated_at | DATETIME | Last update |

**Indexes**:
- PRIMARY KEY (id)
- KEY (contract_id)
- KEY (due_date)
- KEY (status)

**Foreign Keys**:
- → installment_contracts(id) ON DELETE CASCADE ON UPDATE CASCADE

---

### 4. installment_payments
**Purpose**: Payment transaction records

| Column | Type | Description |
|--------|------|-------------|
| id | INT(11) PK | Primary key |
| payment_reference | VARCHAR(50) UNIQUE | Payment ID |
| schedule_id | INT(11) FK | References installment_schedule.id |
| contract_id | INT(11) FK | References installment_contracts.id |
| amount_paid | DECIMAL(15,2) | Payment amount |
| payment_date | DATETIME | Payment timestamp |
| payment_method | ENUM | Payment type |
| receipt_number | VARCHAR(50) | Receipt reference |
| notes | TEXT | Payment notes |
| created_by | INT(30) FK | References users.id |
| created_at | TIMESTAMP | Record creation |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (payment_reference)
- KEY (schedule_id)
- KEY (contract_id)
- KEY (receipt_number)
- KEY (created_by)

**Foreign Keys**:
- → installment_schedule(id) ON DELETE RESTRICT ON UPDATE CASCADE
- → installment_contracts(id) ON DELETE RESTRICT ON UPDATE CASCADE
- → users(id) ON DELETE SET NULL ON UPDATE CASCADE

---

## Data Flow

### Creating a Contract

```
1. User creates invoice → invoices table
2. Admin selects installment plan → installment_plans lookup
3. System creates contract → installment_contracts
   ├── Calculates amounts
   └── Sets dates
4. System generates schedule → installment_schedule
   ├── One record per installment
   └── Calculates principal + interest
```

### Processing Payment

```
1. Customer makes payment
2. Admin processes → installment_payments
   ├── Links to schedule record
   ├── Links to contract
   └── Links to staff user
3. System updates:
   ├── installment_schedule (paid_amount, status)
   ├── installment_contracts (remaining_balance)
   └── installment_contracts (status if completed)
```

### Cascade Effects

```
DELETE contract → CASCADE deletes all schedules
DELETE schedule → RESTRICT (payments must exist)
DELETE customer → RESTRICT (if has contracts)
DELETE invoice → RESTRICT (if has contract)
DELETE staff → SET NULL (created_by becomes NULL)
```

---

## Relationships Summary

| From Table | To Table | Relationship Type | On Delete | On Update |
|------------|----------|-------------------|-----------|-----------|
| contracts → invoices | Many-to-One | RESTRICT | CASCADE |
| contracts → clients | Many-to-One | RESTRICT | CASCADE |
| contracts → plans | Many-to-One | RESTRICT | CASCADE |
| schedule → contracts | Many-to-One | CASCADE | CASCADE |
| payments → schedule | Many-to-One | RESTRICT | CASCADE |
| payments → contracts | Many-to-One | RESTRICT | CASCADE |
| payments → users | Many-to-One | SET NULL | CASCADE |

---

## Common Queries

### Get All Active Contracts

```sql
SELECT 
    ic.contract_number,
    c.firstname, c.lastname,
    i.invoice_number,
    ip.plan_name,
    ic.total_amount,
    ic.remaining_balance,
    ic.status
FROM installment_contracts ic
JOIN client_list c ON ic.customer_id = c.id
JOIN invoices i ON ic.invoice_id = i.id
JOIN installment_plans ip ON ic.installment_plan_id = ip.id
WHERE ic.status = 'active'
ORDER BY ic.created_at DESC;
```

### Get Payment History

```sql
SELECT 
    ip.payment_reference,
    ip.amount_paid,
    ip.payment_date,
    ip.payment_method,
    ip.receipt_number,
    isch.installment_number,
    u.firstname AS staff_firstname,
    u.lastname AS staff_lastname
FROM installment_payments ip
JOIN installment_schedule isch ON ip.schedule_id = isch.id
LEFT JOIN users u ON ip.created_by = u.id
WHERE ip.contract_id = ?
ORDER BY ip.payment_date DESC;
```

### Get Overdue Installments

```sql
SELECT 
    ic.contract_number,
    c.firstname, c.lastname,
    isch.installment_number,
    isch.due_date,
    isch.amount_due,
    isch.late_fee,
    DATEDIFF(CURDATE(), isch.due_date) AS days_overdue
FROM installment_schedule isch
JOIN installment_contracts ic ON isch.contract_id = ic.id
JOIN client_list c ON ic.customer_id = c.id
WHERE isch.due_date < CURDATE() 
AND isch.status IN ('pending', 'partial')
ORDER BY isch.due_date ASC;
```

---

## Integrity Rules

### Business Rules Enforced

1. **Contract belongs to one invoice** (One-to-One)
2. **Contract belongs to one customer** (Many-to-One)
3. **Contract uses one plan** (Many-to-One)
4. **Schedule belongs to one contract** (Many-to-One)
5. **Payment belongs to one schedule** (Many-to-One)
6. **Payment belongs to one contract** (Many-to-One)
7. **Cannot delete invoice with contract** (RESTRICT)
8. **Cannot delete customer with contracts** (RESTRICT)
9. **Deleting contract deletes schedules** (CASCADE)
10. **Cannot delete paid schedule** (RESTRICT)

---

This schema ensures data integrity and referential consistency across all installment transactions.

