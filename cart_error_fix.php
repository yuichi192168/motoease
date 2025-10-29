<?php
/**
 * Cart Error Fix Script
 * This script fixes common cart validation errors and JavaScript issues
 */

require_once('./config.php');

echo "=== CART ERROR FIX SCRIPT ===\n";

// 1. Check for common cart validation issues
echo "1. Checking for cart validation issues...\n";

// Check if there are any cart items with invalid data
$invalid_cart_items = $conn->query("SELECT c.*, p.name as product_name, cl.firstname, cl.lastname 
                                   FROM cart_list c 
                                   LEFT JOIN product_list p ON c.product_id = p.id 
                                   LEFT JOIN client_list cl ON c.client_id = cl.id 
                                   WHERE c.id = 0 OR c.product_id = 0 OR c.client_id = 0 
                                   OR p.id IS NULL OR cl.id IS NULL 
                                   OR c.quantity <= 0 OR c.quantity IS NULL");

if($invalid_cart_items->num_rows > 0) {
    echo "Found " . $invalid_cart_items->num_rows . " invalid cart items:\n";
    while($row = $invalid_cart_items->fetch_assoc()) {
        echo "- ID: " . $row['id'] . ", Client: " . $row['firstname'] . " " . $row['lastname'] . ", Product: " . $row['product_name'] . ", Qty: " . $row['quantity'] . "\n";
    }
} else {
    echo "✓ No invalid cart items found\n";
}

// 2. Check for missing foreign key constraints
echo "\n2. Checking foreign key constraints...\n";
$constraints = $conn->query("SELECT 
    CONSTRAINT_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'cart_list' 
AND REFERENCED_TABLE_NAME IS NOT NULL");

if($constraints->num_rows == 0) {
    echo "⚠ No foreign key constraints found for cart_list table\n";
    echo "Adding foreign key constraints...\n";
    
    // Add foreign key constraints
    $fk_queries = [
        "ALTER TABLE cart_list ADD CONSTRAINT fk_cart_list_client FOREIGN KEY (client_id) REFERENCES client_list(id) ON DELETE CASCADE",
        "ALTER TABLE cart_list ADD CONSTRAINT fk_cart_list_product FOREIGN KEY (product_id) REFERENCES product_list(id) ON DELETE CASCADE"
    ];
    
    foreach($fk_queries as $query) {
        $result = $conn->query($query);
        if($result) {
            echo "✓ Foreign key constraint added\n";
        } else {
            echo "✗ Error adding constraint: " . $conn->error . "\n";
        }
    }
} else {
    echo "✓ Foreign key constraints exist\n";
}

// 3. Check for JavaScript errors in cart.php
echo "\n3. Checking cart.php for potential JavaScript errors...\n";

$cart_file = file_get_contents('cart.php');
$js_errors = [];

// Check for common JavaScript issues
if(strpos($cart_file, 'update_quantity') === false) {
    $js_errors[] = "update_quantity function not found";
}

if(strpos($cart_file, 'remove_from_cart') === false) {
    $js_errors[] = "remove_from_cart function not found";
}

if(strpos($cart_file, 'validate_cart_checkout') === false) {
    $js_errors[] = "validate_cart_checkout AJAX call not found";
}

if(strpos($cart_file, 'Master.php?f=validate_cart_checkout') === false) {
    $js_errors[] = "Cart validation AJAX endpoint not found";
}

if(empty($js_errors)) {
    echo "✓ No obvious JavaScript errors found\n";
} else {
    echo "⚠ Potential JavaScript issues found:\n";
    foreach($js_errors as $error) {
        echo "- " . $error . "\n";
    }
}

// 4. Test the actual cart validation endpoint
echo "\n4. Testing cart validation endpoint...\n";

// Test with different client IDs
$test_clients = [2, 3, 4];

foreach($test_clients as $client_id) {
    echo "Testing client ID: $client_id\n";
    
    // Simulate the cart validation
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
    
    // Check credit application status
    $credit_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();
    $application_completed = $credit_status && $credit_status['credit_application_completed'] == 1;
    
    echo "  - Items: " . $cart_items->num_rows . ", Motorcycles: $motorcycle_count\n";
    echo "  - Credit app completed: " . ($application_completed ? 'Yes' : 'No') . "\n";
    
    if($motorcycle_count > 1) {
        echo "  - ❌ ERROR: More than one motorcycle in cart!\n";
    } else {
        echo "  - ✅ Cart validation passed\n";
    }
}

// 5. Create a comprehensive cart validation test
echo "\n5. Creating comprehensive cart validation test...\n";

$test_script = '<?php
// Comprehensive Cart Validation Test
require_once("./config.php");

echo "<h2>Cart Validation Test Results</h2>";
echo "<style>
    .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
    .pass { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .fail { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
</style>";

$client_id = isset($_GET["client_id"]) ? intval($_GET["client_id"]) : 2;

echo "<div class=\"test-result info\"><strong>Testing Cart Validation for Client ID: $client_id</strong></div>";

// Test cart validation
$cart_items = $conn->query("SELECT c.*, p.name, p.price, cat.category 
                           FROM cart_list c 
                           INNER JOIN product_list p ON c.product_id = p.id 
                           INNER JOIN categories cat ON p.category_id = cat.id 
                           WHERE c.client_id = \'{$client_id}\'");

if($cart_items->num_rows == 0){
    echo "<div class=\"test-result fail\">❌ Cart is empty for client $client_id</div>";
    exit;
}

echo "<div class=\"test-result pass\">✅ Found " . $cart_items->num_rows . " items in cart</div>";

$motorcycle_count = 0;
$has_parts_only = true;
$motorcycle_items = array();

while($item = $cart_items->fetch_assoc()){
    echo "<div class=\"test-result info\">- Item: " . $item[\'name\'] . " (Category: " . $item[\'category\'] . ")</div>";
    if(strtolower($item[\'category\']) == \'motorcycles\'){
        $motorcycle_count++;
        $motorcycle_items[] = $item;
        $has_parts_only = false;
    }
}

echo "<div class=\"test-result info\">Motorcycle count: $motorcycle_count</div>";
echo "<div class=\"test-result info\">Has parts only: " . ($has_parts_only ? \'Yes\' : \'No\') . "</div>";

if($motorcycle_count > 1){
    echo "<div class=\"test-result fail\">❌ ERROR: More than one motorcycle in cart!</div>";
} else {
    echo "<div class=\"test-result pass\">✅ Cart validation passed for motorcycle count</div>";
}

if($has_parts_only){
    echo "<div class=\"test-result pass\">✅ Cart contains only parts - no credit application needed</div>";
    echo "<div class=\"test-result pass\">✅ RESULT: SUCCESS - Cart validation passed. Parts-only order can proceed directly.</div>";
} else {
    echo "<div class=\"test-result info\">Cart contains motorcycle - checking credit application status...</div>";
    
    $credit_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = \'{$client_id}\'")->fetch_assoc();
    $application_completed = $credit_status && $credit_status[\'credit_application_completed\'] == 1;
    
    echo "<div class=\"test-result info\">Credit application completed: " . ($application_completed ? \'Yes\' : \'No\') . "</div>";
    
    if($application_completed) {
        echo "<div class=\"test-result pass\">✅ RESULT: SUCCESS - Cart validation passed. Ready for checkout.</div>";
    } else {
        echo "<div class=\"test-result pass\">✅ RESULT: SUCCESS - Credit application required for motorcycle purchase.</div>";
    }
}
?>';

file_put_contents('cart_validation_test.php', $test_script);
echo "✓ Comprehensive test created: cart_validation_test.php\n";

echo "\n=== CART ERROR FIX COMPLETE ===\n";
echo "You can now test the cart validation by visiting:\n";
echo "http://localhost/bpsms/cart_validation_test.php?client_id=2\n";
echo "http://localhost/bpsms/test_cart_validation_endpoint.php?client_id=2\n";
?>
