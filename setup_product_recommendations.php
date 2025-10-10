<?php
require_once('config.php');

echo "<h2>Setting up Product Recommendations and Notifications</h2>";

// Read and execute the SQL file
$sql = file_get_contents('create_product_recommendations.sql');

if($conn->multi_query($sql)){
    echo "<p>✓ Product recommendations and notifications tables created successfully!</p>";
    
    // Check if tables were created
    $result1 = $conn->query("SHOW TABLES LIKE 'product_recommendations'");
    $result2 = $conn->query("SHOW TABLES LIKE 'product_availability_notifications'");
    
    if($result1->num_rows > 0 && $result2->num_rows > 0){
        echo "<p>✓ Both tables exist in database</p>";
        
        // Add some sample recommendations
        echo "<h3>Adding Sample Recommendations</h3>";
        
        // Get some products to create recommendations
        $products = $conn->query("SELECT id, name FROM product_list WHERE delete_flag = 0 AND status = 1 LIMIT 5");
        
        if($products->num_rows > 0){
            $product_ids = [];
            while($row = $products->fetch_assoc()){
                $product_ids[] = $row['id'];
            }
            
            // Create some sample recommendations
            $sample_recommendations = [
                // If product 1 is unavailable, recommend product 2
                ['product_id' => $product_ids[0], 'recommended_product_id' => $product_ids[1], 'type' => 'alternative', 'priority' => 1],
                ['product_id' => $product_ids[0], 'recommended_product_id' => $product_ids[2], 'type' => 'similar', 'priority' => 2],
                ['product_id' => $product_ids[1], 'recommended_product_id' => $product_ids[0], 'type' => 'alternative', 'priority' => 1],
                ['product_id' => $product_ids[1], 'recommended_product_id' => $product_ids[3], 'type' => 'similar', 'priority' => 2],
                ['product_id' => $product_ids[2], 'recommended_product_id' => $product_ids[0], 'type' => 'alternative', 'priority' => 1],
                ['product_id' => $product_ids[2], 'recommended_product_id' => $product_ids[4], 'type' => 'upgrade', 'priority' => 3],
            ];
            
            $inserted = 0;
            foreach($sample_recommendations as $rec){
                if($rec['product_id'] != $rec['recommended_product_id']){ // Don't recommend self
                    $insert = $conn->query("INSERT INTO product_recommendations (product_id, recommended_product_id, recommendation_type, priority) VALUES ('{$rec['product_id']}', '{$rec['recommended_product_id']}', '{$rec['type']}', '{$rec['priority']}')");
                    if($insert){
                        $inserted++;
                    }
                }
            }
            
            echo "<p>✓ Added {$inserted} sample recommendations</p>";
        }
        
        echo "<h3>Setup Complete!</h3>";
        echo "<p>Features now available:</p>";
        echo "<ul>";
        echo "<li>✓ Product recommendations for out-of-stock items</li>";
        echo "<li>✓ Notification system for product availability</li>";
        echo "<li>✓ Alternative product suggestions</li>";
        echo "<li>✓ User notification subscriptions</li>";
        echo "</ul>";
        
    } else {
        echo "<p>✗ Tables were not created properly</p>";
    }
} else {
    echo "<p>✗ Error creating tables: " . $conn->error . "</p>";
}

$conn->close();
?>

