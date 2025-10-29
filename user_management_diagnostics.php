<?php
require_once('./config.php');

echo "=== USER MANAGEMENT DIAGNOSTICS ===\n";
echo "Database: " . $conn->query('SELECT DATABASE()')->fetch_array()[0] . "\n";
echo "Connection: " . ($conn->ping() ? 'OK' : 'FAILED') . "\n\n";

echo "=== USERS TABLE STRUCTURE ===\n";
$result = $conn->query('DESCRIBE users');
if($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Key'] . ' - ' . $row['Default'] . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n=== USERS TABLE DATA ===\n";
$result = $conn->query('SELECT id, firstname, lastname, username, type, role_type FROM users ORDER BY id DESC LIMIT 10');
if($result) {
    echo "Total rows: " . $result->num_rows . "\n";
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . ", Name: " . $row['firstname'] . " " . $row['lastname'] . ", Username: " . $row['username'] . ", Type: " . $row['type'] . ", Role Type: " . ($row['role_type'] ?? 'NULL') . "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n";
}

echo "\n=== CHECKING FOR ROLE_TYPE COLUMN ===\n";
$column_check = $conn->query("SHOW COLUMNS FROM users LIKE 'role_type'");
if($column_check->num_rows > 0) {
    echo "✓ role_type column exists\n";
} else {
    echo "✗ role_type column does not exist!\n";
}

echo "\n=== CHECKING FOR EMAIL COLUMN ===\n";
$email_check = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
if($email_check->num_rows > 0) {
    echo "✓ email column exists\n";
} else {
    echo "✗ email column does not exist!\n";
}

echo "\n=== TESTING USER UPDATE QUERY ===\n";
// Test the update query that would be generated
$test_data = [
    'firstname' => 'Test',
    'lastname' => 'User',
    'username' => 'testuser',
    'email' => 'test@example.com',
    'role_type' => 'admin'
];

$data = '';
foreach($test_data as $k => $v){
    if(!in_array($k,array('id','password'))){
        if(!empty($data)) $data .=" , ";
        $data .= " {$k} = '{$v}' ";
    }
}

echo "Generated query: UPDATE users set $data where id = 1\n";

// Test if the query would work
$test_query = "UPDATE users set $data where id = 1";
$result = $conn->query($test_query);
if($result) {
    echo "✓ Test query executed successfully\n";
} else {
    echo "✗ Test query failed: " . $conn->error . "\n";
}

echo "\n=== CHECKING FOR MISSING COLUMNS ===\n";
$required_columns = ['role_type', 'email'];
$missing_columns = [];

foreach($required_columns as $column) {
    $check = $conn->query("SHOW COLUMNS FROM users LIKE '$column'");
    if($check->num_rows == 0) {
        $missing_columns[] = $column;
    }
}

if(empty($missing_columns)) {
    echo "✓ All required columns exist\n";
} else {
    echo "✗ Missing columns: " . implode(', ', $missing_columns) . "\n";
}

echo "\n=== DIAGNOSTICS COMPLETE ===\n";
?>
