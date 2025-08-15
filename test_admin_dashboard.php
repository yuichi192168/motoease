<?php
require_once('./config.php');

echo "Testing Admin Dashboard Database Connections\n";
echo "===========================================\n";

// Test customer_transactions table
echo "\nTesting customer_transactions table:\n";
echo "------------------------------------\n";

try {
    $test_query = $conn->query("SELECT COUNT(*) as total FROM customer_transactions");
    $result = $test_query->fetch_assoc();
    echo "✓ customer_transactions table accessible - Total records: {$result['total']}\n";
} catch (Exception $e) {
    echo "✗ Error accessing customer_transactions: " . $e->getMessage() . "\n";
}

// Test or_cr_documents table
echo "\nTesting or_cr_documents table:\n";
echo "------------------------------\n";

try {
    $test_query = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents");
    $result = $test_query->fetch_assoc();
    echo "✓ or_cr_documents table accessible - Total records: {$result['total']}\n";
} catch (Exception $e) {
    echo "✗ Error accessing or_cr_documents: " . $e->getMessage() . "\n";
}

// Test customer accounts query
echo "\nTesting customer accounts query:\n";
echo "--------------------------------\n";

try {
    $test_query = $conn->query("SELECT c.*, 
                                (SELECT MAX(date_created) FROM customer_transactions WHERE client_id = c.id) as last_transaction
                                FROM `client_list` c 
                                WHERE c.delete_flag = 0 
                                ORDER BY c.account_balance DESC, c.lastname ASC 
                                LIMIT 1");
    if($test_query->num_rows > 0) {
        $result = $test_query->fetch_assoc();
        echo "✓ Customer accounts query working - Found customer: {$result['firstname']} {$result['lastname']}\n";
    } else {
        echo "✓ Customer accounts query working - No customers found (this is normal if no customers exist)\n";
    }
} catch (Exception $e) {
    echo "✗ Error in customer accounts query: " . $e->getMessage() . "\n";
}

// Test OR/CR documents query
echo "\nTesting OR/CR documents query:\n";
echo "------------------------------\n";

try {
    $test_query = $conn->query("SELECT d.*, 
                                CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) as customer_name
                                FROM `or_cr_documents` d 
                                INNER JOIN client_list c ON d.client_id = c.id 
                                ORDER BY d.date_created DESC 
                                LIMIT 1");
    if($test_query->num_rows > 0) {
        $result = $test_query->fetch_assoc();
        echo "✓ OR/CR documents query working - Found document: {$result['document_type']} for {$result['customer_name']}\n";
    } else {
        echo "✓ OR/CR documents query working - No documents found (this is normal if no documents exist)\n";
    }
} catch (Exception $e) {
    echo "✗ Error in OR/CR documents query: " . $e->getMessage() . "\n";
}

echo "\nTest completed!\n";
echo "If all tests passed with ✓, the admin dashboard should work correctly.\n";
?>
