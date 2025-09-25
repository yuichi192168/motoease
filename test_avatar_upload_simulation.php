<?php
require_once('config.php');

// Simulate a customer avatar upload
echo "<h3>Testing Avatar Upload Simulation</h3>";

// First, let's check if there are any existing customers
$customers = $conn->query("SELECT * FROM client_list LIMIT 1");
if($customers->num_rows > 0){
    $customer = $customers->fetch_assoc();
    echo "<h4>Testing with customer: " . $customer['firstname'] . " " . $customer['lastname'] . "</h4>";
    
    // Simulate the upload process
    $upload_dir = "uploads/";
    $file_name = time() . '_test_avatar.jpg';
    $file_path = $upload_dir . $file_name;
    $full_path = base_app . $file_path;
    
    echo "Upload directory: " . $upload_dir . "<br>";
    echo "File name: " . $file_name . "<br>";
    echo "File path: " . $file_path . "<br>";
    echo "Full path: " . $full_path . "<br>";
    
    // Create a test image file
    $test_image_content = file_get_contents('dist/img/no-image-available.png');
    if($test_image_content && file_put_contents($full_path, $test_image_content)){
        echo "Test image created successfully<br>";
        
        // Update the customer's avatar in database
        $update_sql = "UPDATE client_list SET avatar = '{$file_path}' WHERE id = '{$customer['id']}'";
        if($conn->query($update_sql)){
            echo "Database updated successfully<br>";
            
            // Check if the file exists and can be accessed
            echo "File exists: " . (file_exists($full_path) ? "YES" : "NO") . "<br>";
            echo "File size: " . filesize($full_path) . " bytes<br>";
            echo "Validate image result: " . validate_image($file_path) . "<br>";
            
            // Display the image
            echo "<img src='" . validate_image($file_path) . "' style='width: 100px; height: 100px; object-fit: cover; border-radius: 50%;'><br>";
            
            // Clean up
            unlink($full_path);
            echo "Test file cleaned up<br>";
        } else {
            echo "Database update failed: " . $conn->error . "<br>";
        }
    } else {
        echo "Failed to create test image<br>";
    }
} else {
    echo "No customers found in database<br>";
}
?>
