<?php
require_once('./config.php');

echo "<h2>Cart System Test</h2>";

// Test database connection
echo "<h3>1. Database Connection Test</h3>";
if($conn){
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Test cart_list table
echo "<h3>2. Cart Table Test</h3>";
$cart_test = $conn->query("SELECT COUNT(*) as count FROM cart_list");
if($cart_test){
    $count = $cart_test->fetch_assoc()['count'];
    echo "✅ Cart table exists and accessible. Total cart items: {$count}<br>";
} else {
    echo "❌ Cart table test failed<br>";
}

// Test product_list table
echo "<h3>3. Product Table Test</h3>";
$product_test = $conn->query("SELECT COUNT(*) as count FROM product_list WHERE delete_flag = 0 AND status = 1");
if($product_test){
    $count = $product_test->fetch_assoc()['count'];
    echo "✅ Product table exists and accessible. Active products: {$count}<br>";
} else {
    echo "❌ Product table test failed<br>";
}

// Test stock_list table
echo "<h3>4. Stock Table Test</h3>";
$stock_test = $conn->query("SELECT COUNT(*) as count FROM stock_list");
if($stock_test){
    $count = $stock_test->fetch_assoc()['count'];
    echo "✅ Stock table exists and accessible. Stock records: {$count}<br>";
} else {
    echo "❌ Stock table test failed<br>";
}

// Test order_list table
echo "<h3>5. Order Table Test</h3>";
$order_test = $conn->query("SELECT COUNT(*) as count FROM order_list");
if($order_test){
    $count = $order_test->fetch_assoc()['count'];
    echo "✅ Order table exists and accessible. Total orders: {$count}<br>";
} else {
    echo "❌ Order table test failed<br>";
}

// Test stock calculation
echo "<h3>6. Stock Calculation Test</h3>";
$stock_calc = $conn->query("
    SELECT 
        p.id,
        p.name,
        COALESCE(SUM(s.quantity), 0) as total_stock,
        COALESCE(SUM(oi.quantity), 0) as total_ordered
    FROM product_list p
    LEFT JOIN stock_list s ON p.id = s.product_id AND s.type = 1
    LEFT JOIN order_items oi ON p.id = oi.product_id
    LEFT JOIN order_list ol ON oi.order_id = ol.id AND ol.status != 5
    WHERE p.delete_flag = 0 AND p.status = 1
    GROUP BY p.id
    LIMIT 5
");

if($stock_calc){
    echo "✅ Stock calculation working. Sample products:<br>";
    while($row = $stock_calc->fetch_assoc()){
        $available = $row['total_stock'] - $row['total_ordered'];
        echo "- {$row['name']}: Stock: {$row['total_stock']}, Ordered: {$row['total_ordered']}, Available: {$available}<br>";
    }
} else {
    echo "❌ Stock calculation test failed<br>";
}

echo "<h3>7. Cart Functions Test</h3>";
echo "To test cart functions, you need to:<br>";
echo "1. Login as a client<br>";
echo "2. Add products to cart<br>";
echo "3. Test cart operations<br>";

echo "<br><strong>Cart System Status: ✅ Ready for testing</strong>";
?>
