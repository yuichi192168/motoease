<?php
/**
 * Verification Script for Invoice Calculations
 * This script verifies that all invoice calculations are working correctly:
 * - Interest amounts
 * - Late fees
 * - Arrears (penalties)
 * - Total balance due
 * - Scheduled payment amounts with penalties
 */

require_once('config.php');

echo "=== Invoice Calculations Verification ===\n\n";

// Check if invoice_financials view exists and has required columns
echo "1. Checking invoice_financials view structure...\n";
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
            echo "   ✓ Column '{$req_col}' exists\n";
        } else {
            echo "   ✗ Column '{$req_col}' MISSING\n";
            $missing_columns[] = $req_col;
        }
    }
} else {
    echo "   ✗ ERROR: Could not query invoice_financials view\n";
    echo "   Error: " . $conn->error . "\n";
    exit(1);
}

if(!empty($missing_columns)){
    echo "\n   ⚠ WARNING: Some required columns are missing. Please run fix_invoice_calculations_with_arrears.sql\n\n";
} else {
    echo "\n   ✓ All required columns exist\n\n";
}

// Test invoice calculations
echo "2. Testing invoice calculations...\n";
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
    ORDER BY i.id DESC
    LIMIT 5
");

if($test_invoices && $test_invoices->num_rows > 0){
    echo "   Found " . $test_invoices->num_rows . " invoices to verify\n\n";
    
    while($inv = $test_invoices->fetch_assoc()){
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
        
        // Verify calculation
        $calculated_total = ($inv['balance_remaining'] ?? 0) + 
                           ($inv['interest_amount'] ?? 0) + 
                           ($inv['late_fee_amount'] ?? 0) + 
                           ($inv['arrears_amount'] ?? 0);
        $stored_total = $inv['total_balance_due'] ?? 0;
        $diff = abs($calculated_total - $stored_total);
        
        if($diff < 0.01){
            echo "   ✓ Calculation verified (difference: ₱" . number_format($diff, 2) . ")\n";
        } else {
            echo "   ✗ Calculation MISMATCH!\n";
            echo "     Calculated: ₱" . number_format($calculated_total, 2) . "\n";
            echo "     Stored: ₱" . number_format($stored_total, 2) . "\n";
            echo "     Difference: ₱" . number_format($diff, 2) . "\n";
        }
        echo "\n";
    }
} else {
    echo "   No invoices found to test\n\n";
}

// Test installment schedule calculations
echo "3. Testing installment schedule calculations...\n";
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

if($test_schedules && $test_schedules->num_rows > 0){
    echo "   Found " . $test_schedules->num_rows . " installment schedules to verify\n\n";
    
    while($sched = $test_schedules->fetch_assoc()){
        echo "   Schedule ID: {$sched['id']} (Contract: {$sched['contract_id']}, Installment #{$sched['installment_number']})\n";
        echo "   Due Date: {$sched['due_date']}\n";
        echo "   Days Overdue: " . ($sched['days_overdue'] > 0 ? $sched['days_overdue'] : 0) . "\n";
        echo "   Amount Due: ₱" . number_format($sched['amount_due'], 2) . "\n";
        echo "   Paid Amount: ₱" . number_format($sched['paid_amount'] ?? 0, 2) . "\n";
        echo "   Remaining Due: ₱" . number_format($sched['remaining_due'], 2) . "\n";
        echo "   Penalty Amount: ₱" . number_format($sched['penalty_amount'] ?? 0, 2) . "\n";
        echo "   Late Fee: ₱" . number_format($sched['late_fee'] ?? 0, 2) . "\n";
        echo "   Status: {$sched['status']}\n";
        
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
            
            if($diff < 0.01){
                echo "   ✓ Penalty calculation verified\n";
            } else {
                echo "   ⚠ Penalty calculation difference: ₱" . number_format($diff, 2) . "\n";
                echo "     Expected: ₱" . number_format($expected_penalty, 2) . "\n";
                echo "     Actual: ₱" . number_format($actual_penalty, 2) . "\n";
            }
        } else {
            echo "   ✓ No penalty expected (within grace period or paid)\n";
        }
        echo "\n";
    }
} else {
    echo "   No installment schedules found to test\n\n";
}

// Test settings
echo "4. Checking invoice settings...\n";
$settings = $conn->query("SELECT setting_key, setting_value, description FROM invoice_settings WHERE setting_key IN ('late_fee_daily_rate', 'penalty_rate_monthly', 'penalty_grace_period_days')");
if($settings && $settings->num_rows > 0){
    while($set = $settings->fetch_assoc()){
        echo "   ✓ {$set['setting_key']}: {$set['setting_value']}";
        if($set['description']){
            echo " ({$set['description']})";
        }
        echo "\n";
    }
} else {
    echo "   ⚠ Some settings may be missing\n";
}

echo "\n=== Verification Complete ===\n";
echo "\nTo update penalties for all overdue installments, run:\n";
echo "CALL update_installment_penalties();\n";

?>

