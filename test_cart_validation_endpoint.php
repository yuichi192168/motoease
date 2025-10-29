<?php
require_once("./config.php");
header("Content-Type: application/json");

$client_id = isset($_GET["client_id"]) ? intval($_GET["client_id"]) : 0;
if($client_id <= 0) {
    echo json_encode(["status" => "failed", "msg" => "Invalid client ID"]);
    exit;
}

// Get cart items with product categories
$cart_items = $conn->query("SELECT c.*, p.name, p.price, cat.category 
                           FROM cart_list c 
                           INNER JOIN product_list p ON c.product_id = p.id 
                           INNER JOIN categories cat ON p.category_id = cat.id 
                           WHERE c.client_id = '{$client_id}'");

if($cart_items->num_rows == 0){
    echo json_encode(["status" => "failed", "msg" => "Your cart is empty."]);
    exit;
}

$motorcycle_count = 0;
$has_parts_only = true;
$motorcycle_items = array();

while($item = $cart_items->fetch_assoc()){
    if(strtolower($item["category"]) == "motorcycles"){
        $motorcycle_count++;
        $motorcycle_items[] = $item;
        $has_parts_only = false;
    }
}

// Check if more than one motorcycle in cart
if($motorcycle_count > 1){
    echo json_encode([
        "status" => "failed",
        "msg" => "You can only checkout one motorcycle at a time. Please remove other motorcycles from your cart.",
        "motorcycle_items" => $motorcycle_items
    ]);
    exit;
}

// If cart contains only parts, no credit application needed
if($has_parts_only){
    echo json_encode([
        "status" => "success",
        "requires_credit_application" => false,
        "msg" => "Cart validation passed. Parts-only order can proceed directly."
    ]);
} else {
    // Cart contains motorcycle, check if credit application is completed
    $credit_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();
    $application_completed = $credit_status && $credit_status["credit_application_completed"] == 1;
    
    echo json_encode([
        "status" => "success",
        "requires_credit_application" => !$application_completed,
        "application_completed" => $application_completed,
        "msg" => $application_completed ? "Cart validation passed. Ready for checkout." : "Credit application required for motorcycle purchase."
    ]);
}
?>