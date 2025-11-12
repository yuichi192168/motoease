<?php
// ✅ Use your Clever Cloud credentials directly for testing (no getenv yet)
$server = "b4ejmxgl0c5c32ejan5s-mysql.services.clever-cloud.com";
$username = "uczldj9vclddzqrx";
$password = "v4Wt6XD3Molc91cs4szI";
$database = "b4ejmxgl0c5c32ejan5s";
$port = 3306;

// Try connecting
$conn = new mysqli($server, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Successfully connected to Clever Cloud MySQL database!<br>";

// Optional: show tables to verify data
$result = $conn->query("SHOW TABLES;");
if ($result) {
    echo "📋 Tables in database:<br>";
    while ($row = $result->fetch_row()) {
        echo "- " . htmlspecialchars($row[0]) . "<br>";
    }
} else {
    echo "⚠️ Query failed: " . $conn->error;
}

$conn->close();
?>
