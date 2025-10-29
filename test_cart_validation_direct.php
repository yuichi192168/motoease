<?php
require_once('./config.php');

echo "=== DIRECT CART VALIDATION TEST ===\n";

// Test the cart validation function directly
$client_id = 2; // Test with client ID 2

echo "Testing cart validation for client ID: $client_id\n\n";

// Get cart items with product categories
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

// Check if more than one motorcycle in cart
if($motorcycle_count > 1){
    echo "ERROR: More than one motorcycle in cart!\n";
    echo "Motorcycle items:\n";
    foreach($motorcycle_items as $item) {
        echo "  - " . $item['name'] . "\n";
    }
} else {
    echo "Cart validation passed for motorcycle count.\n";
}

// If cart contains only parts, no credit application needed
if($has_parts_only){
    echo "Cart contains only parts - no credit application needed.\n";
} else {
    echo "Cart contains motorcycle - checking credit application status...\n";
    
    // Check if credit application is completed
    $credit_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();
    $application_completed = $credit_status && $credit_status['credit_application_completed'] == 1;
    
    echo "Credit application completed: " . ($application_completed ? 'Yes' : 'No') . "\n";
    
    if($application_completed) {
        echo "Cart validation passed. Ready for checkout.\n";
    } else {
        echo "Credit application required for motorcycle purchase.\n";
    }
}

// Test the actual Master class function
echo "\n=== TESTING MASTER CLASS FUNCTION ===\n";

// Include the Master class
require_once('./classes/Master.php');

// Create Master instance
$master = new Master();
$master->conn = $conn;
$master->settings = new stdClass();
$master->settings->userdata = function($key) use ($client_id) {
    if($key == 'id') return $client_id;
    return null;
};

// Test the validation function
$result = $master->validate_cart_checkout();
$response = json_decode($result, true);

echo "Master class validation result:\n";
echo "Status: " . $response['status'] . "\n";
echo "Message: " . $response['msg'] . "\n";
if(isset($response['requires_credit_application'])) {
    echo "Requires credit application: " . ($response['requires_credit_application'] ? 'Yes' : 'No') . "\n";
}
if(isset($response['application_completed'])) {
    echo "Application completed: " . ($response['application_completed'] ? 'Yes' : 'No') . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
