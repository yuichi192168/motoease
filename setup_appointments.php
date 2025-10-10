<?php
require_once('config.php');

// Read and execute the SQL file
$sql = file_get_contents('create_appointments_table.sql');

if($conn->multi_query($sql)){
    echo "Appointments table created successfully!<br>";
    
    // Check if table was created
    $result = $conn->query("SHOW TABLES LIKE 'appointments'");
    if($result->num_rows > 0){
        echo "✓ Appointments table exists in database<br>";
    } else {
        echo "✗ Appointments table was not created<br>";
    }
} else {
    echo "Error creating appointments table: " . $conn->error . "<br>";
}

$conn->close();
?>

