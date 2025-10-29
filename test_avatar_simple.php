<?php
require_once('config.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h2>Form Submission Debug</h2>";
    echo "<h3>POST Data:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    if(isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        echo "<h3>Avatar Upload Test:</h3>";
        
        $upload_dir = "uploads/";
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        $file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if(move_uploaded_file($_FILES['img']['tmp_name'], $file_path)) {
            echo "✅ Avatar uploaded successfully: $file_path<br>";
            echo "✅ validate_image result: " . validate_image($file_path) . "<br>";
        } else {
            echo "❌ Failed to upload avatar<br>";
        }
    } else {
        echo "❌ No avatar file uploaded or error occurred<br>";
        if(isset($_FILES['img'])) {
            echo "Error code: " . $_FILES['img']['error'] . "<br>";
        }
    }
} else {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Avatar Upload</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Test Avatar Upload</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <p>
            <label>First Name:</label><br>
            <input type="text" name="firstname" value="Test User" required>
        </p>
        <p>
            <label>Last Name:</label><br>
            <input type="text" name="lastname" value="Test" required>
        </p>
        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="test@example.com" required>
        </p>
        <p>
            <label>Gender:</label><br>
            <select name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </p>
        <p>
            <label>Contact:</label><br>
            <input type="text" name="contact" value="09123456789" required>
        </p>
        <p>
            <label>Address:</label><br>
            <textarea name="address" required>Test Address</textarea>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" value="123456" required>
        </p>
        <p>
            <label>Avatar:</label><br>
            <input type="file" name="img" accept="image/*">
        </p>
        <p>
            <button type="submit">Test Upload</button>
        </p>
    </form>
</body>
</html>
<?php
}
?>

