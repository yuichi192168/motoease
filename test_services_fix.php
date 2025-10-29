<?php
require_once('config.php');

echo "<h2>Services Page Fix Verification</h2>";

// Test 1: Database connection
echo "<h3>1. Database Connection</h3>";
if ($conn) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
    exit;
}

// Test 2: Service list query
echo "<h3>2. Service List Query</h3>";
$services_query = "SELECT * FROM service_list WHERE status = 1 AND delete_flag = 0 ORDER BY service";
$result = $conn->query($services_query);

if ($result) {
    echo "✅ Query executed successfully<br>";
    echo "Services found: " . $result->num_rows . "<br>";
    
    if ($result->num_rows > 0) {
        echo "<h4>Available Services:</h4><ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>ID: " . $row['id'] . " - " . htmlspecialchars($row['service']) . "</li>";
        }
        echo "</ul>";
    }
} else {
    echo "❌ Query failed: " . $conn->error . "<br>";
}

// Test 3: Check if service_requests table has required columns
echo "<h3>3. Service Requests Table Structure</h3>";
$columns_query = "SHOW COLUMNS FROM service_requests";
$columns_result = $conn->query($columns_query);

if ($columns_result) {
    $required_columns = ['vehicle_type', 'vehicle_name', 'vehicle_registration_number', 'vehicle_model'];
    $existing_columns = [];
    
    while ($row = $columns_result->fetch_assoc()) {
        $existing_columns[] = $row['Field'];
    }
    
    echo "Required columns: " . implode(', ', $required_columns) . "<br>";
    echo "Existing columns: " . implode(', ', $existing_columns) . "<br>";
    
    $missing_columns = array_diff($required_columns, $existing_columns);
    if (empty($missing_columns)) {
        echo "✅ All required columns exist<br>";
    } else {
        echo "❌ Missing columns: " . implode(', ', $missing_columns) . "<br>";
        echo "Run this SQL to fix: <br>";
        echo "<code>ALTER TABLE service_requests ADD COLUMN vehicle_type varchar(100) DEFAULT NULL AFTER client_id, ADD COLUMN vehicle_model varchar(100) DEFAULT NULL AFTER vehicle_registration_number;</code><br>";
    }
} else {
    echo "❌ Failed to check table structure: " . $conn->error . "<br>";
}

// Test 4: User authentication
echo "<h3>4. User Authentication</h3>";
if (isset($_settings)) {
    $user_id = $_settings->userdata('id');
    $login_type = $_settings->userdata('login_type');
    
    echo "User ID: " . ($user_id ? $user_id : 'Not logged in') . "<br>";
    echo "Login Type: " . ($login_type ? $login_type : 'Not logged in') . "<br>";
    
    if ($user_id > 0 && $login_type == 2) {
        echo "✅ User is logged in as customer - modal should work<br>";
    } else {
        echo "⚠️ User is not logged in as customer - will show login prompt<br>";
    }
} else {
    echo "❌ Settings object not available<br>";
}

// Test 5: Check if files exist
echo "<h3>5. File Existence Check</h3>";
$files_to_check = [
    'send_request.php' => 'Service request form',
    'view_service.php' => 'Service details modal',
    'classes/Master.php' => 'Backend functions',
    'inc/footer.php' => 'JavaScript functions'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description ($file) exists<br>";
    } else {
        echo "❌ $description ($file) missing<br>";
    }
}

echo "<h3>6. Next Steps</h3>";
echo "1. If missing columns were found, run the SQL command above<br>";
echo "2. Test the services page at: <a href='?p=services'>http://localhost/bpsms/?p=services</a><br>";
echo "3. Check browser console for JavaScript errors<br>";
echo "4. Test both 'Send Service Request' button and individual service clicks<br>";

?>


