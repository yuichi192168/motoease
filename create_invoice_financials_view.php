<?php
/**
 * Create Invoice Financials View with Arrears
 * This script directly creates the view without parsing DELIMITER blocks
 */

require_once('config.php');

echo "=== Creating Invoice Financials View ===\n\n";

// First, ensure settings exist
echo "1. Setting up invoice settings...\n";
$settings = [
    ['late_fee_daily_rate', '0.50', 'Late fee daily rate in percent (e.g., 0.50 = 0.5%/day)'],
    ['penalty_rate_monthly', '3.00', 'Penalty rate per month for overdue installments (e.g., 3.00 = 3%/month)'],
    ['penalty_grace_period_days', '7', 'Grace period in days before penalty applies']
];

foreach($settings as $setting){
    $sql = "INSERT INTO invoice_settings (setting_key, setting_value, description) 
            VALUES ('{$setting[0]}', '{$setting[1]}', '{$setting[2]}')
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
    if($conn->query($sql)){
        echo "   ✓ Setting '{$setting[0]}' configured\n";
    } else {
        echo "   ✗ Failed to set '{$setting[0]}': " . $conn->error . "\n";
    }
}

// Add penalty_amount column if it doesn't exist
echo "\n2. Checking penalty_amount column...\n";
$check_col = $conn->query("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS 
                          WHERE TABLE_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'installment_schedule' 
                          AND COLUMN_NAME = 'penalty_amount'");
if($check_col){
    $row = $check_col->fetch_assoc();
    if($row['cnt'] == 0){
        $sql = "ALTER TABLE `installment_schedule` 
                ADD COLUMN `penalty_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER `late_fee`";
        if($conn->query($sql)){
            echo "   ✓ Added penalty_amount column\n";
        } else {
            echo "   ✗ Failed to add column: " . $conn->error . "\n";
        }
    } else {
        echo "   ✓ Column penalty_amount already exists\n";
    }
}

// Drop existing view or table
echo "\n3. Dropping existing view/table...\n";
$conn->query("DROP VIEW IF EXISTS invoice_financials");
$conn->query("DROP TABLE IF EXISTS invoice_financials");
echo "   ✓ View/table dropped (if it existed)\n";

// Create the view
echo "\n4. Creating invoice_financials view...\n";

$view_sql = "
CREATE VIEW `invoice_financials` AS
SELECT 
  i.id,
  i.invoice_number,
  i.order_id,
  i.customer_id,
  i.transaction_type,
  i.payment_type,
  i.subtotal,
  i.vat_amount,
  i.total_amount,
  i.generated_at,
  i.due_date,
  i.payment_status AS stored_status,
  COALESCE(SUM(r.amount_paid), 0) AS total_paid,
  MAX(r.issued_at) AS payment_date,
  
  -- Calculate interest amount from installment schedules
  COALESCE((
    SELECT SUM(COALESCE(isch.interest_amount, 0))
    FROM installment_contracts ic
    INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
    WHERE ic.invoice_id = i.id
      AND isch.status IN ('pending', 'overdue', 'partial')
  ), 0) AS interest_amount,
  
  -- Calculate late fee for overdue invoices (non-installment)
  CASE 
    WHEN i.payment_type != 'installment' 
      AND i.due_date IS NOT NULL 
      AND CURDATE() > i.due_date 
      AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 
      GREATEST(DATEDIFF(CURDATE(), i.due_date), 0) * (
        (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) * (
          COALESCE((
            SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 
            FROM invoice_settings 
            WHERE setting_key = 'late_fee_daily_rate' 
            LIMIT 1
          ), 0.005)
        )
      )
    ELSE 0
  END AS late_fee_amount,
  
  -- Calculate arrears (accumulated penalties from overdue installment schedules)
  COALESCE((
    SELECT SUM(
      CASE 
        WHEN isch.due_date < CURDATE() 
          AND isch.status IN ('pending', 'overdue', 'partial')
          AND DATEDIFF(CURDATE(), isch.due_date) >= COALESCE((
            SELECT CAST(setting_value AS UNSIGNED) 
            FROM invoice_settings 
            WHERE setting_key = 'penalty_grace_period_days' 
            LIMIT 1
          ), 7) THEN
          (COALESCE(isch.amount_due, 0) - COALESCE(isch.paid_amount, 0)) * 
          COALESCE((
            SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 
            FROM invoice_settings 
            WHERE setting_key = 'penalty_rate_monthly' 
            LIMIT 1
          ), 0.03) *
          GREATEST(CEIL(DATEDIFF(CURDATE(), isch.due_date) / 30.0), 1)
        ELSE 0
      END
    )
    FROM installment_contracts ic
    INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
    WHERE ic.invoice_id = i.id
      AND ic.status = 'active'
  ), 0) AS arrears_amount,
  
  -- Balance remaining (original amount - payments)
  (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) AS balance_remaining,
  
  -- Total balance due (including interest, late fees, and arrears)
  (
    (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) +
    COALESCE((
      SELECT SUM(COALESCE(isch.interest_amount, 0))
      FROM installment_contracts ic
      INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
      WHERE ic.invoice_id = i.id
        AND isch.status IN ('pending', 'overdue', 'partial')
    ), 0) +
    CASE 
      WHEN i.payment_type != 'installment' 
        AND i.due_date IS NOT NULL 
        AND CURDATE() > i.due_date 
        AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 
        GREATEST(DATEDIFF(CURDATE(), i.due_date), 0) * (
          (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) * (
            COALESCE((
              SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 
              FROM invoice_settings 
              WHERE setting_key = 'late_fee_daily_rate' 
              LIMIT 1
            ), 0.005)
          )
        )
      ELSE 0
    END +
    COALESCE((
      SELECT SUM(
        CASE 
          WHEN isch.due_date < CURDATE() 
            AND isch.status IN ('pending', 'overdue', 'partial')
            AND DATEDIFF(CURDATE(), isch.due_date) >= COALESCE((
              SELECT CAST(setting_value AS UNSIGNED) 
              FROM invoice_settings 
              WHERE setting_key = 'penalty_grace_period_days' 
              LIMIT 1
            ), 7) THEN
            (COALESCE(isch.amount_due, 0) - COALESCE(isch.paid_amount, 0)) * 
            COALESCE((
              SELECT CAST(setting_value AS DECIMAL(10,4)) / 100 
              FROM invoice_settings 
              WHERE setting_key = 'penalty_rate_monthly' 
              LIMIT 1
            ), 0.03) *
            GREATEST(CEIL(DATEDIFF(CURDATE(), isch.due_date) / 30.0), 1)
          ELSE 0
        END
      )
      FROM installment_contracts ic
      INNER JOIN installment_schedule isch ON ic.id = isch.contract_id
      WHERE ic.invoice_id = i.id
        AND ic.status = 'active'
    ), 0)
  ) AS total_balance_due,
  
  -- Computed status
  CASE 
    WHEN COALESCE(SUM(r.amount_paid),0) >= i.total_amount THEN 'paid'
    WHEN i.due_date IS NOT NULL AND CURDATE() > i.due_date AND (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 'late'
    WHEN (i.total_amount - COALESCE(SUM(r.amount_paid), 0)) > 0 THEN 'pending'
    ELSE 'paid'
  END AS computed_status

FROM invoices i
LEFT JOIN receipts r ON r.invoice_id = i.id
GROUP BY i.id, i.invoice_number, i.order_id, i.customer_id, i.transaction_type, i.payment_type, 
         i.subtotal, i.vat_amount, i.total_amount, i.generated_at, i.due_date, i.payment_status
";

if($conn->query($view_sql)){
    echo "   ✓ View created successfully!\n";
} else {
    echo "   ✗ Failed to create view: " . $conn->error . "\n";
    echo "\n   SQL Error Details:\n";
    echo "   " . $conn->error . "\n";
    exit(1);
}

// Verify the view
echo "\n5. Verifying view structure...\n";
$columns_check = $conn->query("SHOW COLUMNS FROM invoice_financials");
$required_columns = ['interest_amount', 'arrears_amount', 'total_balance_due', 'late_fee_amount', 'balance_remaining'];
$found_columns = [];
$all_found = true;

if($columns_check){
    while($col = $columns_check->fetch_assoc()){
        $found_columns[] = $col['Field'];
    }
    
    foreach($required_columns as $req_col){
        if(in_array($req_col, $found_columns)){
            echo "   ✓ Column '{$req_col}' exists\n";
        } else {
            echo "   ✗ Column '{$req_col}' MISSING\n";
            $all_found = false;
        }
    }
} else {
    echo "   ✗ ERROR: Could not query invoice_financials view\n";
    echo "   Error: " . $conn->error . "\n";
    $all_found = false;
}

// Test query
echo "\n6. Testing view query...\n";
try {
    $test = $conn->query("SELECT id, interest_amount, arrears_amount, total_balance_due, late_fee_amount, balance_remaining 
                          FROM invoice_financials LIMIT 1");
    if($test){
        echo "   ✓ View query test successful\n";
        $row = $test->fetch_assoc();
        if($row){
            echo "   ✓ View returns data correctly\n";
        }
    } else {
        echo "   ✗ View query test failed: " . $conn->error . "\n";
        $all_found = false;
    }
} catch(Exception $e){
    echo "   ✗ View query test failed: " . $e->getMessage() . "\n";
    $all_found = false;
}

echo "\n=== Summary ===\n";
if($all_found){
    echo "✓ All columns created successfully!\n";
    echo "✓ View is working correctly!\n";
    echo "\nYou can now run: php verify_invoice_calculations.php\n";
} else {
    echo "✗ Some issues were found. Please check the errors above.\n";
}

?>

