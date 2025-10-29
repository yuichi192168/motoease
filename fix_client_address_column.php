<?php
require_once('./config.php');

echo "<h2>Fixing Client Address Column Issue</h2>";

try {
    // Check if address column exists
    $check_column = $conn->query("SHOW COLUMNS FROM `client_list` LIKE 'address'");
    
    if($check_column->num_rows == 0) {
        echo "<p>Address column not found. Adding it now...</p>";
        
        // Add the address column
        $add_column = $conn->query("ALTER TABLE `client_list` ADD COLUMN `address` TEXT NOT NULL DEFAULT '' AFTER `contact`");
        
        if($add_column) {
            echo "<p style='color: green;'>✅ Address column added successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Error adding address column: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: blue;'>ℹ️ Address column already exists.</p>";
    }
    
    // Update any NULL values
    $update_null = $conn->query("UPDATE `client_list` SET `address` = '' WHERE `address` IS NULL");
    
    if($update_null) {
        echo "<p style='color: green;'>✅ Updated NULL address values.</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Warning updating NULL values: " . $conn->error . "</p>";
    }
    
    // Verify the fix
    $verify = $conn->query("SELECT COUNT(*) as total FROM `client_list`");
    $result = $verify->fetch_assoc();
    
    echo "<p style='color: green;'>✅ Database fix completed! Total clients: " . $result['total'] . "</p>";
    echo "<p><strong>You can now update client details without the address error.</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
p { margin: 10px 0; }
</style>
