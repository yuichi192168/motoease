<?php
require_once('config.php');

$result = $conn->query("DESCRIBE client_list");
if ($result) {
    echo "client_list columns:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
