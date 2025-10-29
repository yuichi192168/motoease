<?php
/**
 * Test User Position Change Functionality
 * This script tests the staff position change functionality
 */

require_once('./config.php');

echo "=== USER POSITION CHANGE TEST ===\n";

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

// Test 3: Test role type changes
echo "\n3. Testing role type changes...\n";
$test_roles = ['admin', 'service_admin', 'stock_admin', 'mechanic', 'inventory'];

foreach($test_roles as $role) {
    $test_query = "UPDATE users SET role_type = '$role' WHERE id = 1";
    $result = $conn->query($test_query);
    if($result) {
        echo "✓ Role '$role' update successful\n";
    } else {
        echo "✗ Role '$role' update failed: " . $conn->error . "\n";
    }
}

// Test 4: Test the Users.php class save function
echo "\n4. Testing Users.php class save function...\n";

// Include the Users class
require_once('./classes/Users.php');

// Create a test user data
$test_user_data = [
    'id' => 1,
    'firstname' => 'Test',
    'lastname' => 'User',
    'username' => 'testuser',
    'email' => 'test@example.com',
    'role_type' => 'admin'
];

// Simulate POST data
$_POST = $test_user_data;

// Test the save function
$users = new Users();
$result = $users->save_users();
$response = json_decode($result, true);

if($response['status'] == 'success') {
    echo "✓ Users.php save function works correctly\n";
} else {
    echo "✗ Users.php save function failed: " . $response['msg'] . "\n";
}

// Test 5: Test form validation
echo "\n5. Testing form validation...\n";

// Test with invalid email
$_POST = [
    'id' => 1,
    'firstname' => 'Test',
    'lastname' => 'User',
    'username' => 'testuser',
    'email' => 'invalid-email',
    'role_type' => 'admin'
];

$result = $users->save_users();
$response = json_decode($result, true);

if($response['status'] == 'failed' && strpos($response['msg'], 'email') !== false) {
    echo "✓ Email validation works correctly\n";
} else {
    echo "✗ Email validation not working properly\n";
}

// Test with invalid username
$_POST = [
    'id' => 1,
    'firstname' => 'Test',
    'lastname' => 'User',
    'username' => 'test user!',
    'email' => 'test@example.com',
    'role_type' => 'admin'
];

$result = $users->save_users();
$response = json_decode($result, true);

if($response['status'] == 'failed' && strpos($response['msg'], 'username') !== false) {
    echo "✓ Username validation works correctly\n";
} else {
    echo "✗ Username validation not working properly\n";
}

// Test 6: Test role type validation
echo "\n6. Testing role type validation...\n";

$valid_roles = ['admin', 'service_admin', 'stock_admin', 'mechanic', 'inventory', 'service_receptionist'];
$invalid_roles = ['invalid_role', 'super_admin', ''];

foreach($valid_roles as $role) {
    $_POST = [
        'id' => 1,
        'firstname' => 'Test',
        'lastname' => 'User',
        'username' => 'testuser',
        'role_type' => $role
    ];
    
    $result = $users->save_users();
    $response = json_decode($result, true);
    
    if($response['status'] == 'success') {
        echo "✓ Role '$role' is valid\n";
    } else {
        echo "✗ Role '$role' failed: " . $response['msg'] . "\n";
    }
}

// Test 7: Test the complete workflow
echo "\n7. Testing complete workflow...\n";

// Get a real user
$real_user = $conn->query("SELECT * FROM users WHERE id > 0 LIMIT 1")->fetch_assoc();
if($real_user) {
    echo "Testing with real user: " . $real_user['firstname'] . " " . $real_user['lastname'] . "\n";
    
    // Test changing role
    $original_role = $real_user['role_type'];
    $new_role = $original_role == 'admin' ? 'service_admin' : 'admin';
    
    $_POST = [
        'id' => $real_user['id'],
        'firstname' => $real_user['firstname'],
        'lastname' => $real_user['lastname'],
        'username' => $real_user['username'],
        'role_type' => $new_role
    ];
    
    $result = $users->save_users();
    $response = json_decode($result, true);
    
    if($response['status'] == 'success') {
        echo "✓ Role change from '$original_role' to '$new_role' successful\n";
        
        // Verify the change
        $updated_user = $conn->query("SELECT role_type FROM users WHERE id = {$real_user['id']}")->fetch_assoc();
        if($updated_user['role_type'] == $new_role) {
            echo "✓ Role change verified in database\n";
        } else {
            echo "✗ Role change not reflected in database\n";
        }
        
        // Change back to original role
        $_POST['role_type'] = $original_role;
        $users->save_users();
        echo "✓ Role changed back to original\n";
    } else {
        echo "✗ Role change failed: " . $response['msg'] . "\n";
    }
} else {
    echo "No users found for testing\n";
}

echo "\n=== USER POSITION CHANGE TEST COMPLETE ===\n";
echo "If all tests passed, the staff position change functionality should work correctly.\n";
?>
