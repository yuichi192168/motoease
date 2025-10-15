<!DOCTYPE html>
<html>
<head>
    <title>Test Cart Functions</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Cart Functions</h2>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Add to Cart Test</h3>
                <button class="btn btn-primary" onclick="testAddToCart()">Add Product 1 to Cart</button>
                <div id="addResult" class="mt-2"></div>
            </div>
            
            <div class="col-md-6">
                <h3>Cart Items</h3>
                <button class="btn btn-info" onclick="loadCartItems()">Load Cart Items</button>
                <div id="cartItems" class="mt-2"></div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <h3>Update Quantity Test</h3>
                <input type="number" id="cartId" placeholder="Cart ID" class="form-control mb-2">
                <button class="btn btn-warning" onclick="testUpdateQuantity()">Update Quantity (+1)</button>
                <div id="updateResult" class="mt-2"></div>
            </div>
            
            <div class="col-md-6">
                <h3>Remove from Cart Test</h3>
                <input type="number" id="removeCartId" placeholder="Cart ID" class="form-control mb-2">
                <button class="btn btn-danger" onclick="testRemoveFromCart()">Remove from Cart</button>
                <div id="removeResult" class="mt-2"></div>
            </div>
        </div>
    </div>

    <script>
        function testAddToCart() {
            $.ajax({
                url: 'classes/Master.php?f=save_to_cart',
                method: 'POST',
                data: {
                    product_id: 1,
                    quantity: 1,
                    color: 'Red'
                },
                dataType: 'json',
                success: function(resp) {
                    $('#addResult').html('<div class="alert alert-success">' + JSON.stringify(resp) + '</div>');
                    if(resp.status === 'success') {
                        loadCartItems();
                    }
                },
                error: function(xhr, status, error) {
                    $('#addResult').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                }
            });
        }
        
        function loadCartItems() {
            $.ajax({
                url: 'test_cart_items.php',
                method: 'GET',
                success: function(data) {
                    $('#cartItems').html(data);
                },
                error: function(xhr, status, error) {
                    $('#cartItems').html('<div class="alert alert-danger">Error loading cart items: ' + error + '</div>');
                }
            });
        }
        
        function testUpdateQuantity() {
            var cartId = $('#cartId').val();
            if(!cartId) {
                $('#updateResult').html('<div class="alert alert-warning">Please enter a cart ID</div>');
                return;
            }
            
            $.ajax({
                url: 'classes/Master.php?f=update_cart_quantity',
                method: 'POST',
                data: {
                    cart_id: cartId,
                    quantity: '+1'
                },
                dataType: 'json',
                success: function(resp) {
                    $('#updateResult').html('<div class="alert alert-success">' + JSON.stringify(resp) + '</div>');
                    if(resp.status === 'success') {
                        loadCartItems();
                    }
                },
                error: function(xhr, status, error) {
                    $('#updateResult').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                }
            });
        }
        
        function testRemoveFromCart() {
            var cartId = $('#removeCartId').val();
            if(!cartId) {
                $('#removeResult').html('<div class="alert alert-warning">Please enter a cart ID</div>');
                return;
            }
            
            $.ajax({
                url: 'classes/Master.php?f=remove_from_cart',
                method: 'POST',
                data: {
                    cart_id: cartId
                },
                dataType: 'json',
                success: function(resp) {
                    $('#removeResult').html('<div class="alert alert-success">' + JSON.stringify(resp) + '</div>');
                    if(resp.status === 'success') {
                        loadCartItems();
                    }
                },
                error: function(xhr, status, error) {
                    $('#removeResult').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                }
            });
        }
        
        // Load cart items on page load
        $(document).ready(function() {
            loadCartItems();
        });
    </script>
</body>
</html>
