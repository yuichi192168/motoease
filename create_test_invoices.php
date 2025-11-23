<?php
/**
 * Create Test Invoices for Verification
 * Creates sample invoices with Interest, Late Fee, and Arrears for testing
 */

require_once('config.php');

echo "=== Creating Test Invoices for Calculation Verification ===\n\n";

// Get first customer
$customer = $conn->query("SELECT id FROM client_list LIMIT 1")->fetch_assoc();
if(!$customer) {
    echo "✗ No customers found. Please create a customer first.\n";
    exit(1);
}
$customer_id = $customer['id'];
echo "✓ Using customer ID: {$customer_id}\n\n";

// Ensure invoice settings exist
echo "1. Ensuring invoice settings exist...\n";
$settings = [
    ['late_fee_daily_rate', '0.50', 'Late fee daily rate in percent (0.5%/day)'],
    ['penalty_rate_monthly', '3.00', 'Penalty rate per month (3%/month)'],
    ['penalty_grace_period_days', '7', 'Grace period in days before penalty applies']
];

foreach($settings as $setting) {
    $sql = "INSERT INTO invoice_settings (setting_key, setting_value, description) 
            VALUES ('{$setting[0]}', '{$setting[1]}', '{$setting[2]}')
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
    $conn->query($sql);
    echo "   ✓ Setting '{$setting[0]}' = {$setting[1]}\n";
}
echo "\n";

// Test Invoice 1: Non-installment with Late Fee
echo "2. Creating Test Invoice #1 (Non-installment with Late Fee)...\n";
$order1 = [
    'client_id' => $customer_id,
    'total_amount' => 30000.00,
    'status' => 1,
    'date_created' => date('Y-m-d H:i:s', strtotime('-20 days'))
];
$order_fields = implode(',', array_keys($order1));
$order_values = "'" . implode("','", array_values($order1)) . "'";
$order_sql = "INSERT INTO order_list ({$order_fields}) VALUES ({$order_values})";
$conn->query($order_sql);
$order_id1 = $conn->insert_id;

$invoice1 = [
    'order_id' => $order_id1,
    'invoice_number' => 'TEST-LATE-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
    'customer_id' => $customer_id,
    'transaction_type' => 'motorcycle_purchase',
    'payment_type' => 'cash', // Non-installment to trigger late fees
    'subtotal' => 30000.00,
    'vat_amount' => 0,
    'total_amount' => 30000.00,
    'payment_status' => 'pending',
    'pickup_location' => 'Main Store',
    'payment_instructions' => 'Payment must be completed in-store',
    'generated_by' => 1,
    'generated_at' => date('Y-m-d H:i:s', strtotime('-20 days')),
    'due_date' => date('Y-m-d', strtotime('-10 days')) // 10 days overdue
];

$inv_fields = implode(',', array_keys($invoice1));
$inv_values = "'" . implode("','", array_values($invoice1)) . "'";
$conn->query("INSERT INTO invoices ({$inv_fields}) VALUES ({$inv_values})");
$invoice_id1 = $conn->insert_id;

// Add partial payment to show balance
$conn->query("INSERT INTO receipts (invoice_id, receipt_number, customer_id, amount_paid, payment_method, received_by, archive_flag, issued_at) 
              VALUES ('{$invoice_id1}', 'RCPT-TEST-1', '{$customer_id}', 5000.00, 'cash', 1, 0, '" . date('Y-m-d H:i:s', strtotime('-15 days')) . "')");

echo "   ✓ Invoice #1 created: {$invoice1['invoice_number']}\n";
echo "   ✓ Total: ₱30,000.00, Paid: ₱5,000.00, Balance: ₱25,000.00\n";
echo "   ✓ Due Date: {$invoice1['due_date']} (10 days overdue)\n";
echo "   ✓ Expected Late Fee: ~₱" . number_format(25000 * 0.005 * 10, 2) . " (0.5%/day × 10 days × ₱25,000)\n\n";

// Test Invoice 2: Installment with Interest and Arrears
echo "3. Creating Test Invoice #2 (Installment with Interest and Arrears)...\n";
$order2 = [
    'client_id' => $customer_id,
    'total_amount' => 50000.00,
    'status' => 1,
    'date_created' => date('Y-m-d H:i:s', strtotime('-60 days'))
];
$order_fields = implode(',', array_keys($order2));
$order_values = "'" . implode("','", array_values($order2)) . "'";
$conn->query("INSERT INTO order_list ({$order_fields}) VALUES ({$order_values})");
$order_id2 = $conn->insert_id;

$invoice2 = [
    'order_id' => $order_id2,
    'invoice_number' => 'TEST-INST-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
    'customer_id' => $customer_id,
    'transaction_type' => 'motorcycle_purchase',
    'payment_type' => 'installment',
    'subtotal' => 50000.00,
    'vat_amount' => 0,
    'total_amount' => 50000.00,
    'payment_status' => 'partial',
    'pickup_location' => 'Main Store',
    'payment_instructions' => 'Payment must be completed in-store',
    'generated_by' => 1,
    'generated_at' => date('Y-m-d H:i:s', strtotime('-60 days')),
    'due_date' => date('Y-m-d', strtotime('-30 days'))
];

$inv_fields = implode(',', array_keys($invoice2));
$inv_values = "'" . implode("','", array_values($invoice2)) . "'";
$conn->query("INSERT INTO invoices ({$inv_fields}) VALUES ({$inv_values})");
$invoice_id2 = $conn->insert_id;

// Add down payment
$conn->query("INSERT INTO receipts (invoice_id, receipt_number, customer_id, amount_paid, payment_method, received_by, archive_flag, issued_at) 
              VALUES ('{$invoice_id2}', 'RCPT-TEST-2', '{$customer_id}', 10000.00, 'cash', 1, 0, '" . date('Y-m-d H:i:s', strtotime('-60 days')) . "')");

// Get or create installment plan
$plan = $conn->query("SELECT id FROM installment_plans LIMIT 1")->fetch_assoc();
if(!$plan) {
    // Create a default installment plan
    $plan_sql = "INSERT INTO installment_plans (plan_name, description, number_of_installments, interest_rate, down_payment_percentage, status) 
                 VALUES ('12 Months Plan', '12 month installment plan', 12, 30.00, 20.00, 'active')";
    $conn->query($plan_sql);
    $plan_id = $conn->insert_id;
    echo "   ✓ Created default installment plan: ID {$plan_id}\n";
} else {
    $plan_id = $plan['id'];
    echo "   ✓ Using installment plan: ID {$plan_id}\n";
}

// Create installment contract
$contract_number = 'CNT-TEST-' . date('Y') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
$start_date = date('Y-m-d', strtotime('-60 days'));
$end_date = date('Y-m-d', strtotime('-60 days +12 months'));

$contract = [
    'contract_number' => $contract_number,
    'invoice_id' => $invoice_id2,
    'customer_id' => $customer_id,
    'installment_plan_id' => $plan_id,
    'total_amount' => 50000.00,
    'down_payment_amount' => 10000.00,
    'remaining_balance' => 40000.00,
    'start_date' => $start_date,
    'end_date' => $end_date,
    'status' => 'active'
];

$contract_fields = implode(',', array_keys($contract));
$contract_values = "'" . implode("','", array_values($contract)) . "'";
$contract_result = $conn->query("INSERT INTO installment_contracts ({$contract_fields}) VALUES ({$contract_values})");

if($contract_result) {
    $contract_id = $conn->insert_id;
    echo "   ✓ Installment contract created: ID {$contract_id}\n";
    
    // Create installment schedule with overdue payments
    $monthly_payment = 40000.00 / 12; // ~3,333.33
    $monthly_interest = 40000.00 * 0.025; // 2.5% = 1,000 per month
    
    for($i = 1; $i <= 12; $i++) {
        $due_date = date('Y-m-d', strtotime("-60 days +{$i} months"));
        $is_overdue = strtotime($due_date) < time();
        $days_overdue = $is_overdue ? max(0, floor((time() - strtotime($due_date)) / 86400)) : 0;
        
        $status = 'pending';
        $paid_amount = 0;
        $penalty_amount = 0;
        
        // First 3 installments are overdue (to trigger arrears)
        if($i <= 3 && $is_overdue) {
            $status = 'overdue';
            // Calculate penalty if past grace period (7 days)
            if($days_overdue >= 7) {
                $months_overdue = ceil($days_overdue / 30.0);
                $penalty_amount = $monthly_payment * 0.03 * $months_overdue; // 3% per month
            }
        }
        
        $schedule = [
            'contract_id' => $contract_id,
            'installment_number' => $i,
            'due_date' => $due_date,
            'amount_due' => $monthly_payment,
            'principal_amount' => $monthly_payment - ($monthly_interest / 12),
            'interest_amount' => $monthly_interest / 12, // ~83.33 per month
            'paid_amount' => $paid_amount,
            'penalty_amount' => $penalty_amount,
            'late_fee' => 0,
            'status' => $status
        ];
        
        $sched_fields = implode(',', array_keys($schedule));
        $sched_values = "'" . implode("','", array_values($schedule)) . "'";
        $conn->query("INSERT INTO installment_schedule ({$sched_fields}) VALUES ({$sched_values})");
        
        if($i <= 3) {
            echo "   ✓ Installment #{$i}: Due {$due_date}, Status: {$status}";
            if($penalty_amount > 0) {
                echo ", Penalty: ₱" . number_format($penalty_amount, 2);
            }
            echo "\n";
        }
    }
    
    echo "   ✓ Invoice #2 created: {$invoice2['invoice_number']}\n";
    echo "   ✓ Total: ₱50,000.00, Down Payment: ₱10,000.00, Financed: ₱40,000.00\n";
    echo "   ✓ Expected Interest: ~₱" . number_format($monthly_interest / 12 * 12, 2) . " (from all pending installments)\n";
    echo "   ✓ Expected Arrears: ~₱" . number_format($monthly_payment * 0.03 * 3, 2) . " (3% penalty on 3 overdue installments)\n\n";
} else {
    echo "   ⚠ Installment tables may not exist. Invoice created but no interest/arrears.\n\n";
}

// Summary
echo "=== Test Invoices Created ===\n\n";
echo "Invoice #1 (Late Fees):\n";
echo "  ID: {$invoice_id1}\n";
echo "  Number: {$invoice1['invoice_number']}\n";
echo "  Type: Cash (Non-installment)\n";
echo "  Status: Pending (10 days overdue)\n";
echo "  Balance: ₱25,000.00\n";
echo "  Expected Late Fee: ~₱125.00 (0.5%/day × 10 days)\n\n";

echo "Invoice #2 (Interest & Arrears):\n";
echo "  ID: {$invoice_id2}\n";
echo "  Number: {$invoice2['invoice_number']}\n";
echo "  Type: Installment\n";
echo "  Status: Partial\n";
echo "  Financed: ₱40,000.00\n";
echo "  Expected Interest: ~₱1,000.00 (from pending installments)\n";
echo "  Expected Arrears: ~₱300.00 (penalties on overdue installments)\n\n";

echo "To verify:\n";
echo "1. Go to: Admin → Invoice Management\n";
echo "2. Click: 'Verify All Calculations' button\n";
echo "3. Or verify specific invoice: Click 'View' → 'Verify Calculations'\n\n";

echo "The invoice_financials view will automatically calculate:\n";
echo "  - Late Fees: For overdue non-installment invoices\n";
echo "  - Interest: From pending/overdue installment schedules\n";
echo "  - Arrears: Penalties from overdue installments past grace period\n";

?>

