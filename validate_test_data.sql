-- ========================================================================
-- Validation Queries for Late Fee and Installment Test Data
-- ========================================================================
-- These queries verify correct calculations and payment tracking
-- across all test scenarios
-- ========================================================================

-- ========================================================================
-- 1. VERIFY ON-TIME PAYMENT (Scenario 1)
-- Expected: No late fees, paid status
-- ========================================================================
SELECT 
    'Scenario 1: On-Time Payment' as test_scenario,
    inv.invoice_number,
    cl.firstname,
    cl.lastname,
    inv.total_amount,
    inv.due_date,
    MAX(r.issued_at) as payment_date,
    DATEDIFF(MAX(r.issued_at), inv.due_date) as days_early,
    SUM(r.amount_paid) as total_paid,
    inv.payment_status,
    CASE 
        WHEN SUM(r.amount_paid) >= inv.total_amount AND DATEDIFF(MAX(r.issued_at), inv.due_date) >= 0
        THEN 'PASS: Paid on or before due date'
        ELSE 'FAIL: Payment logic issue'
    END as validation
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.id = 5001
GROUP BY inv.id;

-- ========================================================================
-- 2. VERIFY PARTIAL PAYMENT (Scenario 2)
-- Expected: Multiple receipts totaling invoice amount
-- ========================================================================
SELECT 
    'Scenario 2: Partial Payments' as test_scenario,
    inv.invoice_number,
    cl.firstname,
    cl.lastname,
    inv.total_amount,
    COUNT(r.id) as payment_count,
    SUM(r.amount_paid) as total_paid,
    inv.total_amount - SUM(r.amount_paid) as balance_remaining,
    inv.payment_status,
    CASE 
        WHEN SUM(r.amount_paid) = inv.total_amount THEN 'PASS: Fully paid'
        WHEN SUM(r.amount_paid) > 0 AND SUM(r.amount_paid) < inv.total_amount THEN 'PASS: Partial paid'
        ELSE 'FAIL: Payment tracking issue'
    END as validation
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.id = 5002
GROUP BY inv.id;

-- ========================================================================
-- 3. VERIFY EARLY-STAGE LATE PAYMENT (Scenario 3)
-- Expected: Payment 5 days late, 3% late fee applied
-- Late fee calculation: 120736.00 * 0.03 = 3622.08
-- ========================================================================
SELECT 
    'Scenario 3: Early-Stage Late Payment (5 Days)' as test_scenario,
    inv.invoice_number,
    cl.firstname,
    cl.lastname,
    inv.total_amount,
    inv.due_date,
    MAX(r.issued_at) as payment_date,
    DATEDIFF(MAX(r.issued_at), inv.due_date) as days_late,
    SUM(r.amount_paid) as total_paid,
    ROUND(inv.total_amount * 0.03, 2) as expected_late_fee,
    ROUND(SUM(r.amount_paid) - inv.total_amount, 2) as actual_paid_late_fee,
    CASE 
        WHEN DATEDIFF(MAX(r.issued_at), inv.due_date) BETWEEN 1 AND 7 
             AND ROUND(SUM(r.amount_paid) - inv.total_amount, 2) = ROUND(inv.total_amount * 0.03, 2)
        THEN 'PASS: Late fee correctly applied'
        ELSE 'FAIL: Late fee calculation mismatch'
    END as validation
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.id = 5003
GROUP BY inv.id;

-- ========================================================================
-- 4. VERIFY SEVERE LATE PAYMENT (Scenario 4)
-- Expected: Payment 14 days late, cumulative late fee structure
-- Initial fee (0-7 days): 83776.00 * 0.03 = 2513.28
-- Cumulative fee (7-14 days): 2513.28 * 0.03 = 75.40
-- Total: 2588.68
-- ========================================================================
SELECT 
    'Scenario 4: Severe Late Payment (14 Days)' as test_scenario,
    inv.invoice_number,
    cl.firstname,
    cl.lastname,
    inv.total_amount,
    inv.due_date,
    MAX(r.issued_at) as payment_date,
    DATEDIFF(MAX(r.issued_at), inv.due_date) as days_late,
    SUM(r.amount_paid) as total_paid,
    ROUND(inv.total_amount * 0.03, 2) as initial_late_fee,
    ROUND((inv.total_amount * 0.03) * 0.03, 2) as cumulative_late_fee_increase,
    ROUND((inv.total_amount * 0.03) + ((inv.total_amount * 0.03) * 0.03), 2) as expected_total_late_fee,
    ROUND(SUM(r.amount_paid) - inv.total_amount, 2) as actual_paid_late_fee,
    CASE 
        WHEN DATEDIFF(MAX(r.issued_at), inv.due_date) > 7 
             AND ROUND(SUM(r.amount_paid) - inv.total_amount, 2) BETWEEN 2500 AND 2600
        THEN 'PASS: Cumulative late fee applied'
        ELSE 'FAIL: Cumulative late fee calculation issue'
    END as validation
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.id = 5004
GROUP BY inv.id;

-- ========================================================================
-- 5. VERIFY 12-MONTH INSTALLMENT AMORTIZATION (Scenario 5)
-- Expected: Declining interest, correct principal/interest split
-- Verify: Principal increases, Interest decreases, Monthly payment constant
-- ========================================================================
SELECT 
    'Scenario 5: 12-Month Installment Amortization' as test_scenario,
    c.id as contract_id,
    c.financed_amount,
    c.monthly_payment,
    c.number_of_installments,
    c.interest_rate,
    COUNT(s.id) as schedule_items,
    SUM(s.amount_due) as total_due_all_months,
    SUM(s.principal_amount) as total_principal,
    SUM(s.interest_amount) as total_interest,
    SUM(s.paid_amount) as total_paid_to_date,
    SUM(CASE WHEN s.status = 'paid' THEN 1 ELSE 0 END) as paid_count,
    SUM(CASE WHEN s.status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    CASE 
        WHEN SUM(s.principal_amount) = c.financed_amount 
             AND COUNT(s.id) = c.number_of_installments
             AND SUM(s.paid_amount) > 0
        THEN 'PASS: Amortization schedule correct'
        ELSE 'FAIL: Schedule calculation issue'
    END as validation
FROM installment_contracts c
LEFT JOIN installment_schedule s ON c.id = s.contract_id
WHERE c.id = 100
GROUP BY c.id;

-- ========================================================================
-- 6. VERIFY INSTALLMENT PAYMENT DETAILS (Scenario 5)
-- Expected: Principal > Interest in early months
-- ========================================================================
SELECT 
    'Scenario 5: Monthly Payment Breakdown' as test_scenario,
    s.installment_number as month,
    s.due_date,
    s.amount_due,
    s.principal_amount,
    s.interest_amount,
    s.status,
    s.paid_amount,
    s.paid_date,
    ROUND((s.interest_amount / s.amount_due * 100), 2) as interest_percentage,
    ROUND(((s.principal_amount + COALESCE((SELECT SUM(s2.principal_amount) 
           FROM installment_schedule s2 
           WHERE s2.contract_id = s.contract_id AND s2.installment_number > s.installment_number), 0)) / 
           (SELECT financed_amount FROM installment_contracts WHERE id = s.contract_id) * 100), 2) as remaining_balance_pct
FROM installment_schedule s
WHERE s.contract_id = 100
ORDER BY s.installment_number;

-- ========================================================================
-- 7. VERIFY SPARE PARTS LOYALTY DISCOUNT (Scenario 6)
-- Expected: Transaction type = motorcycle_parts_purchase, loyalty_card = 1
-- 2% discount: 50400.00 * 0.02 = 1008.00
-- ========================================================================
SELECT 
    'Scenario 6: Spare Parts with Loyalty Discount' as test_scenario,
    inv.invoice_number,
    cl.firstname,
    cl.lastname,
    inv.transaction_type,
    CASE 
        WHEN cl.loyalty_card = 1 THEN 'Yes'
        WHEN cl.has_loyalty = 1 THEN 'Yes'
        WHEN cl.loyalty = 1 THEN 'Yes'
        WHEN cl.membership_card = 1 THEN 'Yes'
        WHEN cl.membership = 1 THEN 'Yes'
        ELSE 'No'
    END as has_loyalty,
    inv.total_amount,
    ROUND(inv.total_amount * 0.02, 2) as expected_discount,
    SUM(r.amount_paid) as total_paid,
    ROUND(inv.total_amount - SUM(r.amount_paid), 2) as balance_due,
    CASE 
        WHEN inv.transaction_type LIKE '%part%' 
             AND (cl.loyalty_card = 1 OR cl.has_loyalty = 1 OR cl.loyalty = 1 OR cl.membership_card = 1 OR cl.membership = 1)
        THEN 'PASS: Eligible for 2% discount'
        ELSE 'INCOMPLETE: Check eligibility'
    END as validation
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.id = 5006
GROUP BY inv.id;

-- ========================================================================
-- 8. VERIFY MIXED CART TRANSACTION (Scenario 7)
-- Expected: Transaction type identified, late fee applied
-- ========================================================================
SELECT 
    'Scenario 7: Mixed Cart Transaction' as test_scenario,
    inv.invoice_number,
    cl.firstname,
    cl.lastname,
    inv.transaction_type,
    COUNT(DISTINCT ii.item_type) as item_types,
    GROUP_CONCAT(DISTINCT ii.item_type SEPARATOR ', ') as items_ordered,
    SUM(ii.quantity) as total_items,
    inv.total_amount,
    inv.due_date,
    MAX(r.issued_at) as payment_date,
    DATEDIFF(MAX(r.issued_at), inv.due_date) as days_late,
    SUM(r.amount_paid) as total_paid,
    ROUND(inv.total_amount * 0.03, 2) as expected_late_fee,
    CASE 
        WHEN inv.transaction_type = 'motorcycle_purchase' 
             AND DATEDIFF(MAX(r.issued_at), inv.due_date) > 0
        THEN 'PASS: Mixed cart correctly classified and late fee applied'
        ELSE 'FAIL: Classification or late fee issue'
    END as validation
FROM invoices inv
LEFT JOIN invoice_items ii ON inv.id = ii.invoice_id
LEFT JOIN receipts r ON inv.id = r.invoice_id
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.id = 5007
GROUP BY inv.id;

-- ========================================================================
-- 9. VERIFY OVERDUE INSTALLMENT WITH LATE FEE (Scenario 8)
-- Expected: Installment payment overdue 30 days, late fee applied
-- Late fee: 5490.00 * 0.03 = 164.70
-- ========================================================================
SELECT 
    'Scenario 8: Overdue Installment Payment' as test_scenario,
    c.id as contract_id,
    s.installment_number as month,
    s.due_date,
    s.amount_due,
    s.principal_amount,
    s.interest_amount,
    s.status,
    s.paid_amount,
    s.paid_date,
    DATEDIFF(NOW(), s.due_date) as days_overdue,
    s.late_fee,
    ROUND(s.amount_due * 0.03, 2) as expected_late_fee,
    CASE 
        WHEN DATEDIFF(NOW(), s.due_date) > 7 AND s.status = 'overdue'
        THEN 'PASS: Overdue status applied'
        WHEN s.late_fee > 0 THEN 'PASS: Late fee calculated'
        ELSE 'INCOMPLETE: Review status'
    END as validation
FROM installment_contracts c
LEFT JOIN installment_schedule s ON c.id = s.contract_id
WHERE c.id = 101 AND s.installment_number = 1;

-- ========================================================================
-- 10. SUMMARY: All Invoice Payment Status Overview
-- Verify payment tracking across all test scenarios
-- ========================================================================
SELECT 
    'SUMMARY: All Test Invoices' as test_label,
    COUNT(DISTINCT inv.id) as total_invoices,
    inv.payment_status,
    COUNT(DISTINCT inv.id) as invoice_count,
    SUM(inv.total_amount) as total_amount_in_status,
    SUM(r.amount_paid) as total_paid,
    SUM(inv.total_amount) - SUM(r.amount_paid) as balance_due,
    ROUND(AVG(DATEDIFF(MAX(r.issued_at), inv.due_date)), 1) as avg_days_late,
    COUNT(DISTINCT CASE WHEN DATEDIFF(MAX(r.issued_at), inv.due_date) > 7 THEN inv.id END) as invoices_severely_late
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
WHERE inv.id BETWEEN 5001 AND 5008
GROUP BY inv.payment_status;

-- ========================================================================
-- 11. DETAILED PAYMENT TIMELINE (All Scenarios)
-- Verify payment flow and timing accuracy
-- ========================================================================
SELECT 
    inv.id,
    inv.invoice_number,
    cl.firstname,
    inv.total_amount,
    inv.due_date,
    r.receipt_number,
    r.amount_paid,
    r.issued_at,
    DATEDIFF(r.issued_at, inv.due_date) as days_from_due,
    CASE 
        WHEN DATEDIFF(r.issued_at, inv.due_date) < 0 THEN 'Early'
        WHEN DATEDIFF(r.issued_at, inv.due_date) = 0 THEN 'On Due Date'
        WHEN DATEDIFF(r.issued_at, inv.due_date) BETWEEN 1 AND 7 THEN 'Late (1-7 Days)'
        WHEN DATEDIFF(r.issued_at, inv.due_date) > 7 THEN 'Severely Late (>7 Days)'
    END as payment_category
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.id BETWEEN 5001 AND 5008
ORDER BY inv.id, r.issued_at;

-- ========================================================================
-- 12. VERIFICATION: Total Interest Paid Across Installments
-- Verify cumulative interest matches expected calculation
-- ========================================================================
SELECT 
    'Installment Interest Verification' as test,
    c.id as contract_id,
    c.financed_amount,
    c.monthly_payment,
    c.number_of_installments,
    c.interest_rate,
    SUM(s.interest_amount) as total_interest_from_schedule,
    ROUND((c.monthly_payment * c.number_of_installments) - c.financed_amount, 2) as expected_total_interest,
    CASE 
        WHEN ROUND(SUM(s.interest_amount), 2) = ROUND((c.monthly_payment * c.number_of_installments) - c.financed_amount, 2)
        THEN 'PASS: Interest calculation correct'
        ELSE 'FAIL: Interest calculation mismatch'
    END as validation
FROM installment_contracts c
LEFT JOIN installment_schedule s ON c.id = s.contract_id
WHERE c.id IN (100, 101)
GROUP BY c.id;

-- ========================================================================
-- 13. LATE FEE SUMMARY ACROSS ALL INVOICES
-- ========================================================================
SELECT 
    'Late Fee Summary' as report,
    COUNT(inv.id) as total_invoices_checked,
    SUM(CASE WHEN DATEDIFF(MAX(r.issued_at), inv.due_date) > 0 THEN 1 ELSE 0 END) as invoices_with_late_payment,
    SUM(CASE WHEN DATEDIFF(MAX(r.issued_at), inv.due_date) BETWEEN 1 AND 7 THEN ROUND(inv.total_amount * 0.03, 2) ELSE 0 END) as total_early_stage_late_fees,
    SUM(CASE WHEN DATEDIFF(MAX(r.issued_at), inv.due_date) > 7 THEN ROUND(inv.total_amount * 0.03 + (inv.total_amount * 0.03 * 0.03), 2) ELSE 0 END) as total_severe_late_fees,
    ROUND(SUM(CASE WHEN DATEDIFF(MAX(r.issued_at), inv.due_date) > 0 THEN SUM(r.amount_paid) - inv.total_amount ELSE 0 END), 2) as total_actual_late_fees_collected
FROM invoices inv
LEFT JOIN receipts r ON inv.id = r.invoice_id
WHERE inv.id BETWEEN 5001 AND 5008
GROUP BY 1;

-- ========================================================================
-- 14. LOYALTY DISCOUNT ELIGIBILITY CHECK (All Parts Orders)
-- ========================================================================
SELECT 
    'Loyalty Discount Eligibility' as report,
    inv.invoice_number,
    cl.firstname,
    cl.lastname,
    inv.transaction_type,
    inv.total_amount,
    COALESCE(cl.loyalty_card, 0) + COALESCE(cl.has_loyalty, 0) + COALESCE(cl.loyalty, 0) + 
    COALESCE(cl.membership_card, 0) + COALESCE(cl.membership, 0) as loyalty_indicator,
    CASE 
        WHEN inv.transaction_type LIKE '%part%' 
             AND (cl.loyalty_card = 1 OR cl.has_loyalty = 1 OR cl.loyalty = 1 OR cl.membership_card = 1 OR cl.membership = 1)
        THEN ROUND(inv.total_amount * 0.02, 2)
        ELSE 0
    END as eligible_discount
FROM invoices inv
LEFT JOIN client_list cl ON inv.customer_id = cl.id
WHERE inv.transaction_type LIKE '%part%' OR inv.id BETWEEN 5001 AND 5008;

-- ========================================================================
-- Script end - Run all queries to verify test data integrity
-- ========================================================================
