<?php
$servername = "sql212.infinityfree.com";
$username = "if0_40141531";
$password = "192168motoease";
$dbname = "if0_40141531_motoease_7";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Database connected successfully!";
?>
