<?php
require_once('./config.php');

echo "=== SIMPLE CART VALIDATION TEST ===\n";

// Test with client ID 2
$client_id = 2;

echo "Testing cart validation for client ID: $client_id\n\n";

// Check if client exists
$client_check = $conn->query("SELECT id, firstname, lastname, credit_application_completed FROM client_list WHERE id = '{$client_id}'");
if($client_check->num_rows == 0) {
    echo "ERROR: Client not found!\n";
    exit;
}

$client = $client_check->fetch_assoc();
echo "Client: " . $client['firstname'] . " " . $client['lastname'] . "\n";
echo "Credit application completed: " . ($client['credit_application_completed'] ? 'Yes' : 'No') . "\n\n";

// Get cart items
$cart_items = $conn->query("SELECT c.*, p.name, p.price, cat.category 
                           FROM cart_list c 
                           INNER JOIN product_list p ON c.product_id = p.id 
                           INNER JOIN categories cat ON p.category_id = cat.id 
                           WHERE c.client_id = '{$client_id}'");

if($cart_items->num_rows == 0){
    echo "Cart is empty for client $client_id\n";
    exit;
}

echo "Found " . $cart_items->num_rows . " items in cart:\n";

$motorcycle_count = 0;
$has_parts_only = true;
$motorcycle_items = array();

while($item = $cart_items->fetch_assoc()){
    echo "- Item: " . $item['name'] . " (Category: " . $item['category'] . ")\n";
    if(strtolower($item['category']) == 'motorcycles'){
        $motorcycle_count++;
        $motorcycle_items[] = $item;
        $has_parts_only = false;
    }
}

echo "\nMotorcycle count: $motorcycle_count\n";
echo "Has parts only: " . ($has_parts_only ? 'Yes' : 'No') . "\n";

// Simulate the validation logic
if($motorcycle_count > 1){
    echo "ERROR: More than one motorcycle in cart!\n";
} else {
    echo "Cart validation passed for motorcycle count.\n";
}

if($has_parts_only){
    echo "Cart contains only parts - no credit application needed.\n";
    echo "RESULT: SUCCESS - Cart validation passed. Parts-only order can proceed directly.\n";
} else {
    echo "Cart contains motorcycle - checking credit application status...\n";
    
    if($client['credit_application_completed'] == 1) {
        echo "RESULT: SUCCESS - Cart validation passed. Ready for checkout.\n";
    } else {
        echo "RESULT: SUCCESS - Credit application required for motorcycle purchase.\n";
    }
}

// Check for any database errors
echo "\n=== DATABASE ERROR CHECK ===\n";
$error_log = $conn->query("SHOW WARNINGS");
if($error_log && $error_log->num_rows > 0) {
    echo "Database warnings found:\n";
    while($row = $error_log->fetch_assoc()) {
        echo "- " . $row['Level'] . ": " . $row['Message'] . "\n";
    }
} else {
    echo "No database warnings found.\n";
}

// Check if credit_application_completed column exists
echo "\n=== COLUMN CHECK ===\n";
$columns = $conn->query("SHOW COLUMNS FROM client_list LIKE 'credit_application_completed'");
if($columns->num_rows > 0) {
    echo "credit_application_completed column exists.\n";
} else {
    echo "ERROR: credit_application_completed column does not exist!\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
