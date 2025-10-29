<?php
// Simulate the AJAX call to Login.php
$_GET['f'] = 'login_client';
$_POST['email'] = 'jhoanna@gmail.com';
$_POST['password'] = '123456';

// Capture the output
ob_start();
include 'classes/Login.php';
$output = ob_get_clean();

echo "Raw Output from Login.php:\n";
echo "==========================\n";
echo $output;

echo "\n\nParsing JSON Response:\n";
echo "=====================\n";

// Try to extract JSON from the output
$lines = explode("\n", $output);
foreach($lines as $line) {
    $line = trim($line);
    if(strpos($line, '{') === 0) {
        echo "Found JSON: $line\n";
        $decoded = json_decode($line, true);
        if(json_last_error() === JSON_ERROR_NONE) {
            echo "Valid JSON Response:\n";
            print_r($decoded);
            
            if($decoded['status'] == 'success') {
                echo "\nLogin successful! Testing redirect...\n";
                echo "Base URL: " . base_url . "\n";
                
                // Test if the homepage is accessible
                $homepage_url = base_url;
                echo "Homepage URL: $homepage_url\n";
                
                // Check if the URL is valid
                $headers = get_headers($homepage_url);
                if($headers !== false) {
                    echo "Homepage is accessible. Status: " . $headers[0] . "\n";
                } else {
                    echo "Homepage is not accessible.\n";
                }
            }
        } else {
            echo "Invalid JSON: " . json_last_error_msg() . "\n";
        }
        break;
    }
}

echo "\n\nFull Output Analysis:\n";
echo "====================\n";
echo "Output length: " . strlen($output) . "\n";
echo "First 200 characters: " . substr($output, 0, 200) . "\n";
echo "Last 200 characters: " . substr($output, -200) . "\n";
?>
