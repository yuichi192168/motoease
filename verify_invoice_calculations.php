<?php
/**
 * Verification Script for Invoice Calculations
 * This script verifies that all invoice calculations are working correctly:
 * - Interest amounts
 * - Late fees
 * - Arrears (penalties)
 * - Total balance due
 * - Scheduled payment amounts with penalties
 * 
 * Can be called via:
 * - CLI: php verify_invoice_calculations.php
 * - AJAX: verify_invoice_calculations.php?ajax=1&invoice_id=123 (optional invoice_id)
 */

require_once('config.php');

// Check if called via AJAX
$is_ajax = isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
$invoice_id = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : null;

$result = [
    'status' => 'success',
    'data' => []
];

// Helper function to output (either echo or add to result array)
function addResult(&$result, $section, $message, $type = 'info') {
    if (!isset($result['data'][$section])) {
        $result['data'][$section] = [];
    }
    $result['data'][$section][] = [
        'message' => $message,
        'type' => $type
    ];
}

if(!$is_ajax) {
    echo "=== Invoice Calculations Verification ===\n\n";
}

// 1. Check if invoice_financials view exists and has required columns
if(!$is_ajax) echo "1. Checking invoice_financials view structure...\n";
$columns_check = $conn->query("SHOW COLUMNS FROM invoice_financials");
$required_columns = ['interest_amount', 'arrears_amount', 'total_balance_due', 'late_fee_amount', 'balance_remaining'];
$found_columns = [];
$missing_columns = [];

if($columns_check){
    while($col = $columns_check->fetch_assoc()){
        $found_columns[] = $col['Field'];
    }
    
    foreach($required_columns as $req_col){
        if(in_array($req_col, $found_columns)){
            if(!$is_ajax) echo "   ✓ Column '{$req_col}' exists\n";
            addResult($result, 'structure', "Column '{$req_col}' exists", 'success');
        } else {
            if(!$is_ajax) echo "   ✗ Column '{$req_col}' MISSING\n";
            addResult($result, 'structure', "Column '{$req_col}' MISSING", 'error');
            $missing_columns[] = $req_col;
        }
    }
} else {
    $error_msg = "ERROR: Could not query invoice_financials view - " . $conn->error;
    if(!$is_ajax) echo "   ✗ {$error_msg}\n";
    addResult($result, 'structure', $error_msg, 'error');
    if($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'msg' => $error_msg]);
        exit;
    } else {
        exit(1);
    }
}

if(!empty($missing_columns)){
    $warning = "WARNING: Some required columns are missing. Please run fix_invoice_calculations_with_arrears.sql";
    if(!$is_ajax) echo "\n   ⚠ {$warning}\n\n";
    addResult($result, 'structure', $warning, 'warning');
    $result['status'] = 'warning';
} else {
    if(!$is_ajax) echo "\n   ✓ All required columns exist\n\n";
    addResult($result, 'structure', 'All required columns exist', 'success');
}

// 2. Test invoice calculations
if(!$is_ajax) echo "2. Testing invoice calculations...\n";
$where_clause = $invoice_id ? "WHERE i.id = '{$invoice_id}'" : "";
// If verifying all invoices, get more results (up to 50 for performance)
$limit_clause = $invoice_id ? "" : "LIMIT 50";

$test_invoices = $conn->query("
    SELECT 
        i.id,
        i.invoice_number,
        i.payment_type,
        i.total_amount,
        fin.balance_remaining,
        fin.interest_amount,
        fin.late_fee_amount,
        fin.arrears_amount,
        fin.total_balance_due,
        fin.total_paid,
        fin.computed_status
    FROM invoices i
    LEFT JOIN invoice_financials fin ON fin.id = i.id
    {$where_clause}
    ORDER BY i.id DESC
    {$limit_clause}
");

$invoice_results = [];

if($test_invoices && $test_invoices->num_rows > 0){
    if(!$is_ajax) echo "   Found " . $test_invoices->num_rows . " invoices to verify\n\n";
    
    while($inv = $test_invoices->fetch_assoc()){
        $invoice_data = [
            'invoice_number' => $inv['invoice_number'],
            'id' => $inv['id'],
            'payment_type' => $inv['payment_type'],
            'total_amount' => floatval($inv['total_amount']),
            'total_paid' => floatval($inv['total_paid'] ?? 0),
            'balance_remaining' => floatval($inv['balance_remaining'] ?? 0),
            'interest_amount' => floatval($inv['interest_amount'] ?? 0),
            'late_fee_amount' => floatval($inv['late_fee_amount'] ?? 0),
            'arrears_amount' => floatval($inv['arrears_amount'] ?? 0),
            'total_balance_due' => floatval($inv['total_balance_due'] ?? 0),
            'computed_status' => $inv['computed_status'],
            'calculation_verified' => false,
            'calculation_diff' => 0
        ];
        
        if(!$is_ajax) {
            echo "   Invoice #{$inv['invoice_number']} (ID: {$inv['id']})\n";
            echo "   Payment Type: {$inv['payment_type']}\n";
            echo "   Total Amount: ₱" . number_format($inv['total_amount'], 2) . "\n";
            echo "   Total Paid: ₱" . number_format($inv['total_paid'] ?? 0, 2) . "\n";
            echo "   Balance Remaining: ₱" . number_format($inv['balance_remaining'] ?? 0, 2) . "\n";
            echo "   Interest Amount: ₱" . number_format($inv['interest_amount'] ?? 0, 2) . "\n";
            echo "   Late Fee: ₱" . number_format($inv['late_fee_amount'] ?? 0, 2) . "\n";
            echo "   Arrears: ₱" . number_format($inv['arrears_amount'] ?? 0, 2) . "\n";
            echo "   Total Balance Due: ₱" . number_format($inv['total_balance_due'] ?? 0, 2) . "\n";
            echo "   Status: {$inv['computed_status']}\n";
        }
        
        // Verify calculation
        $calculated_total = ($inv['balance_remaining'] ?? 0) + 
                           ($inv['interest_amount'] ?? 0) + 
                           ($inv['late_fee_amount'] ?? 0) + 
                           ($inv['arrears_amount'] ?? 0);
        $stored_total = $inv['total_balance_due'] ?? 0;
        $diff = abs($calculated_total - $stored_total);
        
        $invoice_data['calculated_total'] = $calculated_total;
        $invoice_data['calculation_diff'] = $diff;
        $invoice_data['calculation_verified'] = ($diff < 0.01);
        
        if($diff < 0.01){
            if(!$is_ajax) echo "   ✓ Calculation verified (difference: ₱" . number_format($diff, 2) . ")\n";
            addResult($result, 'invoices', "Invoice #{$inv['invoice_number']}: Calculation verified", 'success');
        } else {
            if(!$is_ajax) {
                echo "   ✗ Calculation MISMATCH!\n";
                echo "     Calculated: ₱" . number_format($calculated_total, 2) . "\n";
                echo "     Stored: ₱" . number_format($stored_total, 2) . "\n";
                echo "     Difference: ₱" . number_format($diff, 2) . "\n";
            }
            addResult($result, 'invoices', "Invoice #{$inv['invoice_number']}: Calculation MISMATCH (Difference: ₱" . number_format($diff, 2) . ")", 'error');
            $result['status'] = 'warning';
        }
        if(!$is_ajax) echo "\n";
        
        $invoice_results[] = $invoice_data;
    }
} else {
    if(!$is_ajax) echo "   No invoices found to test\n\n";
    addResult($result, 'invoices', 'No invoices found to test', 'info');
}

$result['data']['invoice_details'] = $invoice_results;

// 3. Test installment schedule calculations
if(!$is_ajax) echo "3. Testing installment schedule calculations...\n";
$test_schedules = $conn->query("
    SELECT 
        isch.id,
        isch.contract_id,
        isch.installment_number,
        isch.due_date,
        isch.amount_due,
        isch.paid_amount,
        isch.penalty_amount,
        isch.late_fee,
        isch.status,
        DATEDIFF(CURDATE(), isch.due_date) as days_overdue,
        (isch.amount_due - COALESCE(isch.paid_amount, 0)) as remaining_due
    FROM installment_schedule isch
    INNER JOIN installment_contracts ic ON isch.contract_id = ic.id
    WHERE ic.status = 'active'
      AND isch.status IN ('pending', 'overdue', 'partial')
    ORDER BY isch.due_date ASC
    LIMIT 5
");

$schedule_results = [];

if($test_schedules && $test_schedules->num_rows > 0){
    if(!$is_ajax) echo "   Found " . $test_schedules->num_rows . " installment schedules to verify\n\n";
    
    while($sched = $test_schedules->fetch_assoc()){
        $schedule_data = [
            'id' => $sched['id'],
            'contract_id' => $sched['contract_id'],
            'installment_number' => $sched['installment_number'],
            'due_date' => $sched['due_date'],
            'days_overdue' => max(0, intval($sched['days_overdue'])),
            'amount_due' => floatval($sched['amount_due']),
            'paid_amount' => floatval($sched['paid_amount'] ?? 0),
            'remaining_due' => floatval($sched['remaining_due']),
            'penalty_amount' => floatval($sched['penalty_amount'] ?? 0),
            'late_fee' => floatval($sched['late_fee'] ?? 0),
            'status' => $sched['status']
        ];
        
        if(!$is_ajax) {
            echo "   Schedule ID: {$sched['id']} (Contract: {$sched['contract_id']}, Installment #{$sched['installment_number']})\n";
            echo "   Due Date: {$sched['due_date']}\n";
            echo "   Days Overdue: " . ($sched['days_overdue'] > 0 ? $sched['days_overdue'] : 0) . "\n";
            echo "   Amount Due: ₱" . number_format($sched['amount_due'], 2) . "\n";
            echo "   Paid Amount: ₱" . number_format($sched['paid_amount'] ?? 0, 2) . "\n";
            echo "   Remaining Due: ₱" . number_format($sched['remaining_due'], 2) . "\n";
            echo "   Penalty Amount: ₱" . number_format($sched['penalty_amount'] ?? 0, 2) . "\n";
            echo "   Late Fee: ₱" . number_format($sched['late_fee'] ?? 0, 2) . "\n";
            echo "   Status: {$sched['status']}\n";
        }
        
        // Check if penalty should be applied
        $grace_period = 7; // Default
        $penalty_rate = 0.03; // 3% default
        
        $settings = $conn->query("SELECT setting_key, setting_value FROM invoice_settings WHERE setting_key IN ('penalty_grace_period_days', 'penalty_rate_monthly')");
        if($settings){
            while($set = $settings->fetch_assoc()){
                if($set['setting_key'] == 'penalty_grace_period_days'){
                    $grace_period = intval($set['setting_value']);
                } elseif($set['setting_key'] == 'penalty_rate_monthly'){
                    $penalty_rate = floatval($set['setting_value']) / 100;
                }
            }
        }
        
        $days_overdue = max(0, intval($sched['days_overdue']));
        $should_have_penalty = $days_overdue >= $grace_period && $sched['status'] != 'paid';
        
        if($should_have_penalty){
            $expected_penalty = $sched['remaining_due'] * $penalty_rate * max(1, ceil($days_overdue / 30.0));
            $actual_penalty = $sched['penalty_amount'] ?? 0;
            $diff = abs($expected_penalty - $actual_penalty);
            
            $schedule_data['penalty_verified'] = ($diff < 0.01);
            $schedule_data['expected_penalty'] = $expected_penalty;
            $schedule_data['penalty_diff'] = $diff;
            
            if($diff < 0.01){
                if(!$is_ajax) echo "   ✓ Penalty calculation verified\n";
                addResult($result, 'schedules', "Schedule #{$sched['installment_number']}: Penalty verified", 'success');
            } else {
                if(!$is_ajax) {
                    echo "   ⚠ Penalty calculation difference: ₱" . number_format($diff, 2) . "\n";
                    echo "     Expected: ₱" . number_format($expected_penalty, 2) . "\n";
                    echo "     Actual: ₱" . number_format($actual_penalty, 2) . "\n";
                }
                addResult($result, 'schedules', "Schedule #{$sched['installment_number']}: Penalty difference ₱" . number_format($diff, 2), 'warning');
            }
        } else {
            if(!$is_ajax) echo "   ✓ No penalty expected (within grace period or paid)\n";
            $schedule_data['penalty_verified'] = true;
            addResult($result, 'schedules', "Schedule #{$sched['installment_number']}: No penalty expected", 'info');
        }
        if(!$is_ajax) echo "\n";
        
        $schedule_results[] = $schedule_data;
    }
} else {
    if(!$is_ajax) echo "   No installment schedules found to test\n\n";
    addResult($result, 'schedules', 'No installment schedules found to test', 'info');
}

$result['data']['schedule_details'] = $schedule_results;

// 4. Test settings
if(!$is_ajax) echo "4. Checking invoice settings...\n";
$settings = $conn->query("SELECT setting_key, setting_value, description FROM invoice_settings WHERE setting_key IN ('late_fee_daily_rate', 'penalty_rate_monthly', 'penalty_grace_period_days')");
$settings_data = [];

if($settings && $settings->num_rows > 0){
    while($set = $settings->fetch_assoc()){
        $settings_data[] = [
            'key' => $set['setting_key'],
            'value' => $set['setting_value'],
            'description' => $set['description'] ?? ''
        ];
        if(!$is_ajax) {
            echo "   ✓ {$set['setting_key']}: {$set['setting_value']}";
            if($set['description']){
                echo " ({$set['description']})";
            }
            echo "\n";
        }
        addResult($result, 'settings', "{$set['setting_key']}: {$set['setting_value']}", 'success');
    }
} else {
    if(!$is_ajax) echo "   ⚠ Some settings may be missing\n";
    addResult($result, 'settings', 'Some settings may be missing', 'warning');
}

$result['data']['settings'] = $settings_data;

// Output results
if($is_ajax) {
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    echo "\n=== Verification Complete ===\n";
    echo "\nTo update penalties for all overdue installments, run:\n";
    echo "CALL update_installment_penalties();\n";
}

?>
