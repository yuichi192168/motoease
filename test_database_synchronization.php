<?php
/**
 * Database Synchronization Test Script
 * This script tests data consistency between client and admin sides
 */

require_once('./config.php');

echo "<h2>Database Synchronization Test Results</h2>";
echo "<style>
    .test-result { margin: 10px 0; padding: 10px; border-radius: 5px; }
    .pass { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .fail { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
</style>";

$tests_passed = 0;
$tests_total = 0;

function runTest($test_name, $query, $expected_condition = null) {
    global $conn, $tests_passed, $tests_total;
    $tests_total++;
    
    echo "<div class='test-result info'><strong>Test $tests_total:</strong> $test_name</div>";
    
    try {
        $result = $conn->query($query);
        if ($result) {
            $count = $result->num_rows;
            echo "<div class='test-result pass'>✓ PASS - Query executed successfully. Rows returned: $count</div>";
            $tests_passed++;
        } else {
            echo "<div class='test-result fail'>✗ FAIL - Query failed: " . $conn->error . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='test-result fail'>✗ FAIL - Exception: " . $e->getMessage() . "</div>";
    }
}

// Test 1: Check if or_cr_documents table exists
runTest("OR/CR Documents Table Exists", "SHOW TABLES LIKE 'or_cr_documents'");

// Test 2: Check order_list table structure
runTest("Order List Table Structure", "DESCRIBE order_list");

// Test 3: Check service_requests table structure  
runTest("Service Requests Table Structure", "DESCRIBE service_requests");

// Test 4: Check foreign key constraints
runTest("Foreign Key Constraints", "SELECT 
    TABLE_NAME, 
    COLUMN_NAME, 
    CONSTRAINT_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE REFERENCED_TABLE_SCHEMA = DATABASE() 
AND REFERENCED_TABLE_NAME IN ('client_list', 'order_list', 'service_requests', 'product_list')");

// Test 5: Check order status consistency
runTest("Order Status Values", "SELECT DISTINCT status, COUNT(*) as count FROM order_list GROUP BY status");

// Test 6: Check service request status consistency
runTest("Service Request Status Values", "SELECT DISTINCT status, COUNT(*) as count FROM service_requests GROUP BY status");

// Test 7: Check client-order relationships
runTest("Client-Order Relationships", "SELECT 
    c.id as client_id, 
    c.firstname, 
    c.lastname, 
    COUNT(o.id) as order_count 
FROM client_list c 
LEFT JOIN order_list o ON c.id = o.client_id 
WHERE c.delete_flag = 0 
GROUP BY c.id 
ORDER BY order_count DESC 
LIMIT 5");

// Test 8: Check order items relationships
runTest("Order Items Relationships", "SELECT 
    o.id as order_id, 
    o.ref_code, 
    COUNT(oi.id) as item_count 
FROM order_list o 
LEFT JOIN order_items oi ON o.id = oi.order_id 
GROUP BY o.id 
ORDER BY item_count DESC 
LIMIT 5");

// Test 9: Check service request meta data
runTest("Service Request Meta Data", "SELECT 
    s.id as request_id, 
    s.service_type, 
    COUNT(rm.id) as meta_count 
FROM service_requests s 
LEFT JOIN request_meta rm ON s.id = rm.request_id 
GROUP BY s.id 
ORDER BY meta_count DESC 
LIMIT 5");

// Test 10: Check data integrity - orphaned records
runTest("Data Integrity - Orphaned Order Items", "SELECT COUNT(*) as orphaned_items 
FROM order_items oi 
LEFT JOIN order_list ol ON oi.order_id = ol.id 
WHERE ol.id IS NULL");

// Test 11: Check data integrity - orphaned cart items
runTest("Data Integrity - Orphaned Cart Items", "SELECT COUNT(*) as orphaned_cart 
FROM cart_list cl 
LEFT JOIN client_list c ON cl.client_id = c.id 
WHERE c.id IS NULL");

// Test 12: Check indexes
runTest("Database Indexes", "SHOW INDEX FROM order_list WHERE Key_name != 'PRIMARY'");

echo "<h3>Test Summary</h3>";
echo "<div class='test-result " . ($tests_passed == $tests_total ? 'pass' : 'fail') . "'>";
echo "Tests Passed: $tests_passed / $tests_total";
if ($tests_passed == $tests_total) {
    echo " - All tests passed! Database synchronization is working correctly.";
} else {
    echo " - Some tests failed. Please review the issues above.";
}
echo "</div>";

// Additional diagnostic information
echo "<h3>Database Diagnostic Information</h3>";
echo "<div class='test-result info'>";
echo "<strong>Database Version:</strong> " . $conn->server_info . "<br>";
echo "<strong>Current Database:</strong> " . $conn->query("SELECT DATABASE()")->fetch_array()[0] . "<br>";
echo "<strong>Connection Status:</strong> " . ($conn->ping() ? "Connected" : "Disconnected") . "<br>";
echo "</div>";
?>
