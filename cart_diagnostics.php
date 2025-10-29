<?php
require_once('./config.php');

echo "=== CART DATABASE DIAGNOSTICS ===\n";
echo "Database: " . $conn->query('SELECT DATABASE()')->fetch_array()[0] . "\n";
echo "Connection: " . ($conn->ping() ? 'OK' : 'FAILED') . "\n\n";

echo "=== CART_LIST TABLE STRUCTURE ===\n";
$result = $conn->query('DESCRIBE cart_list');
if($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . ' - ' . $row['Default'] . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n=== CART_LIST DATA ===\n";
$result = $conn->query('SELECT * FROM cart_list ORDER BY id DESC LIMIT 10');
if($result) {
    echo "Total rows: " . $result->num_rows . "\n";
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Client: " . $row['client_id'] . ", Product: " . $row['product_id'] . ", Qty: " . $row['quantity'] . ", Date: " . $row['date_added'] . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n=== INVALID CART ITEMS ===\n";
$result = $conn->query('SELECT c.* FROM cart_list c LEFT JOIN client_list cl ON c.client_id = cl.id WHERE cl.id IS NULL');
echo "Orphaned client records: " . $result->num_rows . "\n";

$result = $conn->query('SELECT c.* FROM cart_list c LEFT JOIN product_list p ON c.product_id = p.id WHERE p.id IS NULL');
echo "Orphaned product records: " . $result->num_rows . "\n";

$result = $conn->query('SELECT * FROM cart_list WHERE id = 0 OR product_id = 0 OR product_id IS NULL');
echo "Invalid ID/Product records: " . $result->num_rows . "\n";

$result = $conn->query('SELECT * FROM cart_list WHERE quantity <= 0 OR quantity IS NULL');
echo "Invalid quantity records: " . $result->num_rows . "\n";

echo "\n=== CART VALIDATION ERRORS ===\n";
$result = $conn->query('SELECT c.*, p.name as product_name, cl.firstname, cl.lastname 
                       FROM cart_list c 
                       LEFT JOIN product_list p ON c.product_id = p.id 
                       LEFT JOIN client_list cl ON c.client_id = cl.id 
                       WHERE c.id = 0 OR c.product_id = 0 OR c.client_id = 0 
                       OR p.id IS NULL OR cl.id IS NULL 
                       OR c.quantity <= 0 OR c.quantity IS NULL');
if($result && $result->num_rows > 0) {
    echo "Found " . $result->num_rows . " invalid cart items:\n";
    while($row = $result->fetch_assoc()) {
        echo "- ID: " . $row['id'] . ", Client: " . $row['firstname'] . " " . $row['lastname'] . ", Product: " . $row['product_name'] . ", Qty: " . $row['quantity'] . "\n";
    }
} else {
    echo "No invalid cart items found.\n";
}

echo "\n=== CART TABLE CONSTRAINTS ===\n";
$result = $conn->query("SELECT 
    CONSTRAINT_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'cart_list' 
AND REFERENCED_TABLE_NAME IS NOT NULL");
if($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['CONSTRAINT_NAME'] . " -> " . $row['REFERENCED_TABLE_NAME'] . "." . $row['REFERENCED_COLUMN_NAME'] . "\n";
    }
} else {
    echo "No foreign key constraints found.\n";
}

echo "\n=== CART AUTO_INCREMENT STATUS ===\n";
$result = $conn->query("SHOW TABLE STATUS LIKE 'cart_list'");
if($result) {
    $row = $result->fetch_assoc();
    echo "Auto_increment: " . $row['Auto_increment'] . "\n";
    echo "Engine: " . $row['Engine'] . "\n";
    echo "Rows: " . $row['Rows'] . "\n";
} else {
    echo "Error getting table status: " . $conn->error . "\n";
}
?>
