<?php
require_once('./config.php');

echo "Testing Login and Session Handling\n";
echo "==================================\n\n";

// Check if user is already logged in
if($_settings->userdata('id') > 0) {
    echo "User is already logged in:\n";
    echo "ID: " . $_settings->userdata('id') . "\n";
    echo "Email: " . $_settings->userdata('email') . "\n";
    echo "Login Type: " . $_settings->userdata('login_type') . "\n";
    echo "Session Data: ";
    print_r($_SESSION);
} else {
    echo "No user logged in.\n";
    echo "Session Data: ";
    print_r($_SESSION);
}

echo "\n\nTesting Login Process:\n";
echo "======================\n";

// Simulate login
$_POST['email'] = 'jhoanna@gmail.com';
$_POST['password'] = '123456';

// Include the Login class
require_once('./classes/Login.php');

// Create login instance
$login = new Login();

// Call the login_client method directly
$response = $login->login_client();
$decoded = json_decode($response, true);

echo "Login Response: ";
print_r($decoded);

if($decoded['status'] == 'success') {
    echo "\nLogin successful! Checking session...\n";
    
    // Check session after login
    if($_settings->userdata('id') > 0) {
        echo "Session set successfully:\n";
        echo "ID: " . $_settings->userdata('id') . "\n";
        echo "Email: " . $_settings->userdata('email') . "\n";
        echo "Login Type: " . $_settings->userdata('login_type') . "\n";
        
        echo "\nTesting redirect URL:\n";
        echo "Base URL: " . base_url . "\n";
        echo "Full redirect URL: " . base_url . "\n";
        
        // Test if the redirect URL is accessible
        $redirect_url = base_url;
        echo "Redirect URL: $redirect_url\n";
        
        // Check if we can access the homepage
        $homepage_content = file_get_contents($redirect_url);
        if($homepage_content !== false) {
            echo "Homepage is accessible.\n";
        } else {
            echo "Homepage is not accessible.\n";
        }
        
    } else {
        echo "Session not set properly after login.\n";
    }
} else {
    echo "Login failed: " . $decoded['msg'] . "\n";
}

echo "\n\nCurrent Session Data:\n";
echo "====================\n";
print_r($_SESSION);
?>
