<?php
require_once('config.php');

echo "=== Applying Receipt Creation Fix ===\n\n";

// Read and execute SQL file
$sql_file = 'fix_receipt_creation.sql';
if(!file_exists($sql_file)){
    die("Error: SQL file '{$sql_file}' not found!\n");
}

$sql_content = file_get_contents($sql_file);
$statements = array_filter(array_map('trim', explode(';', $sql_content)));

foreach($statements as $index => $statement){
    $statement = trim($statement);
    if(empty($statement) || preg_match('/^--/', $statement) || preg_match('/^SELECT/i', $statement)){
        continue;
    }
    
    try {
        if($conn->query($statement)){
            echo "✓ Statement " . ($index + 1) . " executed successfully\n";
        } else {
            throw new Exception($conn->error);
        }
    } catch(Exception $e){
        // Ignore "already exists" errors
        if(strpos($e->getMessage(), 'already exists') !== false || 
           strpos($e->getMessage(), 'Duplicate') !== false){
            echo "  (Skipped - already exists)\n";
        } else {
            echo "✗ Statement " . ($index + 1) . " failed: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== Verification ===\n";

// Check receipts table
$result = $conn->query("DESCRIBE receipts");
$has_archive_flag = false;
while($row = $result->fetch_assoc()){
    if($row['Field'] == 'archive_flag'){
        $has_archive_flag = true;
        echo "✓ archive_flag column exists with default: " . ($row['Default'] ?? 'NULL') . "\n";
    }
}

if(!$has_archive_flag){
    echo "✗ archive_flag column missing\n";
}

// Check unique constraint on receipt_number
$result = $conn->query("SHOW INDEX FROM receipts WHERE Key_name = 'unique_receipt_number'");
if($result && $result->num_rows > 0){
    echo "✓ Unique constraint on receipt_number exists\n";
} else {
    echo "⚠ Unique constraint on receipt_number may be missing (check manually)\n";
}

echo "\n=== Done ===\n";

?>

