<?php
session_start();
require_once('./config.php');

// Check if user is logged in
if(!isset($_SESSION['userdata'])) {
    echo '<div class="alert alert-warning">User not logged in</div>';
    exit;
}

$client_id = $_SESSION['userdata']['id'];

// Get cart items
$cart = $conn->query("SELECT c.*, p.name, p.price FROM cart_list c 
                     INNER JOIN product_list p ON c.product_id = p.id 
                     WHERE c.client_id = '{$client_id}' 
                     ORDER BY c.id DESC");

if($cart && $cart->num_rows > 0) {
    echo '<table class="table table-striped">';
    echo '<thead><tr><th>ID</th><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th><th>Actions</th></tr></thead>';
    echo '<tbody>';
    
    while($row = $cart->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['quantity'] . '</td>';
        echo '<td>₱' . number_format($row['price'], 2) . '</td>';
        echo '<td>₱' . number_format($row['quantity'] * $row['price'], 2) . '</td>';
        echo '<td>';
        echo '<button class="btn btn-sm btn-warning" onclick="updateCartId(' . $row['id'] . ')">Update</button> ';
        echo '<button class="btn btn-sm btn-danger" onclick="removeCartId(' . $row['id'] . ')">Remove</button>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<div class="alert alert-info">No items in cart</div>';
}
?>

<script>
function updateCartId(id) {
    $('#cartId').val(id);
}

function removeCartId(id) {
    $('#removeCartId').val(id);
}
</script>
