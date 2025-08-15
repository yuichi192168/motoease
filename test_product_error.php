<?php
require_once('./config.php');

echo "<h2>Product Save Error Diagnosis</h2>";

// Test database connection
echo "<h3>1. Database Connection Test</h3>";
try {
    $test_query = $conn->query("SELECT 1 as test");
    if($test_query && $test_query->fetch_assoc()['test'] == 1) {
        echo "✅ Database connection successful<br>";
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database connection error: " . $e->getMessage() . "<br>";
}

// Test product_list table structure
echo "<h3>2. Product Table Structure Test</h3>";
try {
    $result = $conn->query("DESCRIBE product_list");
    if($result) {
        echo "✅ product_list table structure:<br>";
        while($row = $result->fetch_assoc()) {
            echo "&nbsp;&nbsp;• {$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Default']}<br>";
        }
    } else {
        echo "❌ Could not describe product_list table<br>";
    }
} catch (Exception $e) {
    echo "❌ Error describing table: " . $e->getMessage() . "<br>";
}

// Test required tables
echo "<h3>3. Required Tables Test</h3>";
$required_tables = [
    'brand_list',
    'categories',
    'product_list'
];

foreach($required_tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '{$table}'");
    if($result->num_rows > 0) {
        echo "✅ Table '{$table}' exists<br>";
    } else {
        echo "❌ Table '{$table}' missing<br>";
    }
}

// Test sample data insertion
echo "<h3>4. Sample Data Insertion Test</h3>";
try {
    // Check if we have brands and categories
    $brands = $conn->query("SELECT COUNT(*) as count FROM brand_list WHERE delete_flag = 0")->fetch_assoc()['count'];
    $categories = $conn->query("SELECT COUNT(*) as count FROM categories WHERE delete_flag = 0")->fetch_assoc()['count'];
    
    echo "Brands available: {$brands}<br>";
    echo "Categories available: {$categories}<br>";
    
    if($brands > 0 && $categories > 0) {
        // Get first brand and category
        $brand = $conn->query("SELECT id FROM brand_list WHERE delete_flag = 0 LIMIT 1")->fetch_assoc();
        $category = $conn->query("SELECT id FROM categories WHERE delete_flag = 0 LIMIT 1")->fetch_assoc();
        
        if($brand && $category) {
            $test_sql = "INSERT INTO product_list (brand_id, category_id, name, models, description, price, status, delete_flag, date_created) 
                        VALUES ('{$brand['id']}', '{$category['id']}', 'Test Product', 'Test Model', 'Test Description', 100.00, 1, 0, NOW())";
            
            $result = $conn->query($test_sql);
            if($result) {
                $insert_id = $conn->insert_id;
                echo "✅ Sample product inserted successfully (ID: {$insert_id})<br>";
                
                // Clean up test data
                $conn->query("DELETE FROM product_list WHERE id = '{$insert_id}'");
                echo "✅ Test data cleaned up<br>";
            } else {
                echo "❌ Sample insertion failed: " . $conn->error . "<br>";
            }
        } else {
            echo "❌ No brands or categories available for testing<br>";
        }
    } else {
        echo "❌ Need at least one brand and category to test product insertion<br>";
    }
} catch (Exception $e) {
    echo "❌ Error during sample insertion: " . $e->getMessage() . "<br>";
}

// Test Master class
echo "<h3>5. Master Class Test</h3>";
try {
    require_once('./classes/Master.php');
    $master = new Master();
    echo "✅ Master class loaded successfully<br>";
    
    // Test if save_product method exists
    if(method_exists($master, 'save_product')) {
        echo "✅ save_product method exists<br>";
    } else {
        echo "❌ save_product method missing<br>";
    }
} catch (Exception $e) {
    echo "❌ Error loading Master class: " . $e->getMessage() . "<br>";
}

// Test uploads directory
echo "<h3>6. Uploads Directory Test</h3>";
$uploads_dir = "./uploads/products/";
if(is_dir($uploads_dir)) {
    echo "✅ Uploads directory exists<br>";
    if(is_writable($uploads_dir)) {
        echo "✅ Uploads directory is writable<br>";
    } else {
        echo "❌ Uploads directory is not writable<br>";
    }
} else {
    echo "❌ Uploads directory does not exist<br>";
    if(mkdir($uploads_dir, 0777, true)) {
        echo "✅ Created uploads directory<br>";
    } else {
        echo "❌ Failed to create uploads directory<br>";
    }
}

echo "<h3>7. PHP Configuration Test</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "File Uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "<br>";
echo "Max File Size: " . ini_get('upload_max_filesize') . "<br>";
echo "Post Max Size: " . ini_get('post_max_size') . "<br>";

echo "<h3>8. Error Log Check</h3>";
$error_log = ini_get('error_log');
if($error_log && file_exists($error_log)) {
    echo "Error log location: {$error_log}<br>";
    $recent_errors = file_get_contents($error_log);
    if($recent_errors) {
        echo "Recent errors:<br>";
        echo "<pre>" . htmlspecialchars(substr($recent_errors, -1000)) . "</pre>";
    } else {
        echo "No recent errors found<br>";
    }
} else {
    echo "Error log not configured or not accessible<br>";
}

echo "<hr>";
echo "<h3>Recommendations:</h3>";
echo "1. Check browser console (F12) for JavaScript errors<br>";
echo "2. Verify all form fields are filled correctly<br>";
echo "3. Check server error logs for PHP errors<br>";
echo "4. Ensure database permissions are correct<br>";
echo "5. Verify file upload permissions<br>";
?>
