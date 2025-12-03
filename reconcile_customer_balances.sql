-- ========================================================================
-- RECONCILIATION SQL FOR CUSTOMER ACCOUNT BALANCES
-- ========================================================================
-- This script fixes customer account balances by recalculating paid_amount
-- and remaining_balance based on recorded transactions
-- ========================================================================
-- BEFORE RUNNING THIS SCRIPT:
-- 1. Take a complete database backup
-- 2. Test on a development environment first
-- 3. Review the diagnostic queries below to understand the current state
-- ========================================================================

-- ========================================================================
-- STEP 1: DIAGNOSTIC QUERIES - Review Before Reconciliation
-- ========================================================================

-- Show all accounts and calculate what their balances SHOULD be
SELECT 
    cab.id as account_id,
    cab.client_id,
    cl.firstname,
    cl.lastname,
    cab.item_purchased,
    cab.total_price as current_total_price,
    cab.downpayment_amount,
    cab.paid_amount as current_paid_amount,
    cab.remaining_balance as current_remaining_balance,
    COALESCE(SUM(cat.amount), 0) as actual_paid_from_transactions,
    GREATEST(0, cab.total_price - COALESCE(SUM(cat.amount), 0)) as calculated_remaining_balance,
    CASE 
        WHEN cab.paid_amount != COALESCE(SUM(cat.amount), 0) THEN 'MISMATCH'
        WHEN cab.remaining_balance != GREATEST(0, cab.total_price - COALESCE(SUM(cat.amount), 0)) THEN 'MISMATCH'
        ELSE 'OK'
    END as status
FROM customer_account_balances cab
LEFT JOIN client_list cl ON cab.client_id = cl.id
LEFT JOIN customer_account_transactions cat ON cab.id = cat.account_id
GROUP BY cab.id
ORDER BY cab.id;

-- Show accounts with mismatched balances only
SELECT 
    cab.id as account_id,
    cab.item_purchased,
    cab.total_price,
    cab.paid_amount as current_paid,
    COALESCE(SUM(cat.amount), 0) as should_be_paid,
    cab.remaining_balance as current_remaining,
    GREATEST(0, cab.total_price - COALESCE(SUM(cat.amount), 0)) as should_be_remaining
FROM customer_account_balances cab
LEFT JOIN customer_account_transactions cat ON cab.id = cat.account_id
GROUP BY cab.id
HAVING cab.paid_amount != COALESCE(SUM(cat.amount), 0)
   OR cab.remaining_balance != GREATEST(0, cab.total_price - COALESCE(SUM(cat.amount), 0))
ORDER BY cab.id;

-- ========================================================================
-- STEP 2: RECONCILIATION - Execute AFTER reviewing diagnostics
-- ========================================================================

-- Update paid_amount and remaining_balance based on recorded transactions
UPDATE customer_account_balances cab
LEFT JOIN (
    SELECT account_id, COALESCE(SUM(amount), 0) AS total_paid
    FROM customer_account_transactions
    GROUP BY account_id
) transactions ON cab.id = transactions.account_id
SET 
    cab.paid_amount = COALESCE(transactions.total_paid, 0),
    cab.remaining_balance = GREATEST(0, cab.total_price - COALESCE(transactions.total_paid, 0)),
    cab.updated_at = NOW()
WHERE cab.id > 0;

-- ========================================================================
-- STEP 3: RECONCILE PAYMENT SCHEDULE ROWS
-- ========================================================================

-- Update schedule row paid_amount and remaining_balance based on schedule-specific transactions
UPDATE customer_account_schedule cas
LEFT JOIN (
    SELECT schedule_id, COALESCE(SUM(amount), 0) AS total_paid
    FROM customer_account_transactions
    WHERE schedule_id IS NOT NULL
    GROUP BY schedule_id
) transactions ON cas.id = transactions.schedule_id
SET 
    cas.paid_amount = COALESCE(transactions.total_paid, 0),
    cas.remaining_balance = GREATEST(0, cas.amount_due - COALESCE(transactions.total_paid, 0)),
    cas.payment_status = CASE 
        WHEN GREATEST(0, cas.amount_due - COALESCE(transactions.total_paid, 0)) = 0 THEN 'Paid'
        WHEN COALESCE(transactions.total_paid, 0) > 0 THEN 'Partial'
        ELSE 'Unpaid'
    END,
    cas.updated_at = NOW()
WHERE cas.id > 0;

-- ========================================================================
-- STEP 4: VERIFY RECONCILIATION - Run after reconciliation
-- ========================================================================

-- Verify all accounts are now correct
SELECT 
    'Reconciliation Status' as report,
    COUNT(*) as total_accounts,
    SUM(CASE 
        WHEN paid_amount = COALESCE((SELECT SUM(amount) FROM customer_account_transactions WHERE account_id = cab.id), 0)
             AND remaining_balance = GREATEST(0, total_price - COALESCE((SELECT SUM(amount) FROM customer_account_transactions WHERE account_id = cab.id), 0))
        THEN 1 ELSE 0 
    END) as correct_accounts,
    SUM(CASE 
        WHEN paid_amount != COALESCE((SELECT SUM(amount) FROM customer_account_transactions WHERE account_id = cab.id), 0)
             OR remaining_balance != GREATEST(0, total_price - COALESCE((SELECT SUM(amount) FROM customer_account_transactions WHERE account_id = cab.id), 0))
        THEN 1 ELSE 0 
    END) as accounts_with_issues
FROM customer_account_balances cab;

-- ========================================================================
-- STEP 5: EXAMPLE FORMULA VERIFICATION
-- ========================================================================

-- Verify the formula for a specific account (replace 1 with actual account_id)
SELECT 
    cab.id as account_id,
    cab.item_purchased,
    cab.downpayment_amount,
    cab.monthly_payment_amount,
    cab.installment_plan_months,
    -- Expected total_cost
    (cab.downpayment_amount + (cab.monthly_payment_amount * COALESCE(cab.installment_plan_months, 0))) as formula_total_cost,
    cab.total_price as stored_total_price,
    -- Actual paid from transactions
    COALESCE(SUM(cat.amount), 0) as paid_amount,
    cab.paid_amount as stored_paid_amount,
    -- Remaining calculation
    GREATEST(0, cab.total_price - COALESCE(SUM(cat.amount), 0)) as calculated_remaining,
    cab.remaining_balance as stored_remaining,
    cab.status
FROM customer_account_balances cab
LEFT JOIN customer_account_transactions cat ON cab.id = cat.account_id
WHERE cab.id = 1
GROUP BY cab.id;

-- ========================================================================
-- REFERENCE: ACCOUNT BALANCE FORMULA
-- ========================================================================
-- 
-- For an installment account:
--   Downpayment: 39,900
--   Monthly Payment: 4,375
--   Term: 48 months
--
-- TOTAL COST:
--   = 39,900 + (4,375 × 48)
--   = 39,900 + 210,000
--   = 249,900
--
-- IF CUSTOMER PAID: downpayment + 5 months
--   PAID AMOUNT:
--   = 39,900 + (4,375 × 5)
--   = 39,900 + 21,875
--   = 61,775
--
--   REMAINING BALANCE:
--   = 249,900 - 61,775
--   = 188,125
--
-- ========================================================================
