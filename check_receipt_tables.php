<?php
require_once('config.php');

echo "=== Checking Receipt Tables Structure ===\n\n";

// Check receipts table structure
echo "1. Receipts table structure:\n";
$result = $conn->query("DESCRIBE receipts");
if($result){
    $columns = [];
    while($row = $result->fetch_assoc()){
        $columns[] = $row['Field'];
        echo "   - {$row['Field']}: {$row['Type']} " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
} else {
    echo "   ERROR: " . $conn->error . "\n";
}

// Check invoices table structure
echo "\n2. Invoices table structure (relevant columns):\n";
$result = $conn->query("DESCRIBE invoices");
if($result){
    while($row = $result->fetch_assoc()){
        if(in_array($row['Field'], ['id', 'customer_id', 'order_id', 'payment_status', 'total_amount'])){
            echo "   - {$row['Field']}: {$row['Type']}\n";
        }
    }
} else {
    echo "   ERROR: " . $conn->error . "\n";
}

// Check for missing columns or mismatches
echo "\n3. Checking for issues:\n";

// Check if receipts table has all required columns
$required_columns = ['id', 'invoice_id', 'receipt_number', 'customer_id', 'amount_paid', 'payment_method', 'received_by', 'issued_at'];
$missing = [];
foreach($required_columns as $col){
    if(!in_array($col, $columns)){
        $missing[] = $col;
    }
}

if(!empty($missing)){
    echo "   ✗ Missing columns: " . implode(', ', $missing) . "\n";
} else {
    echo "   ✓ All required columns exist\n";
}

// Check for archive_flag (optional)
if(in_array('archive_flag', $columns)){
    echo "   ✓ archive_flag column exists (optional)\n";
}

// Check if invoice_financials is a view or table
echo "\n4. Checking invoice_financials:\n";
$result = $conn->query("SHOW FULL TABLES WHERE Tables_in_" . $conn->query("SELECT DATABASE()")->fetch_row()[0] . " = 'invoice_financials'");
if($result && $result->num_rows > 0){
    $row = $result->fetch_array();
    echo "   Type: " . ($row[1] == 'VIEW' ? 'VIEW' : 'TABLE') . "\n";
    
    // Check columns
    $result2 = $conn->query("DESCRIBE invoice_financials");
    if($result2){
        echo "   Columns:\n";
        while($row2 = $result2->fetch_assoc()){
            echo "     - {$row2['Field']}: {$row2['Type']}\n";
        }
    }
} else {
    echo "   ✗ invoice_financials does not exist\n";
}

// Test receipt creation query structure
echo "\n5. Testing receipt creation query structure:\n";
$test_data = [
    'invoice_id' => 1,
    'receipt_number' => 'TEST-001',
    'customer_id' => 1,
    'amount_paid' => 100.00,
    'payment_method' => 'cash',
    'payment_reference' => '',
    'received_by' => 1,
    'acknowledgment_note' => 'Test'
];

$fields = implode(',', array_keys($test_data));
$values = "'" . implode("','", array_values($test_data)) . "'";
$test_query = "INSERT INTO receipts ({$fields}) VALUES ({$values})";

echo "   Test query: " . substr($test_query, 0, 100) . "...\n";

// Check for SQL injection vulnerabilities (the current code has issues)
echo "\n6. Security check:\n";
echo "   ⚠ WARNING: Current code uses string concatenation (SQL injection risk)\n";
echo "   Recommendation: Use prepared statements\n";

echo "\n=== Done ===\n";

?>

