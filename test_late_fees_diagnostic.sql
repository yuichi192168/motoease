-- ========================================================================
-- LATE FEE CALCULATION VERIFICATION TEST
-- ========================================================================
-- This script tests the late fee calculation logic (3% after 7 days late)
-- using real client data and simulated payment timelines
-- ========================================================================

-- ========================================================================
-- STEP 1: IDENTIFY LATEST CLIENT
-- ========================================================================

-- Get the most recently added client
SELECT 
    id,
    firstname,
    lastname,
    email,
    contact,
    date_created
FROM client_list 
ORDER BY created_at DESC 
LIMIT 1;

-- ========================================================================
-- STEP 2: UNDERSTAND THE LATE FEE RULE
-- ========================================================================
-- 
-- RULE: If a payment is 7 or more days late, apply 3% fee
--
-- Calculation:
--   late_fee = monthly_amortization_amount × 0.03
--
-- Example:
--   Monthly Payment: 4,375
--   Late Fee (7+ days): 4,375 × 0.03 = 131.25
--
-- ========================================================================

-- ========================================================================
-- STEP 3: QUERY EXISTING ACCOUNTS (if any)
-- ========================================================================

-- Show all customer accounts with payment schedule
SELECT 
    cab.id as account_id,
    cab.client_id,
    cl.firstname,
    cl.lastname,
    cab.item_purchased,
    cab.total_price,
    cab.downpayment_amount,
    cab.monthly_payment_amount,
    cab.installment_plan_months,
    cab.paid_amount,
    cab.remaining_balance,
    cab.status,
    COUNT(cas.id) as scheduled_months,
    SUM(CASE WHEN cas.payment_status = 'Paid' THEN 1 ELSE 0 END) as paid_months,
    SUM(CASE WHEN cas.payment_status = 'Late' THEN 1 ELSE 0 END) as late_months,
    SUM(CASE WHEN cas.payment_status IN ('Unpaid', 'Partial') THEN 1 ELSE 0 END) as pending_months
FROM customer_account_balances cab
LEFT JOIN client_list cl ON cab.client_id = cl.id
LEFT JOIN customer_account_schedule cas ON cab.id = cas.account_id
GROUP BY cab.id
ORDER BY cab.created_at DESC;

-- ========================================================================
-- STEP 4: VIEW PAYMENT SCHEDULE FOR LATEST ACCOUNT
-- ========================================================================

-- Show detailed schedule for the most recent account
SELECT 
    cas.id as schedule_id,
    cas.account_id,
    cas.installment_number,
    cas.due_date,
    cas.amount_due,
    cas.paid_amount,
    cas.remaining_balance,
    cas.late_fee,
    cas.payment_status,
    cas.paid_date,
    -- Days overdue (negative = early, 0 = on-time, positive = late)
    DATEDIFF(CURDATE(), cas.due_date) as days_overdue,
    CASE 
        WHEN DATEDIFF(CURDATE(), cas.due_date) > 7 AND cas.late_fee = 0 
        THEN 'ELIGIBLE FOR LATE FEE'
        WHEN DATEDIFF(CURDATE(), cas.due_date) > 7 AND cas.late_fee > 0 
        THEN 'LATE FEE APPLIED'
        WHEN DATEDIFF(CURDATE(), cas.due_date) <= 7 AND DATEDIFF(CURDATE(), cas.due_date) > 0 
        THEN 'LATE (< 7 DAYS)'
        WHEN DATEDIFF(CURDATE(), cas.due_date) <= 0 
        THEN 'ON-TIME'
        ELSE 'UNKNOWN'
    END as fee_status
FROM customer_account_schedule cas
WHERE cas.account_id = (
    SELECT cab.id FROM customer_account_balances cab ORDER BY cab.created_at DESC LIMIT 1
)
ORDER BY cas.installment_number;

-- ========================================================================
-- STEP 5: VIEW TRANSACTION HISTORY FOR LATEST ACCOUNT
-- ========================================================================

-- Show all payments recorded for the account
SELECT 
    cat.id as transaction_id,
    cat.account_id,
    cat.schedule_id,
    cat.transaction_type,
    cat.amount,
    cat.payment_method,
    cat.receipt_number,
    cat.notes,
    cat.transaction_date,
    cat.processed_by,
    u.firstname as processed_by_firstname,
    u.lastname as processed_by_lastname
FROM customer_account_transactions cat
LEFT JOIN users u ON cat.processed_by = u.id
WHERE cat.account_id = (
    SELECT cab.id FROM customer_account_balances cab ORDER BY cab.created_at DESC LIMIT 1
)
ORDER BY cat.transaction_date DESC;

-- ========================================================================
-- STEP 6: CURRENT ACCOUNT STATUS - LATEST ACCOUNT
-- ========================================================================

-- Show comprehensive status for latest account
SELECT 
    cab.id as account_id,
    cl.firstname,
    cl.lastname,
    cab.item_purchased,
    cab.total_price as total_contract_value,
    cab.downpayment_amount as down_payment_preference,
    cab.monthly_payment_amount as monthly_amortization,
    cab.installment_plan_months as term_months,
    cab.paid_amount as total_paid_to_date,
    cab.remaining_balance as outstanding_balance,
    cab.status,
    cab.created_at as account_created,
    cab.updated_at as last_updated,
    -- Calculate how much should be paid based on on-time schedule
    (cab.downpayment_amount + (
        SELECT COUNT(*) FROM customer_account_schedule 
        WHERE account_id = cab.id 
        AND payment_status = 'Paid'
    ) * cab.monthly_payment_amount) as expected_paid_if_all_ontime,
    -- Check for any late fees
    COALESCE(
        (SELECT SUM(late_fee) FROM customer_account_schedule WHERE account_id = cab.id),
        0
    ) as total_late_fees_applied
FROM customer_account_balances cab
LEFT JOIN client_list cl ON cab.client_id = cl.id
WHERE cab.id = (
    SELECT cab.id FROM customer_account_balances cab ORDER BY cab.created_at DESC LIMIT 1
);

-- ========================================================================
-- STEP 7: LATE FEE ANALYSIS - WHAT FEES SHOULD BE APPLIED?
-- ========================================================================

-- Analyze which payments are eligible for late fees
SELECT 
    cas.id as schedule_id,
    cas.installment_number as month,
    cas.due_date,
    cas.amount_due as installment_amount,
    cas.payment_status,
    cas.paid_amount as amount_paid_so_far,
    cas.remaining_balance as still_owes,
    DATEDIFF(CURDATE(), cas.due_date) as days_overdue,
    cas.late_fee as current_late_fee,
    -- Calculate what late fee SHOULD be
    CASE 
        WHEN DATEDIFF(CURDATE(), cas.due_date) >= 7 AND cas.payment_status != 'Paid' AND cas.late_fee = 0
        THEN ROUND(cas.remaining_balance * 0.03, 2)
        ELSE 0
    END as should_apply_late_fee,
    CASE 
        WHEN DATEDIFF(CURDATE(), cas.due_date) >= 7 AND cas.payment_status != 'Paid'
        THEN 'YES - 7+ DAYS LATE'
        WHEN DATEDIFF(CURDATE(), cas.due_date) > 0 AND DATEDIFF(CURDATE(), cas.due_date) < 7
        THEN 'NO - LESS THAN 7 DAYS'
        WHEN DATEDIFF(CURDATE(), cas.due_date) <= 0
        THEN 'NO - NOT YET DUE'
        WHEN cas.payment_status = 'Paid'
        THEN 'NO - ALREADY PAID'
        ELSE 'UNKNOWN'
    END as late_fee_eligible
FROM customer_account_schedule cas
WHERE cas.account_id = (
    SELECT cab.id FROM customer_account_balances cab ORDER BY cab.created_at DESC LIMIT 1
)
ORDER BY cas.installment_number;

-- ========================================================================
-- STEP 8: BALANCE VERIFICATION
-- ========================================================================

-- Verify account balance matches transaction sum
SELECT 
    cab.id as account_id,
    cab.item_purchased,
    cab.total_price as stored_total,
    cab.paid_amount as stored_paid,
    cab.remaining_balance as stored_remaining,
    -- Calculate from transactions
    COALESCE(SUM(cat.amount), 0) as sum_transactions,
    -- Calculate what remaining should be
    GREATEST(0, cab.total_price - COALESCE(SUM(cat.amount), 0)) as calculated_remaining,
    -- Check if they match
    CASE 
        WHEN cab.paid_amount = COALESCE(SUM(cat.amount), 0) AND 
             cab.remaining_balance = GREATEST(0, cab.total_price - COALESCE(SUM(cat.amount), 0))
        THEN 'CORRECT ✓'
        ELSE 'MISMATCH ⚠️'
    END as status
FROM customer_account_balances cab
LEFT JOIN customer_account_transactions cat ON cab.id = cat.account_id
WHERE cab.id = (
    SELECT cab.id FROM customer_account_balances cab ORDER BY cab.created_at DESC LIMIT 1
)
GROUP BY cab.id;

-- ========================================================================
-- STEP 9: LATE FEE CALCULATION FORMULA VERIFICATION
-- ========================================================================

-- Show the formula in action
SELECT 
    'LATE FEE FORMULA TEST' as test_name,
    (SELECT monthly_payment_amount FROM customer_account_balances 
     WHERE id = (SELECT cab.id FROM customer_account_balances cab ORDER BY cab.created_at DESC LIMIT 1)
    ) as monthly_amount,
    ROUND(
        (SELECT monthly_payment_amount FROM customer_account_balances 
         WHERE id = (SELECT cab.id FROM customer_account_balances cab ORDER BY cab.created_at DESC LIMIT 1)
        ) * 0.03, 2
    ) as late_fee_3_percent,
    'If payment is 7+ days late, add this fee' as note;

-- ========================================================================
-- REFERENCE: TEST SCENARIOS
-- ========================================================================
--
-- Scenario 1: ON-TIME PAYMENT
--   Due Date: 2025-01-15
--   Payment Date: 2025-01-15
--   Days Late: 0
--   Late Fee: ₱0.00 ✓
--
-- Scenario 2: EARLY PAYMENT
--   Due Date: 2025-01-15
--   Payment Date: 2025-01-10
--   Days Late: -5 (EARLY)
--   Late Fee: ₱0.00 ✓
--
-- Scenario 3: SLIGHTLY LATE (< 7 days)
--   Due Date: 2025-01-15
--   Payment Date: 2025-01-20
--   Days Late: 5
--   Late Fee: ₱0.00 (no fee < 7 days) ✓
--
-- Scenario 4: LATE (7+ days)
--   Due Date: 2025-01-15
--   Payment Date: 2025-01-25
--   Days Late: 10
--   Late Fee: ₱131.25 (assuming 4,375 monthly × 3%) ✓
--
-- ========================================================================
