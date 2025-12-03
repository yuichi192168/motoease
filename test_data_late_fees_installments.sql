-- ========================================================================
-- Test Data for Late Fee Calculations and Installment Payment Logic
-- Using EXISTING database users and clients
-- ========================================================================
-- This script inserts realistic sample data to validate:
-- 1. Late fee calculations (3% increase after 7 days overdue)
-- 2. Scheduled payment logic and tracking
-- 3. Installment computations with correct interest and amortization
-- 4. Various payment scenarios (on-time, partial, multiple late cycles)
-- 5. Mixed cart transactions and payment application
-- ========================================================================

-- Existing Users (Admin/Staff):
-- User ID 10: Henry Legaspi (admin)
-- User ID 11: Euniel Bandian (service_admin)
-- User ID 12: Mark Pancho (inventory)
-- User ID 13: Joshua Cansino (inventory)
-- User ID 14: Karen Bautista (service_admin)

-- Existing Clients:
-- Client ID 2: Jazmine Cruz
-- Client ID 3: Joshara Carasig
-- Client ID 6: Lyra Jamaica Vergara
-- Client ID 8: Aljay Plantado

-- ========================================================================
-- SCENARIO 1: On-Time Payment (No Late Fees)
-- Customer: Jazmine Cruz (Client ID: 2)
-- Admin: Henry Legaspi (User ID: 10)
-- ========================================================================

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5001, 'ORD-TEST-001', 2, 132800.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50001, 5001, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Invoice for on-time payment
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5001, 5001, 'INV-TEST-001', 2, 'motorcycle_purchase', 'cash', 132800.00, 15936.00, 148736.00, 'paid', 'Store', 'Pay in-store', 10, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50001, 5001, 'motorcycle', 5, 'Honda PCX160 CBS', 1, 132800.00, 132800.00)
ON DUPLICATE KEY UPDATE id=id;

-- On-time receipt (paid 2 days before due date)
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5001, 5001, 'RCPT-TEST-001', 2, 148736.00, 'cash', 'ON-TIME-PAYMENT', 10, 'Payment received on time', DATE_SUB(NOW(), INTERVAL 8 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- SCENARIO 2: Partial Payment (Multiple Payment Cycles)
-- Customer: Joshara Carasig (Client ID: 3)
-- Admin: Henry Legaspi (User ID: 10)
-- ========================================================================

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5002, 'ORD-TEST-002', 3, 110800.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 20 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50002, 5002, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Invoice for partial payment scenario
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5002, 5002, 'INV-TEST-002', 3, 'motorcycle_purchase', 'cash', 110800.00, 13296.00, 124096.00, 'partial', 'Store', 'Pay in-store', 10, DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50002, 5002, 'motorcycle', 5, 'Honda PCX160 CBS', 1, 110800.00, 110800.00)
ON DUPLICATE KEY UPDATE id=id;

-- First partial receipt (50% of total) - on time
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5002, 5002, 'RCPT-TEST-002A', 3, 62048.00, 'cash', 'PARTIAL-PAYMENT-1', 10, 'Partial payment 1 of 2', DATE_SUB(NOW(), INTERVAL 12 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- Second partial receipt (remaining 50%) - 5 days later
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5003, 5002, 'RCPT-TEST-002B', 3, 62048.00, 'cash', 'PARTIAL-PAYMENT-2', 10, 'Partial payment 2 of 2 - COMPLETE', DATE_SUB(NOW(), INTERVAL 7 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- SCENARIO 3: Late Payment with Early Stage Late Fee (1-7 Days Overdue)
-- Customer: Lyra Jamaica Vergara (Client ID: 6)
-- Admin: Henry Legaspi (User ID: 10)
-- Late fee = 3% of outstanding amount
-- ========================================================================

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5003, 'ORD-TEST-003', 6, 107800.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 25 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50003, 5003, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Invoice with due date 18 days ago
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5003, 5003, 'INV-TEST-003', 6, 'motorcycle_purchase', 'cash', 107800.00, 12936.00, 120736.00, 'late', 'Store', 'Pay in-store', 10, DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50003, 5003, 'motorcycle', 5, 'Honda PCX160 CBS', 1, 107800.00, 107800.00)
ON DUPLICATE KEY UPDATE id=id;

-- Payment 5 days after due date (within 7-day early stage)
-- Late fee = 120736.00 * 0.03 = 3622.08
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5004, 5003, 'RCPT-TEST-003', 6, 124358.08, 'cash', 'LATE-PAYMENT-5DAYS', 10, 'Payment 5 days late - 3% late fee applied (₱3,622.08)', DATE_SUB(NOW(), INTERVAL 13 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- SCENARIO 4: Severe Late Payment (>7 Days Overdue, Multiple Late Cycles)
-- Customer: Aljay Plantado (Client ID: 8)
-- Admin: Henry Legaspi (User ID: 10)
-- Late fee = 3% + 3% cumulative late fee structure
-- ========================================================================

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5004, 'ORD-TEST-004', 8, 74800.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 35 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50004, 5004, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Invoice with due date 28 days ago (far overdue)
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5004, 5004, 'INV-TEST-004', 8, 'motorcycle_purchase', 'cash', 74800.00, 8976.00, 83776.00, 'late', 'Store', 'Pay in-store', 10, DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 28 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50004, 5004, 'motorcycle', 5, 'Honda PCX160 CBS', 1, 74800.00, 74800.00)
ON DUPLICATE KEY UPDATE id=id;

-- Payment 14 days after due date (triggers increased late fee)
-- Initial late fee (first 7 days): 83776.00 * 0.03 = 2513.28
-- Cumulative late fee (additional 7 days): 2513.28 * 0.03 = 75.40
-- Total late fees: 2513.28 + 75.40 = 2588.68
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5005, 5004, 'RCPT-TEST-004', 8, 86364.68, 'cash', 'LATE-PAYMENT-14DAYS', 10, 'Payment 14 days late - Cumulative late fees applied (₱2,588.68)', DATE_SUB(NOW(), INTERVAL 14 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- SCENARIO 5: 12-Month Installment Plan with Correct Amortization
-- Customer: Jazmine Cruz (Client ID: 2)
-- Admin: Henry Legaspi (User ID: 10)
-- Motorcycle: PCX160 (SRP: 132,800) with 2% monthly interest
-- Down payment: 39,900 | Financed: 92,900
-- ========================================================================

INSERT INTO `installment_plans` (`id`, `plan_name`, `plan_description`, `number_of_installments`, `down_payment_percentage`, `interest_rate`, `down_payment_fixed_amount`, `status`, `date_created`) VALUES
(100, '12 Months Installment', '12 monthly payments with 2% monthly interest', 12, 0, 2.00, 39900.00, 'active', NOW())
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5005, 'ORD-TEST-005', 2, 132800.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50005, 5005, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Installment invoice
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5005, 5005, 'INV-TEST-005', 2, 'motorcycle_purchase', 'installment', 132800.00, 0.00, 172841.00, 'partial', 'Store', 'Installment plan', 10, DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 23 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50005, 5005, 'motorcycle', 5, 'Honda PCX160 CBS', 1, 132800.00, 132800.00)
ON DUPLICATE KEY UPDATE id=id;

-- Create installment contract
INSERT INTO `installment_contracts` (`id`, `order_id`, `customer_id`, `invoice_id`, `installment_plan_id`, `total_amount`, `down_payment`, `financed_amount`, `monthly_payment`, `number_of_installments`, `interest_rate`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(100, 5005, 2, 5005, 100, 172841.00, 39900.00, 92900.00, 11039.00, 12, 2.00, 'active', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 30 DAY), INTERVAL 12 MONTH), DATE_SUB(NOW(), INTERVAL 30 DAY), NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Insert installment schedule for 12 months with amortization
-- Monthly payment: ₱11,039.00
-- Using declining balance amortization
INSERT INTO `installment_schedule` (`id`, `contract_id`, `installment_number`, `due_date`, `amount_due`, `principal_amount`, `interest_amount`, `status`, `paid_amount`, `paid_date`, `late_fee`) VALUES
(1001, 100, 1, DATE_SUB(NOW(), INTERVAL 23 DAY), 11039.00, 9775.00, 1264.00, 'paid', 11039.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 0.00),
(1002, 100, 2, DATE_SUB(NOW(), INTERVAL 16 DAY), 11039.00, 9930.00, 1109.00, 'paid', 11039.00, DATE_SUB(NOW(), INTERVAL 15 DAY), 0.00),
(1003, 100, 3, DATE_SUB(NOW(), INTERVAL 9 DAY), 11039.00, 10088.00, 951.00, 'paid', 11039.00, DATE_SUB(NOW(), INTERVAL 8 DAY), 0.00),
(1004, 100, 4, DATE_SUB(NOW(), INTERVAL 2 DAY), 11039.00, 10250.00, 789.00, 'paid', 11039.00, NOW(), 0.00),
(1005, 100, 5, DATE_ADD(NOW(), INTERVAL 5 DAY), 11039.00, 10416.00, 623.00, 'pending', 0.00, NULL, 0.00),
(1006, 100, 6, DATE_ADD(NOW(), INTERVAL 12 DAY), 11039.00, 10586.00, 453.00, 'pending', 0.00, NULL, 0.00),
(1007, 100, 7, DATE_ADD(NOW(), INTERVAL 19 DAY), 11039.00, 10760.00, 279.00, 'pending', 0.00, NULL, 0.00),
(1008, 100, 8, DATE_ADD(NOW(), INTERVAL 26 DAY), 11039.00, 10760.00, 279.00, 'pending', 0.00, NULL, 0.00),
(1009, 100, 9, DATE_ADD(NOW(), INTERVAL 33 DAY), 11039.00, 10760.00, 279.00, 'pending', 0.00, NULL, 0.00),
(1010, 100, 10, DATE_ADD(NOW(), INTERVAL 40 DAY), 11039.00, 10760.00, 279.00, 'pending', 0.00, NULL, 0.00),
(1011, 100, 11, DATE_ADD(NOW(), INTERVAL 47 DAY), 11039.00, 10760.00, 279.00, 'pending', 0.00, NULL, 0.00),
(1012, 100, 12, DATE_ADD(NOW(), INTERVAL 54 DAY), 11039.00, 10760.00, 279.00, 'pending', 0.00, NULL, 0.00)
ON DUPLICATE KEY UPDATE id=id;

-- Insert down payment receipt for installment plan
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5006, 5005, 'RCPT-TEST-005', 2, 39900.00, 'cash', 'DOWN-PAYMENT-INST', 10, 'Down payment for 12-month installment plan', DATE_SUB(NOW(), INTERVAL 30 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- SCENARIO 6: Spare Parts Order with Loyalty Discount
-- Customer: Joshara Carasig (Client ID: 3)
-- Admin: Henry Legaspi (User ID: 10)
-- Mixed cart: spare parts totaling ₱45,000 with 2% loyalty discount
-- ========================================================================

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5006, 'ORD-TEST-006', 3, 45000.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

-- Spare parts items
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50006, 5006, 5, 2),
(50007, 5006, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Invoice for spare parts with loyalty discount consideration
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5006, 5006, 'INV-TEST-006', 3, 'motorcycle_parts_purchase', 'cash', 45000.00, 5400.00, 50400.00, 'partial', 'Store', 'Pay in-store', 10, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50006, 5006, 'parts', 5, 'Spare Parts Assortment', 3, 15000.00, 45000.00)
ON DUPLICATE KEY UPDATE id=id;

-- Partial payment on spare parts (should show 2% loyalty discount in UI)
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5007, 5006, 'RCPT-TEST-006', 3, 25200.00, 'cash', 'PARTS-PAYMENT-1', 10, 'Partial payment on spare parts (with loyalty card discount: ₱1,008)', DATE_SUB(NOW(), INTERVAL 2 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- SCENARIO 7: Mixed Cart Transaction (Motorcycle + Parts + Oil)
-- Customer: Lyra Jamaica Vergara (Client ID: 6)
-- Admin: Henry Legaspi (User ID: 10)
-- Verification of transaction type determination and interest calculations
-- ========================================================================

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5007, 'ORD-TEST-007', 6, 158500.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 12 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50008, 5007, 5, 1),
(50009, 5007, 5, 2),
(50010, 5007, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- Mixed transaction invoice
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5007, 5007, 'INV-TEST-007', 6, 'motorcycle_purchase', 'cash', 158300.00, 18996.00, 177296.00, 'late', 'Store', 'Pay in-store', 10, DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50007, 5007, 'motorcycle', 5, 'Honda PCX160 CBS', 1, 132800.00, 132800.00),
(50008, 5007, 'parts', 5, 'Spare Parts', 2, 12500.00, 25000.00),
(50009, 5007, 'oil', 5, 'Genuine Oil', 1, 500.00, 500.00)
ON DUPLICATE KEY UPDATE id=id;

-- Payment 7 days after due date (triggers 3% late fee)
-- Late fee: 177296.00 * 0.03 = 5318.88
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5008, 5007, 'RCPT-TEST-007', 6, 182614.88, 'cash', 'MIXED-CART-LATE', 10, 'Mixed transaction payment - 7 days late (3% late fee: ₱5,318.88)', DATE_SUB(NOW(), INTERVAL 5 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- SCENARIO 8: Overdue Installment with Late Fee Application
-- Customer: Aljay Plantado (Client ID: 8)
-- Admin: Henry Legaspi (User ID: 10)
-- Installment payment missed, late fee applies to next payment
-- ========================================================================

INSERT INTO `order_list` (`id`, `ref_code`, `client_id`, `total_amount`, `status`, `requires_credit`, `agreed_to_terms`, `date_created`, `date_updated`, `delete_flag`) VALUES
(5008, 'ORD-TEST-008', 8, 107800.00, 3, 1, 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(50011, 5008, 5, 1)
ON DUPLICATE KEY UPDATE id=id;

-- 24-month installment plan invoice
INSERT INTO `invoices` (`id`, `order_id`, `invoice_number`, `customer_id`, `transaction_type`, `payment_type`, `subtotal`, `vat_amount`, `total_amount`, `payment_status`, `pickup_location`, `payment_instructions`, `generated_by`, `generated_at`, `due_date`, `updated_at`, `archive_flag`, `delete_flag`) VALUES
(5008, 5008, 'INV-TEST-008', 8, 'motorcycle_purchase', 'installment', 107800.00, 0.00, 135228.00, 'partial', 'Store', 'Installment plan', 10, DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(NOW(), INTERVAL 38 DAY), NOW(), 0, 0)
ON DUPLICATE KEY UPDATE id=id;

INSERT INTO `invoice_items` (`id`, `invoice_id`, `item_type`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(50011, 5008, 'motorcycle', 5, 'Honda PCX160 CBS', 1, 107800.00, 107800.00)
ON DUPLICATE KEY UPDATE id=id;

-- 24-month installment contract with 5% interest
INSERT INTO `installment_contracts` (`id`, `order_id`, `customer_id`, `invoice_id`, `installment_plan_id`, `total_amount`, `down_payment`, `financed_amount`, `monthly_payment`, `number_of_installments`, `interest_rate`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(101, 5008, 8, 5008, 5, 135228.00, 32400.00, 102828.00, 5490.00, 24, 5.00, 'active', DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 45 DAY), INTERVAL 24 MONTH), DATE_SUB(NOW(), INTERVAL 45 DAY), NOW())
ON DUPLICATE KEY UPDATE id=id;

-- Insert installment schedule with one overdue payment
INSERT INTO `installment_schedule` (`id`, `contract_id`, `installment_number`, `due_date`, `amount_due`, `principal_amount`, `interest_amount`, `status`, `paid_amount`, `paid_date`, `late_fee`) VALUES
(2001, 101, 1, DATE_SUB(NOW(), INTERVAL 30 DAY), 5490.00, 4248.00, 1242.00, 'overdue', 0.00, NULL, 164.70),
(2002, 101, 2, DATE_SUB(NOW(), INTERVAL 23 DAY), 5490.00, 4312.00, 1178.00, 'paid', 5490.00, DATE_SUB(NOW(), INTERVAL 22 DAY), 0.00),
(2003, 101, 3, DATE_SUB(NOW(), INTERVAL 16 DAY), 5490.00, 4377.00, 1113.00, 'paid', 5490.00, DATE_SUB(NOW(), INTERVAL 15 DAY), 0.00),
(2004, 101, 4, DATE_SUB(NOW(), INTERVAL 9 DAY), 5490.00, 4445.00, 1045.00, 'paid', 5490.00, DATE_SUB(NOW(), INTERVAL 8 DAY), 0.00),
(2005, 101, 5, DATE_SUB(NOW(), INTERVAL 2 DAY), 5490.00, 4515.00, 975.00, 'pending', 0.00, NULL, 0.00)
ON DUPLICATE KEY UPDATE id=id;

-- Down payment receipt
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5009, 5008, 'RCPT-TEST-008', 8, 32400.00, 'cash', 'DOWN-PAYMENT-24M', 10, 'Down payment for 24-month plan', DATE_SUB(NOW(), INTERVAL 45 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- Payment for overdue installment (1 month late) with late fee penalty
-- Original: ₱5,490.00 + Late fee (3%): ₱164.70 = ₱5,654.70
INSERT INTO `receipts` (`id`, `invoice_id`, `receipt_number`, `customer_id`, `amount_paid`, `payment_method`, `payment_reference`, `received_by`, `acknowledgment_note`, `issued_at`, `archive_flag`) VALUES
(5010, 5008, 'RCPT-TEST-008-INST1', 8, 5654.70, 'cash', 'OVERDUE-INST-PAYMENT', 10, 'Installment 1 payment (30 days late) - includes 3% late fee (₱164.70)', DATE_SUB(NOW(), INTERVAL 22 DAY), 0)
ON DUPLICATE KEY UPDATE id=id;

-- ========================================================================
-- Test data summary comments
-- ========================================================================
-- Generated test scenarios using EXISTING database users and clients:
-- 1. Scenario 5001: On-time payment (Client: Jazmine Cruz - no late fees)
-- 2. Scenario 5002: Partial payments across 2 cycles (Client: Joshara Carasig)
-- 3. Scenario 5003: Early-stage late (5 days, 3% late fee = ₱3,622.08) (Client: Lyra Jamaica Vergara)
-- 4. Scenario 5004: Severe late (14 days, cumulative late fees = ₱2,588.68) (Client: Aljay Plantado)
-- 5. Scenario 5005: 12-month installment (4 paid, 8 pending, declining interest) (Client: Jazmine Cruz)
-- 6. Scenario 5006: Spare parts with 2% loyalty discount (Client: Joshara Carasig)
-- 7. Scenario 5007: Mixed cart (motorcycle + parts + oil) with 7-day late fee (Client: Lyra Jamaica Vergara)
-- 8. Scenario 5008: Overdue installment with per-payment late fees (Client: Aljay Plantado)
--
-- Admin User (All scenarios): Henry Legaspi (User ID: 10)
--
-- Expected calculations to verify:
-- - On-time: 0% late fees applied
-- - Partial: Full payment achieved across two receipts
-- - Early late: (120736.00 * 0.03) = 3622.08 late fee
-- - Severe late: Initial (83776.00 * 0.03) + Cumulative (75.40) = 2588.68
-- - Installment: 12 payments of 11,039.00 total 132,468 with declining interest
-- - Parts loyalty: 2% discount applied (50400.00 * 0.02 = 1,008.00)
-- - Mixed: Transaction type correctly identified as motorcycle_purchase
-- - Overdue inst: Installment payment late fee (5490.00 * 0.03 = 164.70)
-- ========================================================================

COMMIT;
