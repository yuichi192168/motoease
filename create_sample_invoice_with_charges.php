<?php
/**
 * Create Sample Invoice with Interest, Late Fee, and Arrears
 * This script creates a test invoice with all charges to verify calculations
 */

require_once('config.php');

echo "=== Creating Sample Invoice with Interest, Late Fee, and Arrears ===\n\n";

// 1. Get or create a test customer
echo "1. Getting/Creating test customer...\n";
$customer = $conn->query("SELECT id FROM client_list LIMIT 1")->fetch_assoc();
if(!$customer) {
    echo "   ✗ No customers found. Please create a customer first.\n";
    exit(1);
}
$customer_id = $customer['id'];
echo "   ✓ Using customer ID: {$customer_id}\n\n";

// 2. Create a sample order
echo "2. Creating sample order...\n";
$order_data = [
    'client_id' => $customer_id,
    'total_amount' => 50000.00,
    'status' => 1,
    'date_created' => date('Y-m-d H:i:s', strtotime('-30 days'))
];
$order_fields = implode(',', array_keys($order_data));
$order_values = "'" . implode("','", array_values($order_data)) . "'";
$order_sql = "INSERT INTO order_list ({$order_fields}) VALUES ({$order_values})";

if($conn->query($order_sql)) {
    $order_id = $conn->insert_id;
    echo "   ✓ Order created with ID: {$order_id}\n\n";
} else {
    echo "   ✗ Failed to create order: " . $conn->error . "\n";
    exit(1);
}

// 3. Create invoice with past due date (to trigger late fees)
echo "3. Creating invoice with past due date...\n";
$invoice_number = 'TEST-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
$due_date = date('Y-m-d', strtotime('-15 days')); // 15 days overdue
$generated_at = date('Y-m-d H:i:s', strtotime('-30 days'));

$invoice_data = [
    'order_id' => $order_id,
    'invoice_number' => $invoice_number,
    'customer_id' => $customer_id,
    'transaction_type' => 'motorcycle_purchase',
    'payment_type' => 'installment', // Use installment to trigger interest and arrears
    'subtotal' => 50000.00,
    'vat_amount' => 0, // VAT removed
    'total_amount' => 50000.00,
    'payment_status' => 'partial',
    'pickup_location' => 'Main Store',
    'payment_instructions' => 'Payment must be completed in-store',
    'generated_by' => 1,
    'generated_at' => $generated_at,
    'due_date' => $due_date
];

$invoice_fields = implode(',', array_keys($invoice_data));
$invoice_values = "'" . implode("','", array_values($invoice_data)) . "'";
$invoice_sql = "INSERT INTO invoices ({$invoice_fields}) VALUES ({$invoice_values})";

if($conn->query($invoice_sql)) {
    $invoice_id = $conn->insert_id;
    echo "   ✓ Invoice created with ID: {$invoice_id}, Number: {$invoice_number}\n";
    echo "   ✓ Due date: {$due_date} (15 days overdue)\n\n";
} else {
    echo "   ✗ Failed to create invoice: " . $conn->error . "\n";
    exit(1);
}

// 4. Add invoice items
echo "4. Adding invoice items...\n";
$item_data = [
    'invoice_id' => $invoice_id,
    'item_type' => 'motorcycle',
    'item_id' => 1,
    'item_name' => 'Sample Motorcycle - Test Invoice',
    'item_description' => 'Test motorcycle for calculation verification',
    'quantity' => 1,
    'unit_price' => 50000.00,
    'total_price' => 50000.00
];
$item_fields = implode(',', array_keys($item_data));
$item_values = "'" . implode("','", array_values($item_data)) . "'";
$item_sql = "INSERT INTO invoice_items ({$item_fields}) VALUES ({$item_values})";

if($conn->query($item_sql)) {
    echo "   ✓ Invoice item added\n\n";
} else {
    echo "   ⚠ Failed to add invoice item: " . $conn->error . "\n\n";
}

// 5. Create partial payment receipt (to show balance remaining)
echo "5. Creating partial payment receipt...\n";
$receipt_number = 'RCPT-TEST-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
$amount_paid = 10000.00; // Partial payment of 10,000 out of 50,000

$receipt_data = [
    'invoice_id' => $invoice_id,
    'receipt_number' => $receipt_number,
    'customer_id' => $customer_id,
    'amount_paid' => $amount_paid,
    'payment_method' => 'cash',
    'payment_reference' => 'TEST-PAYMENT',
    'received_by' => 1,
    'acknowledgment_note' => 'Test payment for verification',
    'archive_flag' => 0,
    'issued_at' => date('Y-m-d H:i:s', strtotime('-20 days'))
];

$receipt_fields = implode(',', array_keys($receipt_data));
$receipt_values = "'" . implode("','", array_values($receipt_data)) . "'";
$receipt_sql = "INSERT INTO receipts ({$receipt_fields}) VALUES ({$receipt_values})";

if($conn->query($receipt_sql)) {
    echo "   ✓ Receipt created: {$receipt_number}\n";
    echo "   ✓ Amount paid: ₱" . number_format($amount_paid, 2) . "\n";
    echo "   ✓ Balance remaining: ₱" . number_format(50000.00 - $amount_paid, 2) . "\n\n";
} else {
    echo "   ⚠ Failed to create receipt: " . $conn->error . "\n\n";
}

// 6. Create installment contract and schedule (to trigger interest and arrears)
echo "6. Creating installment contract and schedule...\n";

// Create installment contract
$contract_data = [
    'invoice_id' => $invoice_id,
    'order_id' => $order_id,
    'customer_id' => $customer_id,
    'total_amount' => 50000.00,
    'down_payment' => 10000.00,
    'financed_amount' => 40000.00,
    'remaining_balance' => 40000.00,
    'monthly_interest_rate' => 2.5,
    'number_of_months' => 12,
    'status' => 'active',
    'created_at' => $generated_at
];

$contract_fields = implode(',', array_keys($contract_data));
$contract_values = "'" . implode("','", array_values($contract_data)) . "'";
$contract_sql = "INSERT INTO installment_contracts ({$contract_fields}) VALUES ({$contract_values})";

if($conn->query($contract_sql)) {
    $contract_id = $conn->insert_id;
    echo "   ✓ Installment contract created with ID: {$contract_id}\n";
} else {
    echo "   ⚠ Failed to create contract: " . $conn->error . "\n";
    echo "   Note: Installment tables may not exist. Interest and arrears will be 0.\n\n";
    echo "\n=== Sample Invoice Created ===\n";
    echo "Invoice ID: {$invoice_id}\n";
    echo "Invoice Number: {$invoice_number}\n";
    echo "Due Date: {$due_date} (15 days overdue - will trigger late fees)\n";
    echo "Total Amount: ₱50,000.00\n";
    echo "Amount Paid: ₱10,000.00\n";
    echo "Balance Remaining: ₱40,000.00\n";
    echo "\nNote: Late fees will be calculated automatically by the invoice_financials view.\n";
    exit(0);
}

// Create installment schedule with overdue payments
$monthly_payment = 40000.00 / 12; // Approximately 3,333.33 per month
$base_interest = 40000.00 * 0.025; // 2.5% monthly interest

echo "   Creating installment schedule...\n";
for($i = 1; $i <= 12; $i++) {
    $due_date_schedule = date('Y-m-d', strtotime($generated_at . " +{$i} months"));
    $is_overdue = strtotime($due_date_schedule) < time();
    $days_overdue = $is_overdue ? max(0, floor((time() - strtotime($due_date_schedule)) / 86400)) : 0;
    
    // First 3 installments are overdue (to trigger arrears)
    if($i <= 3 && $is_overdue) {
        $status = 'overdue';
        $paid_amount = 0;
        $remaining_due = $monthly_payment;
        
        // Calculate penalty if past grace period (7 days)
        $penalty_amount = 0;
        if($days_overdue >= 7) {
            $months_overdue = ceil($days_overdue / 30.0);
            $penalty_rate = 0.03; // 3% monthly
            $penalty_amount = $remaining_due * $penalty_rate * $months_overdue;
        }
        
        $late_fee = 0; // Late fees are usually for non-installment invoices
    } else {
        $status = $i <= 3 ? 'pending' : 'pending';
        $paid_amount = 0;
        $remaining_due = $monthly_payment;
        $penalty_amount = 0;
        $late_fee = 0;
    }
    
    $schedule_data = [
        'contract_id' => $contract_id,
        'installment_number' => $i,
        'due_date' => $due_date_schedule,
        'amount_due' => $monthly_payment,
        'principal_amount' => $monthly_payment - ($base_interest / 12),
        'interest_amount' => $base_interest / 12,
        'paid_amount' => $paid_amount,
        'remaining_balance' => $remaining_due,
        'penalty_amount' => $penalty_amount,
        'late_fee' => $late_fee,
        'status' => $status,
        'created_at' => $generated_at
    ];
    
    $schedule_fields = implode(',', array_keys($schedule_data));
    $schedule_values = "'" . implode("','", array_values($schedule_data)) . "'";
    $schedule_sql = "INSERT INTO installment_schedule ({$schedule_fields}) VALUES ({$schedule_values})";
    
    if($conn->query($schedule_sql)) {
        if($i <= 3) {
            echo "   ✓ Installment #{$i} created (Due: {$due_date_schedule}, Status: {$status}";
            if($penalty_amount > 0) {
                echo ", Penalty: ₱" . number_format($penalty_amount, 2);
            }
            echo ")\n";
        }
    } else {
        echo "   ⚠ Failed to create schedule #{$i}: " . $conn->error . "\n";
    }
}

// 7. Summary
echo "\n=== Sample Invoice Created Successfully ===\n\n";
echo "Invoice Details:\n";
echo "  Invoice ID: {$invoice_id}\n";
echo "  Invoice Number: {$invoice_number}\n";
echo "  Customer ID: {$customer_id}\n";
echo "  Order ID: {$order_id}\n";
echo "  Total Amount: ₱50,000.00\n";
echo "  Amount Paid: ₱10,000.00\n";
echo "  Balance Remaining: ₱40,000.00\n";
echo "  Due Date: {$due_date} (15 days overdue)\n";
echo "  Payment Type: Installment\n\n";

echo "Expected Charges:\n";
echo "  Interest: Will be calculated from installment schedule (pending installments)\n";
echo "  Late Fee: ₱0.00 (late fees apply to non-installment invoices only)\n";
echo "  Arrears: Will be calculated from overdue installment schedules (3 overdue installments)\n\n";

echo "To verify calculations, run:\n";
echo "  - Visit: Admin → Invoice Management → Verify All Calculations\n";
echo "  - Or: php verify_invoice_calculations.php?ajax=1&invoice_id={$invoice_id}\n\n";

echo "Note: The invoice_financials view will automatically calculate:\n";
echo "  - Interest from pending/overdue installment schedules\n";
echo "  - Arrears (penalties) from overdue installments past grace period\n";
echo "  - Total balance due including all charges\n";

?>

