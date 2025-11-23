<?php
/**
 * Apply Invoice Calculations Fix
 * This script applies the invoice_financials view update with arrears computation
 */

require_once('config.php');

echo "=== Applying Invoice Calculations Fix ===\n\n";

// Read the SQL file (try v2 first, then fallback to original)
$sql_file = 'fix_invoice_calculations_with_arrears_v2.sql';
if(!file_exists($sql_file)){
    $sql_file = 'fix_invoice_calculations_with_arrears.sql';
}
if(!file_exists($sql_file)){
    die("Error: SQL file '{$sql_file}' not found!\n");
}

$sql_content = file_get_contents($sql_file);

// Split by semicolons but preserve DELIMITER blocks
$statements = [];
$current_statement = '';
$in_delimiter_block = false;
$delimiter = ';';

$lines = explode("\n", $sql_content);
foreach($lines as $line){
    $trimmed = trim($line);
    
    // Check for DELIMITER command
    if(preg_match('/^DELIMITER\s+(.+)$/i', $trimmed, $matches)){
        $delimiter = trim($matches[1]);
        $in_delimiter_block = ($delimiter != ';');
        continue;
    }
    
    // Check if line ends with current delimiter
    if($in_delimiter_block){
        $current_statement .= $line . "\n";
        if(preg_match('/' . preg_quote($delimiter, '/') . '\s*$/', $trimmed)){
            $statements[] = trim($current_statement);
            $current_statement = '';
        }
    } else {
        $current_statement .= $line . "\n";
        if(preg_match('/;\s*$/', $trimmed) && !empty(trim($current_statement))){
            $statements[] = trim($current_statement);
            $current_statement = '';
        }
    }
}

// Add any remaining statement
if(!empty(trim($current_statement))){
    $statements[] = trim($current_statement);
}

echo "Found " . count($statements) . " SQL statements to execute\n\n";

$success_count = 0;
$error_count = 0;
$errors = [];

foreach($statements as $index => $statement){
    $statement = trim($statement);
    
    // Skip empty statements and comments
    if(empty($statement) || preg_match('/^--/', $statement) || preg_match('/^\/\*/', $statement)){
        continue;
    }
    
    // Skip SELECT statements that are just for verification
    if(preg_match('/^SELECT\s+[\'"]/', $statement, $matches)){
        echo "Skipping verification statement: " . substr($statement, 0, 50) . "...\n";
        continue;
    }
    
    // Skip SHOW statements
    if(preg_match('/^SHOW\s+/i', $statement)){
        continue;
    }
    
    try {
        // Execute statement
        if($conn->multi_query($statement)){
            // Handle multiple results
            do {
                if($result = $conn->store_result()){
                    $result->free();
                }
            } while($conn->next_result());
        } else {
            // Single query
            $result = $conn->query($statement);
        }
        
        if($conn->error){
            throw new Exception($conn->error);
        }
        
        $success_count++;
        echo "✓ Statement " . ($index + 1) . " executed successfully\n";
        
    } catch(Exception $e){
        $error_count++;
        $error_msg = "✗ Statement " . ($index + 1) . " failed: " . $e->getMessage();
        echo $error_msg . "\n";
        $errors[] = [
            'statement' => substr($statement, 0, 100) . "...",
            'error' => $e->getMessage()
        ];
    }
}

echo "\n=== Execution Summary ===\n";
echo "Successful: {$success_count}\n";
echo "Errors: {$error_count}\n\n";

if($error_count > 0){
    echo "=== Errors ===\n";
    foreach($errors as $error){
        echo "Error: {$error['error']}\n";
        echo "Statement: {$error['statement']}\n\n";
    }
}

// Verify the view was created correctly
echo "\n=== Verifying View Structure ===\n";
$columns_check = $conn->query("SHOW COLUMNS FROM invoice_financials");
$required_columns = ['interest_amount', 'arrears_amount', 'total_balance_due', 'late_fee_amount', 'balance_remaining'];
$found_columns = [];

if($columns_check){
    while($col = $columns_check->fetch_assoc()){
        $found_columns[] = $col['Field'];
    }
    
    foreach($required_columns as $req_col){
        if(in_array($req_col, $found_columns)){
            echo "✓ Column '{$req_col}' exists\n";
        } else {
            echo "✗ Column '{$req_col}' MISSING\n";
        }
    }
} else {
    echo "✗ ERROR: Could not query invoice_financials view\n";
    echo "Error: " . $conn->error . "\n";
}

// Test a simple query
echo "\n=== Testing View Query ===\n";
try {
    $test = $conn->query("SELECT id, interest_amount, arrears_amount, total_balance_due FROM invoice_financials LIMIT 1");
    if($test){
        echo "✓ View query test successful\n";
    } else {
        echo "✗ View query test failed: " . $conn->error . "\n";
    }
} catch(Exception $e){
    echo "✗ View query test failed: " . $e->getMessage() . "\n";
}

echo "\n=== Done ===\n";
echo "\nIf errors occurred, please check:\n";
echo "1. All required tables exist (invoices, receipts, installment_contracts, installment_schedule)\n";
echo "2. MySQL user has CREATE VIEW and ALTER privileges\n";
echo "3. No syntax errors in the SQL file\n";

?>

