<?php
/**
 * Cart Issues Cleanup Script
 * This script removes problematic cart items that cause validation errors
 */

try {
    // Direct database connection
    $conn = new mysqli('localhost', 'root', '', 'bpsms_db');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Starting cart cleanup...\n";
    
    // 1. Remove cart items with invalid IDs
    echo "1. Removing cart items with invalid IDs...\n";
    $sql = "DELETE FROM `cart_list` WHERE `id` = 0 OR `id` IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with invalid IDs\n";
    
    // 2. Remove cart items with invalid product_id
    echo "2. Removing cart items with invalid product_id...\n";
    $sql = "DELETE FROM `cart_list` WHERE `product_id` = 0 OR `product_id` IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with invalid product_id\n";
    
    // 3. Remove cart items with invalid client_id
    echo "3. Removing cart items with invalid client_id...\n";
    $sql = "DELETE FROM `cart_list` WHERE `client_id` = 0 OR `client_id` IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with invalid client_id\n";
    
    // 4. Remove cart items that reference non-existent products
    echo "4. Removing cart items with non-existent products...\n";
    $sql = "DELETE c FROM `cart_list` c 
            LEFT JOIN `product_list` p ON c.product_id = p.id 
            WHERE p.id IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with non-existent products\n";
    
    // 5. Remove cart items that reference non-existent clients
    echo "5. Removing cart items with non-existent clients...\n";
    $sql = "DELETE c FROM `cart_list` c 
            LEFT JOIN `client_list` cl ON c.client_id = cl.id 
            WHERE cl.id IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with non-existent clients\n";
    
    // 6. Remove cart items with deleted or inactive products
    echo "6. Removing cart items with deleted/inactive products...\n";
    $sql = "DELETE c FROM `cart_list` c 
            INNER JOIN `product_list` p ON c.product_id = p.id 
            WHERE p.delete_flag = 1 OR p.status = 0";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with deleted/inactive products\n";
    
    // 7. Fix quantity issues
    echo "7. Fixing quantity issues...\n";
    $sql = "UPDATE `cart_list` SET quantity = 1 WHERE quantity <= 0 OR quantity IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Fixed invalid quantities\n";
    
    // 8. Add missing color column if it doesn't exist
    echo "8. Adding color column if missing...\n";
    $sql = "ALTER TABLE `cart_list` ADD COLUMN `color` VARCHAR(50) NULL AFTER `product_id`";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ Color column added successfully\n";
    } else {
        if (strpos($conn->error, "Duplicate column name") !== false) {
            echo "   ✓ Color column already exists\n";
        } else {
            echo "   ✗ Error adding color column: " . $conn->error . "\n";
        }
    }
    
    // 9. Add credit_application_completed column to client_list if it doesn't exist
    echo "9. Adding credit_application_completed column if missing...\n";
    $sql = "ALTER TABLE `client_list` ADD COLUMN `credit_application_completed` TINYINT(1) NOT NULL DEFAULT 0";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ credit_application_completed column added successfully\n";
    } else {
        if (strpos($conn->error, "Duplicate column name") !== false) {
            echo "   ✓ credit_application_completed column already exists\n";
        } else {
            echo "   ✗ Error adding credit_application_completed column: " . $conn->error . "\n";
        }
    }
    
    // 10. Show current cart status
    echo "10. Current cart status...\n";
    $sql = "SELECT COUNT(*) as total_items FROM `cart_list`";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "   ✓ Total valid cart items: " . $row['total_items'] . "\n";
    
    echo "\nCart cleanup completed successfully!\n";
    echo "The cart validation should now work without errors.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
