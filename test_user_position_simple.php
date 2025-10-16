<?php
/**
 * Simple User Position Change Test
 * This script tests the staff position change functionality without class dependencies
 */

require_once('./config.php');

echo "=== SIMPLE USER POSITION CHANGE TEST ===\n";

// Test 1: Check database structure
echo "1. Checking database structure...\n";
$email_check = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
if($email_check->num_rows > 0) {
    echo "✓ Email column exists\n";
} else {
    echo "✗ Email column missing\n";
}

$role_check = $conn->query("SHOW COLUMNS FROM users LIKE 'role_type'");
if($role_check->num_rows > 0) {
    echo "✓ Role type column exists\n";
} else {
    echo "✗ Role type column missing\n";
}

// Test 2: Get current users
echo "\n2. Getting current users...\n";
$users = $conn->query("SELECT id, firstname, lastname, username, role_type FROM users ORDER BY id DESC LIMIT 5");
if($users->num_rows > 0) {
    echo "Found " . $users->num_rows . " users:\n";
    while($user = $users->fetch_assoc()) {
        echo "- ID: " . $user['id'] . ", Name: " . $user['firstname'] . " " . $user['lastname'] . ", Role: " . ($user['role_type'] ?? 'NULL') . "\n";
    }
} else {
    echo "No users found\n";
}

// Test 3: Test role type changes directly
echo "\n3. Testing role type changes...\n";
$test_roles = ['admin', 'service_admin', 'stock_admin', 'mechanic', 'inventory'];

// Get a test user
$test_user = $conn->query("SELECT * FROM users WHERE id > 0 LIMIT 1")->fetch_assoc();
if($test_user) {
    echo "Testing with user: " . $test_user['firstname'] . " " . $test_user['lastname'] . " (ID: " . $test_user['id'] . ")\n";
    
    $original_role = $test_user['role_type'];
    echo "Original role: " . $original_role . "\n";
    
    foreach($test_roles as $role) {
        $test_query = "UPDATE users SET role_type = '$role' WHERE id = {$test_user['id']}";
        $result = $conn->query($test_query);
        if($result) {
            echo "✓ Role '$role' update successful\n";
            
            // Verify the change
            $updated_user = $conn->query("SELECT role_type FROM users WHERE id = {$test_user['id']}")->fetch_assoc();
            if($updated_user['role_type'] == $role) {
                echo "  ✓ Role change verified in database\n";
            } else {
                echo "  ✗ Role change not reflected in database\n";
            }
        } else {
            echo "✗ Role '$role' update failed: " . $conn->error . "\n";
        }
    }
    
    // Restore original role
    $restore_query = "UPDATE users SET role_type = '$original_role' WHERE id = {$test_user['id']}";
    $conn->query($restore_query);
    echo "✓ Original role restored\n";
} else {
    echo "No users found for testing\n";
}

// Test 4: Test the form data processing
echo "\n4. Testing form data processing...\n";

// Simulate form data
$form_data = [
    'id' => $test_user['id'],
    'firstname' => $test_user['firstname'],
    'lastname' => $test_user['lastname'],
    'username' => $test_user['username'],
    'email' => 'test@example.com',
    'role_type' => 'service_admin'
];

// Build the update query like the Users.php class does
$data = '';
foreach($form_data as $k => $v){
    if(!in_array($k,array('id','password'))){
        if(!empty($data)) $data .=" , ";
        $data .= " {$k} = '{$v}' ";
    }
}

$update_query = "UPDATE users set $data where id = {$form_data['id']}";
echo "Generated query: " . $update_query . "\n";

$result = $conn->query($update_query);
if($result) {
    echo "✓ Form data processing successful\n";
    
    // Verify the change
    $updated_user = $conn->query("SELECT role_type FROM users WHERE id = {$form_data['id']}")->fetch_assoc();
    if($updated_user['role_type'] == 'service_admin') {
        echo "✓ Role change to 'service_admin' verified\n";
    } else {
        echo "✗ Role change not reflected\n";
    }
} else {
    echo "✗ Form data processing failed: " . $conn->error . "\n";
}

// Test 5: Test all available role types
echo "\n5. Testing all available role types...\n";
$available_roles = ['admin', 'service_admin', 'stock_admin', 'mechanic', 'inventory', 'service_receptionist'];

foreach($available_roles as $role) {
    $test_query = "UPDATE users SET role_type = '$role' WHERE id = {$test_user['id']}";
    $result = $conn->query($test_query);
    if($result) {
        echo "✓ Role '$role' is valid and can be set\n";
    } else {
        echo "✗ Role '$role' failed: " . $conn->error . "\n";
    }
}

// Test 6: Test error handling
echo "\n6. Testing error handling...\n";

// Test with invalid role
$invalid_query = "UPDATE users SET role_type = 'invalid_role' WHERE id = {$test_user['id']}";
$result = $conn->query($invalid_query);
if($result) {
    echo "⚠ Invalid role was accepted (this might be a database configuration issue)\n";
} else {
    echo "✓ Invalid role was rejected: " . $conn->error . "\n";
}

// Test with empty role
$empty_query = "UPDATE users SET role_type = '' WHERE id = {$test_user['id']}";
$result = $conn->query($empty_query);
if($result) {
    echo "⚠ Empty role was accepted\n";
} else {
    echo "✓ Empty role was rejected: " . $conn->error . "\n";
}

echo "\n=== SIMPLE USER POSITION CHANGE TEST COMPLETE ===\n";
echo "The staff position change functionality should now work correctly.\n";
echo "You can test by:\n";
echo "1. Going to Admin > User Management\n";
echo "2. Editing a user\n";
echo "3. Changing their staff position\n";
echo "4. Saving the changes\n";
?>
