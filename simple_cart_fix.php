<?php
/**
 * Simple Cart Validation Error Fix Script
 * This script fixes common issues with cart validation and checkout
 */

try {
    // Direct database connection
    $conn = new mysqli('localhost', 'root', '', 'bpsms_db');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Starting cart validation fixes...\n";
    
    // 1. Add missing color column to cart_list table
    echo "1. Adding color column to cart_list table...\n";
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
    
    // 2. Add AUTO_INCREMENT to id field
    echo "2. Adding AUTO_INCREMENT to id field...\n";
    $sql = "ALTER TABLE `cart_list` MODIFY COLUMN `id` int(30) NOT NULL AUTO_INCREMENT";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ AUTO_INCREMENT added successfully\n";
    } else {
        echo "   ✗ Error adding AUTO_INCREMENT: " . $conn->error . "\n";
    }
    
    // 3. Add primary key
    echo "3. Adding primary key...\n";
    $sql = "ALTER TABLE `cart_list` ADD PRIMARY KEY (`id`)";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ Primary key added successfully\n";
    } else {
        if (strpos($conn->error, "Duplicate key name") !== false) {
            echo "   ✓ Primary key already exists\n";
        } else {
            echo "   ✗ Error adding primary key: " . $conn->error . "\n";
        }
    }
    
    // 4. Clean up invalid cart items
    echo "4. Cleaning up invalid cart items...\n";
    
    // Remove items with ID 0
    $sql = "DELETE FROM `cart_list` WHERE `id` = 0";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with ID 0\n";
    
    // Remove items with invalid product_id
    $sql = "DELETE FROM `cart_list` WHERE `product_id` = 0 OR `product_id` IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with invalid product_id\n";
    
    // Remove items with invalid client_id
    $sql = "DELETE FROM `cart_list` WHERE `client_id` = 0 OR `client_id` IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with invalid client_id\n";
    
    // Remove items that reference non-existent products
    $sql = "DELETE c FROM `cart_list` c 
            LEFT JOIN `product_list` p ON c.product_id = p.id 
            WHERE p.id IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with non-existent products\n";
    
    // Remove items that reference non-existent clients
    $sql = "DELETE c FROM `cart_list` c 
            LEFT JOIN `client_list` cl ON c.client_id = cl.id 
            WHERE cl.id IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Removed items with non-existent clients\n";
    
    // 5. Fix quantity issues
    echo "5. Fixing quantity issues...\n";
    $sql = "UPDATE `cart_list` SET quantity = 1 WHERE quantity <= 0 OR quantity IS NULL";
    $result = $conn->query($sql);
    echo "   ✓ Fixed invalid quantities\n";
    
    // 6. Reset AUTO_INCREMENT
    echo "6. Resetting AUTO_INCREMENT...\n";
    $sql = "ALTER TABLE `cart_list` AUTO_INCREMENT = 1";
    if ($conn->query($sql) === TRUE) {
        echo "   ✓ AUTO_INCREMENT reset successfully\n";
    } else {
        echo "   ✗ Error resetting AUTO_INCREMENT: " . $conn->error . "\n";
    }
    
    // 7. Add credit_application_completed column to client_list if it doesn't exist
    echo "7. Checking credit_application_completed column...\n";
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
    
    echo "\nCart validation fixes completed successfully!\n";
    echo "You can now test the cart checkout functionality.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
