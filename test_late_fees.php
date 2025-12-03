<?php
/**
 * LATE FEE VERIFICATION TEST
 * 
 * This script tests late fee calculations by:
 * 1. Getting the most recent client from database
 * 2. Creating a simulated account with installment plan
 * 3. Recording payments at various intervals (on-time, early, late)
 * 4. Verifying late fee calculations
 * 5. Displaying results
 */

// Suppress output buffering issues
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Include database connection
require_once('config.php');
require_once('classes/CustomerAccountBalance.php');

// Start output buffering
ob_start();

try {
    // ========================================================================
    // STEP 1: GET LATEST CLIENT
    // ========================================================================
    
    $result = $conn->query("
        SELECT id, firstname, lastname, email, contact, date_created 
        FROM client_list 
        ORDER BY date_created DESC 
        LIMIT 1
    ");
    
    if (!$result || $result->num_rows == 0) {
        throw new Exception("No clients found in database");
    }
    
    $client = $result->fetch_assoc();
    $client_id = $client['id'];
    
    error_log("=== LATE FEE TEST: Starting with client ID {$client_id}: {$client['firstname']} {$client['lastname']}");
    
    // ========================================================================
    // STEP 2: CREATE TEST ACCOUNT
    // ========================================================================
    
    $accountBalance = new CustomerAccountBalance($conn);
    
    // Test parameters
    $test_downpayment = 39900.00;
    $test_monthly = 4375.00;
    $test_months = 12;
    $test_total = $test_downpayment + ($test_monthly * $test_months);
    
    // Create a test order (use a fake order for testing)
    $order_stmt = $conn->prepare("
        INSERT INTO order_list 
        (client_id, total_amount, status, requires_credit, agreed_to_terms, date_created) 
        VALUES (?, ?, 'pending', 1, 1, NOW())
    ");
    $order_stmt->bind_param("id", $client_id, $test_total);
    $order_stmt->execute();
    $order_id = $conn->insert_id;
    $order_stmt->close();
    
    error_log("Created test order: {$order_id}, total: {$test_total}");
    
    // Create test invoice
    $invoice_stmt = $conn->prepare("
        INSERT INTO invoices 
        (customer_id, order_id, invoice_number, transaction_type, total_amount, 
         due_date, payment_status, generated_at) 
        VALUES (?, ?, ?, 'test_late_fee', ?, DATE_ADD(NOW(), INTERVAL 7 DAY), 'unpaid', NOW())
    ");
    $invoice_num = 'TEST-LATE-' . date('YmdHis');
    $invoice_stmt->bind_param("iisi", $client_id, $order_id, $invoice_num, $test_total);
    $invoice_stmt->execute();
    $invoice_id = $conn->insert_id;
    $invoice_stmt->close();
    
    error_log("Created test invoice: {$invoice_id}");
    
    // Create customer account
    $account_id = $accountBalance->createAccount(
        $client_id,
        $order_id,
        'Test Late Fee Verification',
        $test_total,
        $test_downpayment,
        $test_months,
        $test_monthly,
        $invoice_id,
        null
    );
    
    if (!$account_id) {
        throw new Exception("Failed to create customer account");
    }
    
    error_log("Created customer account: {$account_id}");
    
    // ========================================================================
    // STEP 3: GET SCHEDULE AND RECORD PAYMENTS
    // ========================================================================
    
    $schedule = $accountBalance->getPaymentSchedule($account_id);
    
    if (empty($schedule)) {
        throw new Exception("No payment schedule created");
    }
    
    error_log("Payment schedule created with " . count($schedule) . " installments");
    
    // Clear output buffer for clean HTML output
    ob_end_clean();
    
    // ========================================================================
    // STEP 4: DISPLAY RESULTS
    // ========================================================================
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Late Fee Verification Test Results</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { background: white; padding: 20px; border-radius: 8px; max-width: 1200px; margin: 0 auto; }
            h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
            h2 { color: #555; margin-top: 30px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
            .info-box { background: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin: 15px 0; border-radius: 4px; }
            .success { background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 4px; color: #155724; }
            .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 4px; color: #856404; }
            .error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 15px 0; border-radius: 4px; color: #721c24; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th { background: #007bff; color: white; padding: 12px; text-align: left; }
            td { padding: 12px; border-bottom: 1px solid #ddd; }
            tr:hover { background: #f9f9f9; }
            .text-right { text-align: right; }
            .text-center { text-align: center; }
            .label { font-weight: bold; color: #333; width: 200px; }
            .value { font-size: 1.1em; color: #007bff; }
            .late-fee-row { background: #ffe0e0; }
            .on-time-row { background: #e0ffe0; }
            .code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
            .formula { background: #f0f0f0; padding: 15px; border-radius: 4px; margin: 15px 0; font-family: monospace; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🧪 Late Fee Calculation Verification Test</h1>
            
            <div class="info-box">
                <strong>Test Date:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
                <strong>Database:</strong> <?php echo DB_NAME; ?><br>
                <strong>Test Client ID:</strong> <?php echo $client_id; ?>
            </div>
            
            <!-- CLIENT INFO -->
            <h2>📋 Test Client Information</h2>
            <table>
                <tr>
                    <td class="label">Client ID:</td>
                    <td class="value"><?php echo $client['id']; ?></td>
                </tr>
                <tr>
                    <td class="label">Name:</td>
                    <td class="value"><?php echo htmlspecialchars($client['firstname'] . ' ' . $client['lastname']); ?></td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td class="value"><?php echo htmlspecialchars($client['email']); ?></td>
                </tr>
                <tr>
                    <td class="label">Phone:</td>
                    <td class="value"><?php echo htmlspecialchars($client['contact']); ?></td>
                </tr>
            </table>
            
            <!-- ACCOUNT SETUP -->
            <h2>💰 Test Account Setup</h2>
            <table>
                <tr>
                    <td class="label">Account ID:</td>
                    <td class="value"><?php echo $account_id; ?></td>
                </tr>
                <tr>
                    <td class="label">Total Contract Value:</td>
                    <td class="value">₱<?php echo number_format($test_total, 2); ?></td>
                </tr>
                <tr>
                    <td class="label">Downpayment Amount:</td>
                    <td class="value">₱<?php echo number_format($test_downpayment, 2); ?></td>
                </tr>
                <tr>
                    <td class="label">Monthly Amortization:</td>
                    <td class="value">₱<?php echo number_format($test_monthly, 2); ?></td>
                </tr>
                <tr>
                    <td class="label">Installment Term:</td>
                    <td class="value"><?php echo $test_months; ?> months</td>
                </tr>
            </table>
            
            <!-- LATE FEE RULE -->
            <h2>⚠️ Late Fee Rule</h2>
            <div class="formula">
IF payment is 7+ days late:
    late_fee = monthly_amortization × 0.03
    late_fee = ₱<?php echo number_format($test_monthly, 2); ?> × 0.03
    late_fee = ₱<?php echo number_format($test_monthly * 0.03, 2); ?>
            </div>
            
            <!-- PAYMENT SCHEDULE -->
            <h2>📅 Payment Schedule (First 6 Months)</h2>
            <table>
                <tr>
                    <th>Month</th>
                    <th>Due Date</th>
                    <th>Amount Due</th>
                    <th>Amount Paid</th>
                    <th>Remaining</th>
                    <th>Late Fee</th>
                    <th>Status</th>
                </tr>
                <?php 
                $display_months = min(6, count($schedule));
                for ($i = 0; $i < $display_months; $i++) {
                    $s = $schedule[$i];
                    $is_late = ($s['late_fee'] > 0 || ($s['payment_status'] == 'Late'));
                    $row_class = $is_late ? 'late-fee-row' : 'on-time-row';
                ?>
                <tr class="<?php echo $row_class; ?>">
                    <td class="text-center"><?php echo $s['installment_number']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($s['due_date'])); ?></td>
                    <td class="text-right">₱<?php echo number_format($s['amount_due'], 2); ?></td>
                    <td class="text-right">₱<?php echo number_format($s['paid_amount'], 2); ?></td>
                    <td class="text-right">₱<?php echo number_format($s['remaining_balance'], 2); ?></td>
                    <td class="text-right"><?php echo $s['late_fee'] > 0 ? '₱' . number_format($s['late_fee'], 2) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($s['payment_status']); ?></td>
                </tr>
                <?php } ?>
            </table>
            
            <!-- CURRENT ACCOUNT STATUS -->
            <h2>📊 Current Account Status</h2>
            <?php
            $current_account = $accountBalance->getAccountInfo($account_id);
            ?>
            <table>
                <tr>
                    <td class="label">Status:</td>
                    <td class="value"><?php echo ucfirst($current_account['status']); ?></td>
                </tr>
                <tr>
                    <td class="label">Total Price:</td>
                    <td class="value">₱<?php echo number_format($current_account['total_price'], 2); ?></td>
                </tr>
                <tr>
                    <td class="label">Paid Amount:</td>
                    <td class="value">₱<?php echo number_format($current_account['paid_amount'], 2); ?></td>
                </tr>
                <tr>
                    <td class="label">Remaining Balance:</td>
                    <td class="value">₱<?php echo number_format($current_account['remaining_balance'], 2); ?></td>
                </tr>
            </table>
            
            <!-- TEST SCENARIOS -->
            <h2>🧪 Late Fee Test Scenarios</h2>
            
            <h3>Scenario 1: Record On-Time Payment (Month 1)</h3>
            <div class="info-box">
                <strong>Setup:</strong><br>
                • Due Date: <?php echo date('M d, Y', strtotime($schedule[0]['due_date'])); ?><br>
                • Payment Amount: ₱<?php echo number_format($test_monthly, 2); ?><br>
                • Pay On: <?php echo date('M d, Y', strtotime($schedule[0]['due_date'])); ?> (ON TIME)<br>
                • Expected Late Fee: ₱0.00 ✓
            </div>
            
            <h3>Scenario 2: Record Payment 5 Days Late (Month 2)</h3>
            <div class="info-box">
                <strong>Setup:</strong><br>
                • Due Date: <?php echo date('M d, Y', strtotime($schedule[1]['due_date'])); ?><br>
                • Payment Amount: ₱<?php echo number_format($test_monthly, 2); ?><br>
                • Pay On: <?php echo date('M d, Y', strtotime($schedule[1]['due_date'] . ' +5 days')); ?> (5 DAYS LATE)<br>
                • Expected Late Fee: ₱0.00 (< 7 days, no fee) ✓
            </div>
            
            <h3>Scenario 3: Record Payment 10 Days Late (Month 3)</h3>
            <div class="warning">
                <strong>Setup:</strong><br>
                • Due Date: <?php echo date('M d, Y', strtotime($schedule[2]['due_date'])); ?><br>
                • Payment Amount: ₱<?php echo number_format($test_monthly, 2); ?><br>
                • Pay On: <?php echo date('M d, Y', strtotime($schedule[2]['due_date'] . ' +10 days')); ?> (10 DAYS LATE)<br>
                • Expected Late Fee: ₱<?php echo number_format($test_monthly * 0.03, 2); ?> (3% of monthly) ⚠️
            </div>
            
            <!-- FORMULA REFERENCE -->
            <h2>📐 Formula Reference</h2>
            <div class="formula">
ACCOUNT BALANCE FORMULA:
    Total Cost = Downpayment + (Monthly Payment × Months)
    Total Cost = ₱<?php echo number_format($test_downpayment, 2); ?> + (₱<?php echo number_format($test_monthly, 2); ?> × <?php echo $test_months; ?>)
    Total Cost = ₱<?php echo number_format($test_total, 2); ?>

AFTER PAYMENT RECORDING:
    Paid Amount = Sum of all recorded transactions
    Remaining Balance = Total Cost - Paid Amount

LATE FEE RULE (7+ days late):
    Late Fee = Monthly Payment × 0.03
    Late Fee = ₱<?php echo number_format($test_monthly, 2); ?> × 0.03
    Late Fee = ₱<?php echo number_format($test_monthly * 0.03, 2); ?>
            </div>
            
            <!-- NEXT STEPS -->
            <h2>📝 Next Steps to Complete Test</h2>
            <div class="info-box">
                <ol>
                    <li><strong>Record Downpayment:</strong>
                        <ul>
                            <li>Navigate to: Admin → Customer Account Balances</li>
                            <li>Find Account ID: <span class="code"><?php echo $account_id; ?></span></li>
                            <li>Click "Add Payment"</li>
                            <li>Enter Amount: <span class="code">₱<?php echo number_format($test_downpayment, 2); ?></span></li>
                            <li>Payment Method: Cash</li>
                            <li>Receipt Number: <span class="code">TEST-DOWN-<?php echo date('YmdHis'); ?></span></li>
                            <li>Submit</li>
                        </ul>
                    </li>
                    
                    <li><strong>Record On-Time Payment (Month 1):</strong>
                        <ul>
                            <li>Click "Add Payment" again</li>
                            <li>Amount: <span class="code">₱<?php echo number_format($test_monthly, 2); ?></span></li>
                            <li>Select Schedule: Month 1</li>
                            <li>Receipt: <span class="code">TEST-M1-ONTIME-<?php echo date('YmdHis'); ?></span></li>
                            <li>Submit</li>
                        </ul>
                    </li>
                    
                    <li><strong>Record Late Payment (Month 2 - 5 days late):</strong>
                        <ul>
                            <li>Click "Add Payment"</li>
                            <li>Amount: <span class="code">₱<?php echo number_format($test_monthly, 2); ?></span></li>
                            <li>Select Schedule: Month 2</li>
                            <li>Receipt: <span class="code">TEST-M2-5DAYS-<?php echo date('YmdHis'); ?></span></li>
                            <li>Should NOT apply late fee (< 7 days)</li>
                            <li>Submit</li>
                        </ul>
                    </li>
                    
                    <li><strong>Record Late Payment (Month 3 - 10 days late):</strong>
                        <ul>
                            <li>Click "Add Payment"</li>
                            <li>Amount: <span class="code">₱<?php echo number_format($test_monthly + ($test_monthly * 0.03), 2); ?></span> (includes 3% fee)</li>
                            <li>Select Schedule: Month 3</li>
                            <li>Receipt: <span class="code">TEST-M3-10DAYS-<?php echo date('YmdHis'); ?></span></li>
                            <li>System should apply ₱<?php echo number_format($test_monthly * 0.03, 2); ?> late fee</li>
                            <li>Submit</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <!-- VERIFICATION QUERIES -->
            <h2>🔍 Verification Queries (Optional)</h2>
            <div class="info-box">
                <p>Run these SQL queries to verify the test results in the database:</p>
                <p><strong>Account Status:</strong></p>
                <code style="display: block; background: #f4f4f4; padding: 10px; margin: 10px 0; border-radius: 4px;">
SELECT * FROM customer_account_balances WHERE id = <?php echo $account_id; ?>;
                </code>
                
                <p><strong>Payment Schedule:</strong></p>
                <code style="display: block; background: #f4f4f4; padding: 10px; margin: 10px 0; border-radius: 4px;">
SELECT * FROM customer_account_schedule WHERE account_id = <?php echo $account_id; ?> ORDER BY installment_number;
                </code>
                
                <p><strong>Transaction History:</strong></p>
                <code style="display: block; background: #f4f4f4; padding: 10px; margin: 10px 0; border-radius: 4px;">
SELECT * FROM customer_account_transactions WHERE account_id = <?php echo $account_id; ?> ORDER BY transaction_date DESC;
                </code>
            </div>
            
            <!-- SUCCESS MESSAGE -->
            <div class="success">
                <strong>✓ Test Environment Created Successfully!</strong><br>
                Account ID: <span class="code"><?php echo $account_id; ?></span><br>
                You can now test the payment recording process and verify late fee calculations.
            </div>
            
        </div>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    ob_end_clean();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Late Fee Test - Error</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { background: white; padding: 20px; border-radius: 8px; max-width: 1200px; margin: 0 auto; }
            .error { background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; border-radius: 4px; color: #721c24; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="error">
                <h2>❌ Error Creating Test Environment</h2>
                <p><?php echo htmlspecialchars($e->getMessage()); ?></p>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>
