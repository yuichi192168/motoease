<?php
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
                           WHERE c.client_id = '{$client_id}'");

if($cart_items->num_rows == 0){
    echo "<div class=\"test-result fail\">❌ Cart is empty for client $client_id</div>";
    exit;
}

echo "<div class=\"test-result pass\">✅ Found " . $cart_items->num_rows . " items in cart</div>";

$motorcycle_count = 0;
$has_parts_only = true;
$motorcycle_items = array();

while($item = $cart_items->fetch_assoc()){
    echo "<div class=\"test-result info\">- Item: " . $item['name'] . " (Category: " . $item['category'] . ")</div>";
    if(strtolower($item['category']) == 'motorcycles'){
        $motorcycle_count++;
        $motorcycle_items[] = $item;
        $has_parts_only = false;
    }
}

echo "<div class=\"test-result info\">Motorcycle count: $motorcycle_count</div>";
echo "<div class=\"test-result info\">Has parts only: " . ($has_parts_only ? 'Yes' : 'No') . "</div>";

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
    
    $credit_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();
    $application_completed = $credit_status && $credit_status['credit_application_completed'] == 1;
    
    echo "<div class=\"test-result info\">Credit application completed: " . ($application_completed ? 'Yes' : 'No') . "</div>";
    
    if($application_completed) {
        echo "<div class=\"test-result pass\">✅ RESULT: SUCCESS - Cart validation passed. Ready for checkout.</div>";
    } else {
        echo "<div class=\"test-result pass\">✅ RESULT: SUCCESS - Credit application required for motorcycle purchase.</div>";
    }
}
?>