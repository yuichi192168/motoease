<?php
require_once('config.php');

// Check if we have a logged-in customer
if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2){
    $client_id = $_settings->userdata('id');
    
    // Get customer data from database
    $customer = $conn->query("SELECT * FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();
    
    echo "<h3>Debug Avatar Issue</h3>";
    echo "<h4>Session Data:</h4>";
    echo "<pre>";
    print_r($_settings->userdata());
    echo "</pre>";
    
    echo "<h4>Database Data:</h4>";
    echo "<pre>";
    print_r($customer);
    echo "</pre>";
    
    echo "<h4>Avatar Paths:</h4>";
    echo "Session Avatar: " . $_settings->userdata('avatar') . "<br>";
    echo "Database Avatar: " . $customer['avatar'] . "<br>";
    
    echo "<h4>File Existence:</h4>";
    $session_avatar = $_settings->userdata('avatar');
    $db_avatar = $customer['avatar'];
    
    if($session_avatar){
        echo "Session Avatar File Exists: " . (file_exists(base_app . $session_avatar) ? "YES" : "NO") . "<br>";
        echo "Session Avatar Path: " . base_app . $session_avatar . "<br>";
    }
    
    if($db_avatar){
        echo "Database Avatar File Exists: " . (file_exists(base_app . $db_avatar) ? "YES" : "NO") . "<br>";
        echo "Database Avatar Path: " . base_app . $db_avatar . "<br>";
    }
    
    echo "<h4>Validate Image Results:</h4>";
    echo "Session validate_image: " . validate_image($_settings->userdata('avatar')) . "<br>";
    echo "Database validate_image: " . validate_image($customer['avatar']) . "<br>";
    
} else {
    echo "No customer logged in. Please log in as a customer first.";
}
?>
