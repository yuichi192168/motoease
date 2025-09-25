<?php
require_once('config.php');

echo "base_app: " . base_app . "<br>";
echo "base_url: " . base_url . "<br>";
echo "__DIR__: " . __DIR__ . "<br>";
echo "Current working directory: " . getcwd() . "<br>";

$test_file = "uploads/test_file.txt";
$full_path = base_app . $test_file;
echo "Test file path: " . $full_path . "<br>";
echo "Directory exists: " . (is_dir(dirname($full_path)) ? "YES" : "NO") . "<br>";

// Test creating a file
if(!is_dir(base_app . "uploads/")){
    mkdir(base_app . "uploads/", 0777, true);
    echo "Created uploads directory<br>";
}

$test_content = "This is a test file";
if(file_put_contents($full_path, $test_content)){
    echo "Test file created successfully<br>";
    echo "File exists: " . (file_exists($full_path) ? "YES" : "NO") . "<br>";
    echo "File size: " . filesize($full_path) . " bytes<br>";
    unlink($full_path); // Clean up
    echo "Test file deleted<br>";
} else {
    echo "Failed to create test file<br>";
}
?>
