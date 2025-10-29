<?php
require_once('./config.php');

echo "=== CART VALIDATION TEST ===\n";

// Test 1: Check cart validation function
echo "\n1. Testing cart validation function...\n";
$client_id = 2; // Test with client ID 2

// Simulate cart validation
$cart_items = $conn->query("SELECT c.*, p.name, p.price, cat.category 
                           FROM cart_list c 
                           INNER JOIN product_list p ON c.product_id = p.id 
                           INNER JOIN categories cat ON p.category_id = cat.id 
                           WHERE c.client_id = '{$client_id}'");

if($cart_items->num_rows == 0){
    echo "Cart is empty for client $client_id\n";
} else {
    echo "Found " . $cart_items->num_rows . " items in cart for client $client_id\n";
    
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
    
    echo "Motorcycle count: $motorcycle_count\n";
    echo "Has parts only: " . ($has_parts_only ? 'Yes' : 'No') . "\n";
    
    if($motorcycle_count > 1){
        echo "ERROR: More than one motorcycle in cart!\n";
    } else {
        echo "Cart validation passed.\n";
    }
}

// Test 2: Check for cart items with invalid data
echo "\n2. Checking for cart items with invalid data...\n";
$invalid_items = $conn->query("SELECT c.*, p.name as product_name, cl.firstname, cl.lastname 
                              FROM cart_list c 
                              LEFT JOIN product_list p ON c.product_id = p.id 
                              LEFT JOIN client_list cl ON c.client_id = cl.id 
                              WHERE c.id = 0 OR c.product_id = 0 OR c.client_id = 0 
                              OR p.id IS NULL OR cl.id IS NULL 
                              OR c.quantity <= 0 OR c.quantity IS NULL");

if($invalid_items->num_rows > 0) {
    echo "Found " . $invalid_items->num_rows . " invalid cart items:\n";
    while($row = $invalid_items->fetch_assoc()) {
        echo "- ID: " . $row['id'] . ", Client: " . $row['firstname'] . " " . $row['lastname'] . ", Product: " . $row['product_name'] . ", Qty: " . $row['quantity'] . "\n";
    }
} else {
    echo "No invalid cart items found.\n";
}

// Test 3: Check stock availability for cart items
echo "\n3. Checking stock availability for cart items...\n";
$stock_check = $conn->query("SELECT c.*, p.name, p.price, 
                            COALESCE(SUM(s.quantity), 0) as total_stock,
                            COALESCE(SUM(oi.quantity), 0) as total_out
                            FROM cart_list c 
                            INNER JOIN product_list p ON c.product_id = p.id 
                            LEFT JOIN stock_list s ON p.id = s.product_id AND s.type = 1
                            LEFT JOIN order_items oi ON p.id = oi.product_id 
                            LEFT JOIN order_list ol ON oi.order_id = ol.id AND ol.status != 5
                            WHERE c.client_id = '{$client_id}'
                            GROUP BY c.id, p.id");

if($stock_check->num_rows > 0) {
    while($row = $stock_check->fetch_assoc()) {
        $available = $row['total_stock'] - $row['total_out'];
        echo "- " . $row['name'] . ": Stock=" . $row['total_stock'] . ", Out=" . $row['total_out'] . ", Available=" . $available . ", Cart Qty=" . $row['quantity'] . "\n";
        
        if($available < $row['quantity']) {
            echo "  WARNING: Not enough stock! Available: $available, Requested: " . $row['quantity'] . "\n";
        }
    }
} else {
    echo "No cart items found for stock check.\n";
}

// Test 4: Check for database errors
echo "\n4. Checking for recent database errors...\n";
$error_log = $conn->query("SHOW WARNINGS");
if($error_log && $error_log->num_rows > 0) {
    echo "Database warnings found:\n";
    while($row = $error_log->fetch_assoc()) {
        echo "- " . $row['Level'] . ": " . $row['Message'] . "\n";
    }
} else {
    echo "No database warnings found.\n";
}

// Test 5: Test cart cleanup function
echo "\n5. Testing cart cleanup function...\n";
try {
    // Start transaction
    $conn->query("START TRANSACTION");
    
    // Remove cart items with ID 0 or invalid product_id
    $result1 = $conn->query("DELETE FROM cart_list WHERE id = 0 OR product_id = 0 OR product_id IS NULL");
    echo "Removed invalid ID/product items: " . $conn->affected_rows . "\n";
    
    // Remove cart items that reference non-existent products
    $result2 = $conn->query("DELETE c FROM cart_list c 
                            LEFT JOIN product_list p ON c.product_id = p.id 
                            WHERE p.id IS NULL");
    echo "Removed orphaned product items: " . $conn->affected_rows . "\n";
    
    // Remove cart items with invalid client_id
    $result3 = $conn->query("DELETE c FROM cart_list c 
                            LEFT JOIN client_list cl ON c.client_id = cl.id 
                            WHERE cl.id IS NULL");
    echo "Removed orphaned client items: " . $conn->affected_rows . "\n";
    
    // Update any cart items with quantity 0 or negative
    $result4 = $conn->query("UPDATE cart_list SET quantity = 1 WHERE quantity <= 0");
    echo "Updated invalid quantities: " . $conn->affected_rows . "\n";
    
    // Set proper default values for any NULL quantities
    $result5 = $conn->query("UPDATE cart_list SET quantity = 1 WHERE quantity IS NULL");
    echo "Set NULL quantities to 1: " . $conn->affected_rows . "\n";
    
    // Commit transaction
    $conn->query("COMMIT");
    echo "Cart cleanup completed successfully.\n";
    
} catch (Exception $e) {
    $conn->query("ROLLBACK");
    echo "Cart cleanup failed: " . $e->getMessage() . "\n";
}

echo "\n=== CART VALIDATION TEST COMPLETE ===\n";
?>
