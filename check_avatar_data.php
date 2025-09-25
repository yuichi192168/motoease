<?php
require_once('config.php');

// Check database for customers with avatars
$customers = $conn->query("SELECT id, firstname, lastname, email, avatar FROM client_list WHERE avatar IS NOT NULL AND avatar != ''");

echo "<h3>Customers with Avatars in Database:</h3>";
if($customers->num_rows > 0){
    while($row = $customers->fetch_assoc()){
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<strong>ID:</strong> " . $row['id'] . "<br>";
        echo "<strong>Name:</strong> " . $row['firstname'] . " " . $row['lastname'] . "<br>";
        echo "<strong>Email:</strong> " . $row['email'] . "<br>";
        echo "<strong>Avatar Path:</strong> " . $row['avatar'] . "<br>";
        echo "<strong>File Exists:</strong> " . (file_exists(base_app . $row['avatar']) ? "YES" : "NO") . "<br>";
        echo "<strong>Validate Image:</strong> " . validate_image($row['avatar']) . "<br>";
        echo "<img src='" . validate_image($row['avatar']) . "' style='width: 50px; height: 50px; object-fit: cover; border-radius: 50%;'><br>";
        echo "</div>";
    }
} else {
    echo "No customers with avatars found in database.";
}

// Check uploads directory
echo "<h3>Files in uploads directory:</h3>";
$upload_dir = "uploads/";
if(is_dir($upload_dir)){
    $files = scandir($upload_dir);
    foreach($files as $file){
        if($file != '.' && $file != '..'){
            echo $file . "<br>";
        }
    }
} else {
    echo "Uploads directory does not exist.";
}
?>
