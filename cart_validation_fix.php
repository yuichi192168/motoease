<?php
/**
 * Cart Validation Fix Script
 * This script fixes common cart validation issues
 */

require_once('./config.php');

echo "=== CART VALIDATION FIX SCRIPT ===\n";

// 1. Add missing credit_application_completed column if it doesn't exist
echo "1. Checking credit_application_completed column...\n";
$column_check = $conn->query("SHOW COLUMNS FROM client_list LIKE 'credit_application_completed'");
if($column_check->num_rows == 0) {
    echo "Adding credit_application_completed column...\n";
    $conn->query("ALTER TABLE client_list ADD COLUMN credit_application_completed tinyint(1) DEFAULT 0 AFTER password");
    echo "Column added successfully.\n";
} else {
    echo "Column already exists.\n";
}

// 2. Clean up invalid cart items
echo "\n2. Cleaning up invalid cart items...\n";
$cleanup_queries = [
    "DELETE FROM cart_list WHERE id = 0 OR product_id = 0 OR product_id IS NULL",
    "DELETE c FROM cart_list c LEFT JOIN product_list p ON c.product_id = p.id WHERE p.id IS NULL",
    "DELETE c FROM cart_list c LEFT JOIN client_list cl ON c.client_id = cl.id WHERE cl.id IS NULL",
    "UPDATE cart_list SET quantity = 1 WHERE quantity <= 0 OR quantity IS NULL",
    "UPDATE cart_list SET date_added = NOW() WHERE date_added IS NULL"
];

foreach($cleanup_queries as $query) {
    $result = $conn->query($query);
    if($result) {
        echo "✓ " . $conn->affected_rows . " records affected\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
}

// 3. Add proper indexes for better performance
echo "\n3. Adding indexes for better performance...\n";
$index_queries = [
    "CREATE INDEX IF NOT EXISTS idx_cart_list_client_id ON cart_list (client_id)",
    "CREATE INDEX IF NOT EXISTS idx_cart_list_product_id ON cart_list (product_id)",
    "CREATE INDEX IF NOT EXISTS idx_cart_list_date_added ON cart_list (date_added)"
];

foreach($index_queries as $query) {
    $result = $conn->query($query);
    if($result) {
        echo "✓ Index created successfully\n";
    } else {
        echo "✗ Error: " . $conn->error . "\n";
    }
}

// 4. Test cart validation function
echo "\n4. Testing cart validation function...\n";
$test_clients = [2, 3, 4]; // Test with different client IDs

foreach($test_clients as $client_id) {
    echo "Testing client ID: $client_id\n";
    
    // Check if client exists
    $client_check = $conn->query("SELECT id, firstname, lastname, credit_application_completed FROM client_list WHERE id = '{$client_id}'");
    if($client_check->num_rows == 0) {
        echo "  - Client not found, skipping...\n";
        continue;
    }
    
    $client = $client_check->fetch_assoc();
    
    // Get cart items
    $cart_items = $conn->query("SELECT c.*, p.name, p.price, cat.category 
                               FROM cart_list c 
                               INNER JOIN product_list p ON c.product_id = p.id 
                               INNER JOIN categories cat ON p.category_id = cat.id 
                               WHERE c.client_id = '{$client_id}'");
    
    if($cart_items->num_rows == 0){
        echo "  - Cart is empty\n";
        continue;
    }
    
    $motorcycle_count = 0;
    $has_parts_only = true;
    
    while($item = $cart_items->fetch_assoc()){
        if(strtolower($item['category']) == 'motorcycles'){
            $motorcycle_count++;
            $has_parts_only = false;
        }
    }
    
    echo "  - Items: " . $cart_items->num_rows . ", Motorcycles: $motorcycle_count, Parts only: " . ($has_parts_only ? 'Yes' : 'No') . "\n";
    
    if($motorcycle_count > 1) {
        echo "  - ERROR: More than one motorcycle in cart!\n";
    } else {
        echo "  - ✓ Cart validation passed\n";
    }
}

// 5. Create a test cart validation endpoint
echo "\n5. Creating test cart validation endpoint...\n";
$test_endpoint = '<?php
require_once("./config.php");
header("Content-Type: application/json");

$client_id = isset($_GET["client_id"]) ? intval($_GET["client_id"]) : 0;
if($client_id <= 0) {
    echo json_encode(["status" => "failed", "msg" => "Invalid client ID"]);
    exit;
}

// Get cart items with product categories
$cart_items = $conn->query("SELECT c.*, p.name, p.price, cat.category 
                           FROM cart_list c 
                           INNER JOIN product_list p ON c.product_id = p.id 
                           INNER JOIN categories cat ON p.category_id = cat.id 
                           WHERE c.client_id = \'{$client_id}\'");

if($cart_items->num_rows == 0){
    echo json_encode(["status" => "failed", "msg" => "Your cart is empty."]);
    exit;
}

$motorcycle_count = 0;
$has_parts_only = true;
$motorcycle_items = array();

while($item = $cart_items->fetch_assoc()){
    if(strtolower($item["category"]) == "motorcycles"){
        $motorcycle_count++;
        $motorcycle_items[] = $item;
        $has_parts_only = false;
    }
}

// Check if more than one motorcycle in cart
if($motorcycle_count > 1){
    echo json_encode([
        "status" => "failed",
        "msg" => "You can only checkout one motorcycle at a time. Please remove other motorcycles from your cart.",
        "motorcycle_items" => $motorcycle_items
    ]);
    exit;
}

// If cart contains only parts, no credit application needed
if($has_parts_only){
    echo json_encode([
        "status" => "success",
        "requires_credit_application" => false,
        "msg" => "Cart validation passed. Parts-only order can proceed directly."
    ]);
} else {
    // Cart contains motorcycle, check if credit application is completed
    $credit_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = \'{$client_id}\'")->fetch_assoc();
    $application_completed = $credit_status && $credit_status["credit_application_completed"] == 1;
    
    echo json_encode([
        "status" => "success",
        "requires_credit_application" => !$application_completed,
        "application_completed" => $application_completed,
        "msg" => $application_completed ? "Cart validation passed. Ready for checkout." : "Credit application required for motorcycle purchase."
    ]);
}
?>';

file_put_contents('test_cart_validation_endpoint.php', $test_endpoint);
echo "✓ Test endpoint created: test_cart_validation_endpoint.php\n";

// 6. Test the endpoint
echo "\n6. Testing the endpoint...\n";
$test_url = "http://localhost/bpsms/test_cart_validation_endpoint.php?client_id=2";
$response = file_get_contents($test_url);
$data = json_decode($response, true);

if($data) {
    echo "✓ Endpoint working correctly\n";
    echo "Status: " . $data['status'] . "\n";
    echo "Message: " . $data['msg'] . "\n";
    if(isset($data['requires_credit_application'])) {
        echo "Requires credit application: " . ($data['requires_credit_application'] ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "✗ Endpoint test failed\n";
}

echo "\n=== CART VALIDATION FIX COMPLETE ===\n";
echo "You can now test the cart validation by visiting:\n";
echo "http://localhost/bpsms/test_cart_validation_endpoint.php?client_id=2\n";
?>
