<?php 
require_once('./inc/sess_auth.php');
?>
<style>
    .prod-cart-img{
        width: 100%;
        height: 200px;
        object-fit: cover;
        object-position: center center;
        border-radius: 8px;
    }
    
    .cart-item {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .cart-item:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    
    .cart-item .card-body {
        padding: 20px;
    }
    
    .product-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        background: #f8f9fa;
    }
    
    .product-image-container img {
        transition: transform 0.3s ease;
    }
    
    .product-image-container:hover img {
        transform: scale(1.05);
    }
    
    .stock-warning {
        color: #dc3545;
        font-size: 0.8em;
    }
    .stock-info {
        color: #28a745;
        font-size: 0.8em;
    }
    
    /* Red and Black Theme for Cart */
    .btn-primary {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    }
    
    .btn-primary:disabled {
        background: #6c757d;
        border-color: #6c757d;
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }
    
    .btn-primary:disabled:hover {
        background: #6c757d;
        border-color: #6c757d;
        transform: none;
        box-shadow: none;
    }
    
    .addon-checkbox:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    /* Mobile Responsive Cart */
    @media (max-width: 768px) {
        .cart-item .card-body {
            padding: 15px;
        }
        
        .prod-cart-img {
            height: 150px;
        }
        
        .cart-item .row {
            flex-direction: column;
        }
        
        .cart-item .col-auto {
            margin-bottom: 15px;
        }
        
        .product-image-container {
            margin-bottom: 15px;
        }
        
        .add-ons-section .row {
            flex-direction: column;
        }
        
        .add-ons-section .col-6 {
            width: 100%;
            margin-bottom: 10px;
        }
    }
    
    @media (max-width: 576px) {
        .prod-cart-img {
            height: 120px;
        }
        
        .cart-item .card-body {
            padding: 12px;
        }
        
        .add-ons-section .card-body {
            padding: 10px;
        }
    }
</style>
<div class="content py-5 mt-3">
    <div class="container">
        <h3><b>Advance Order Cart</b></h3>
        <hr>
        <div class="card card-outline card-primary shadow rounded-0">
            <div class="w-100 p-3">
                <?php 
                $cust = $conn->query("SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) AS fullname, contact FROM client_list WHERE id = '{$_settings->userdata('id')}'");
                $cinfo = $cust && $cust->num_rows ? $cust->fetch_assoc() : ['fullname'=>'','contact'=>''];
                ?>
                <div class="border rounded p-3 mb-2 bg-light">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div class="mb-2">
                            <small class="text-muted">Name</small><br>
                            <strong><?= htmlspecialchars($cinfo['fullname']) ?></strong>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Contact</small><br>
                            <strong><?= htmlspecialchars($cinfo['contact']) ?></strong>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="w-100" id="cart-list">
                <?php 
                $total = 0;
                $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$_settings->userdata('id')}' AND c.product_id > 0 AND p.id > 0 AND p.delete_flag = 0 AND p.status = 1 order by p.name asc");
                while($row = $cart->fetch_assoc()):
                    // Calculate available stock
                    $stocks = $conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$row['product_id']}' AND type = 1")->fetch_assoc()['total_stock'];
                    $out = $conn->query("SELECT SUM(oi.quantity) as total_out FROM order_items oi 
                                        INNER JOIN order_list ol ON oi.order_id = ol.id 
                                        WHERE oi.product_id = '{$row['product_id']}' AND ol.status != 5")->fetch_assoc()['total_out'];
                    
                    $stocks = $stocks > 0 ? $stocks : 0;
                    $out = $out > 0 ? $out : 0;
                    $available = $stocks - $out;
                    
                    $total += ($row['quantity'] * $row['price']);
                ?>
                <div class="card mb-3 cart-item" data-id="<?= $row['id'] ?>">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="form-check">
                                    <input class="form-check-input item-checkbox" type="checkbox" value="<?= $row['id'] ?>" id="item_<?= $row['id'] ?>">
                                    <label class="form-check-label" for="item_<?= $row['id'] ?>"></label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="product-image-container">
                                    <img src="<?= validate_image($row['image_path']) ?>" alt="Product Image" class="prod-cart-img">
                                </div>
                            </div>
                            <div class="col flex-grow-1">
                                <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>" class="h4 text-muted">
                                    <p class="text-truncate-1 m-0"><?= $row['name'] ?></p>
                                </a>
                                <small><?= $row['brand'] ?></small><br>
                                <small><?= $row['category'] ?></small><br>
                                
                                <!-- Stock Availability -->
                                <?php if($available > 10): ?>
                                    <small class="stock-info"><i class="fa fa-check-circle"></i> In Stock (<?= $available ?> available)</small>
                                <?php elseif($available > 0): ?>
                                    <small class="stock-warning"><i class="fa fa-exclamation-triangle"></i> Low Stock (<?= $available ?> available)</small>
                                <?php else: ?>
                                    <small class="stock-warning"><i class="fa fa-times-circle"></i> Out of Stock</small>
                                <?php endif; ?>
                                
                                <!-- Color Selection -->
                                <div class="mt-2">
                                    <small class="text-muted">Color: </small>
                                    <?php 
                                    // Get available colors for this product
                                    $product_colors = $conn->query("SELECT available_colors FROM product_list WHERE id = '{$row['product_id']}'")->fetch_assoc();
                                    if($product_colors && !empty($product_colors['available_colors'])):
                                        $available_colors = explode(',', $product_colors['available_colors']);
                                        $available_colors = array_map('trim', $available_colors);
                                        $available_colors = array_filter($available_colors);
                                        
                                        if(!empty($available_colors)):
                                    ?>
                                        <select class="form-control form-control-sm d-inline-block color-selector" style="width: auto;" data-cart-id="<?= $row['id'] ?>">
                                            <?php foreach($available_colors as $color): ?>
                                                <option value="<?= htmlspecialchars($color) ?>" <?= $color == $row['color'] ? 'selected' : '' ?>><?= htmlspecialchars($color) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">No color options</span>
                                    <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">No color options</span>
                                    <?php endif; ?>
                                    <?php if(!empty($row['color'])): ?>
                                        <small class="text-success ml-2">
                                            <i class="fa fa-check-circle"></i> Selected: <strong><?= htmlspecialchars($row['color']) ?></strong>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Add-ons Section - Only show for motorcycles -->
                                <?php 
                                // Show recommendations ONLY for motorcycles
                                $is_motorcycle = (isset($row['category']) && strtolower($row['category']) === 'motorcycles');
                                if($is_motorcycle): 
                                ?>
                                <div class="add-ons-section mt-3">
                                    <div class="card border-info">
                                        <div class="card-header py-2 bg-info text-white">
                                            <h6 class="mb-0"><i class="fa fa-wrench"></i> Recommended Motorcycle Parts</h6>
                                        </div>
                                        <div class="card-body p-2">
                                            <?php 
                                            // Get suggested motorcycle parts based on product category
                                            $suggested_parts = $conn->query("SELECT p.*, b.name as brand, c.category FROM product_list p 
                                                                            INNER JOIN brand_list b ON p.brand_id = b.id 
                                                                            INNER JOIN categories c ON p.category_id = c.id 
                                                                            WHERE p.delete_flag = 0 AND p.status = 1 
                                                                            AND c.category IN ('Motorcycle Parts', 'Oils', 'Accessories') 
                                                                            AND p.id != '{$row['product_id']}'
                                                                            ORDER BY RAND() LIMIT 6");
                                            
                                            if($suggested_parts && $suggested_parts->num_rows > 0):
                                                $part_count = 0;
                                                while($part = $suggested_parts->fetch_assoc()):
                                                    $part_count++;
                                                    if($part_count % 2 == 1): ?>
                                                        <div class="row">
                                                    <?php endif; ?>
                                                    <div class="col-6 mb-2">
                                                        <div class="form-check border rounded p-2" style="background-color: #f8f9fa;">
                                                            <input class="form-check-input addon-checkbox" type="checkbox" 
                                                                   value="<?= $part['id'] ?>" 
                                                                   data-product="<?= $row['product_id'] ?>" 
                                                                   id="part_<?= $part['id'] ?>_<?= $row['id'] ?>" 
                                                                   data-price="<?= $part['price'] ?>"
                                                                   data-part-name="<?= htmlspecialchars($part['name']) ?>">
                                                            <label class="form-check-label" for="part_<?= $part['id'] ?>_<?= $row['id'] ?>" style="cursor: pointer;">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="mr-2">
                                                                        <img src="<?= validate_image($part['image_path']) ?>" alt="Part Image" 
                                                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <small><strong><?= htmlspecialchars($part['name']) ?></strong></small><br>
                                                                        <small class="text-muted"><?= $part['brand'] ?> - <?= $part['category'] ?></small><br>
                                                                        <small class="text-success font-weight-bold">(+₱<?= number_format($part['price'], 2) ?>)</small>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <?php if($part_count % 2 == 0): ?>
                                                        </div>
                                                    <?php endif;
                                                endwhile;
                                                if($part_count % 2 != 0): ?>
                                                    </div>
                                                <?php endif;
                                            else: ?>
                                                <div class="text-center text-muted py-2">
                                                    <i class="fa fa-info-circle"></i>
                                                    <small>No additional parts available at the moment.</small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if($available < $row['quantity']): ?>
                                    <div class="stock-warning">
                                        <i class="fa fa-exclamation-triangle"></i> 
                                        Only <?= $available ?> units available (you have <?= $row['quantity'] ?> in cart)
                                    </div>
                                <?php elseif($available <= 5): ?>
                                    <div class="stock-warning">
                                        <i class="fa fa-exclamation-triangle"></i> 
                                        Low stock: <?= $available ?> units available
                                    </div>
                                <?php else: ?>
                                    <div class="stock-info">
                                        <i class="fa fa-check-circle"></i> 
                                        <?= $available ?> units available
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <div class="d-flex align-items-center">
                                        <?php $is_motorcycle_qty_fixed = (isset($row['category']) && strtolower($row['category']) === 'motorcycles'); ?>
                                        <div class="input-group" style="width:8em">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-sm btn-outline-secondary btn-minus" data-id='<?= $row['id'] ?>' <?= ($is_motorcycle_qty_fixed || $row['quantity'] <= 1) ? 'disabled' : '' ?>><i class="fa fa-minus"></i></button>
                                            </div>
                                            <input type="text" value="<?= $row['quantity'] ?>" readonly class="form-control form-control-sm text-center">
                                            <div class="input-group-append">
                                                <button class="btn btn-sm btn-outline-secondary btn-plus" data-id='<?= $row['id'] ?>' <?= ($available <= $row['quantity'] || $is_motorcycle_qty_fixed) ? 'disabled' : '' ?>><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <span class="ml-2 unit-price">X ₱<?= number_format($row['price'],2) ?></span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <h5 class="mb-0 text-primary"><b>₱<?= number_format($row['quantity'] * $row['price'],2) ?></b></h5>
                                        <button class="btn btn-sm btn-outline-danger btn-remove ml-2" data-id="<?= $row['id'] ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php if($cart->num_rows <= 0): ?>
                <div class="d-flex align-items-center w-100 border justify-content-center">
                    <div class="col-12 text-center py-5">
                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No items in your cart</h5>
                        <p class="text-muted">You haven't added any items to your cart yet.</p>
                        <a href="./?p=products" class="btn btn-primary">
                            <i class="fa fa-shopping-bag"></i> Browse Products
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <div class="d-flex align-items-center w-100 border">
                    <div class="col-auto flex-grow-1 flex-shrink-1 px-3 py-2">
                            <h4 class="text-dark mb-0">SUBTOTAL</h4>
                    </div>
                    <div class="col-auto text-right px-3 py-2">
                        <h4 class="text-dark mb-0"><b id="subtotal_display">₱<?= number_format($total,2) ?></b></h4>
                    </div>
                </div>
                <div class="d-flex align-items-center w-100 border">
                    <div class="col-auto flex-grow-1 flex-shrink-1 px-3 py-2">
                            <h4 class="text-dark mb-0">ADD-ONS TOTAL</h4>
                    </div>
                    <div class="col-auto text-right px-3 py-2">
                        <h4 class="text-dark mb-0"><b id="addons_total_display">₱0.00</b></h4>
                    </div>
                </div>
                <div class="d-flex align-items-center w-100 border border-primary">
                    <div class="col-auto flex-grow-1 flex-shrink-1 px-3 py-2">
                            <h3 class="text-dark mb-0">GRAND TOTAL</h3>
                    </div>
                    <div class="col-auto text-right px-3 py-2">
                        <h3 class="text-dark mb-0"><b id="grand_total">₱<?= number_format($total,2) ?></b></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear-fix my-3"></div>
        
        <!-- Actions -->
        <div class="row mb-3">
            <div class="col-md-6">
                
            </div>
            <div class="col-md-6 text-right">
                <button class="btn btn-primary btn-lg" type="button" id="checkout" disabled>
                    <i class="fa fa-shopping-cart"></i> Proceed to Advance Order
                </button>
            </div>
        </div>
        
        <!-- Hidden form for POST submission to place_order -->
        <form id="checkoutForm" action="?p=place_order" method="POST" style="display: none;">
            <input type="hidden" name="selected_items" id="selectedItemsInput">
            <input type="hidden" name="addons" id="addonsInput">
            <input type="hidden" name="addon_details" id="addonDetailsInput">
            <input type="hidden" name="addons_total" id="addonsTotalInput">
        </form>
    </div>
</div>
<script>
    window.update_quantity = function($cart_id = 0, $quantity = ""){
        start_loader();
        console.log('Updating quantity for cart ID:', $cart_id, 'with quantity:', $quantity);
        
        // Validate cart ID
        if(!$cart_id || $cart_id == 0 || $cart_id == '0') {
            alert_toast('Invalid cart item ID.','error');
            end_loader();
            return;
        }
        
        $.ajax({
            url:_base_url_+'classes/Master.php?f=update_cart_quantity',
            data:{cart_id : $cart_id, quantity : $quantity},
            method:'POST',
            dataType:'json',
            error:err=>{
                console.error('AJAX Error:', err);
                alert_toast('An error occurred while updating quantity.','error')
                end_loader()
            },
            success:function(resp){
                console.log('Update quantity response:', resp);
                if(resp.status == 'success'){
                    // Update the quantity in the DOM
                    var cartItem = $('.cart-item[data-id="' + $cart_id + '"]');
                    var input = cartItem.find('input[type="text"]');
                    var newQty = resp.new_quantity || parseInt(input.val()) || 1;
                    
                    input.val(newQty);
                    
                    // Update the total price for this item
                    var priceText = cartItem.find('.text-primary b').text();
                    var price = parseFloat(priceText.replace('₱', '').replace(/,/g, '')) || 0;
                    var totalPrice = newQty * price;
                    cartItem.find('.text-primary b').text('₱' + totalPrice.toLocaleString());
                    
                    // Update cart totals
                    updateCartTotals();
                    
                    alert_toast('Quantity updated successfully.','success');
                }else if(!!resp.msg){
                    alert_toast(resp.msg,'error')
                }else{
                    alert_toast('An error occurred while updating quantity.','error')
                }
                end_loader();
            }
        })
    }
    $(function(){
        // Cleanup invalid cart items on page load
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=cleanup_invalid_cart_items',
            method: 'POST',
            dataType: 'json',
            success: function(resp) {
                if(resp.status == 'success') {
                    console.log('Cart cleanup completed');
                }
            }
        });
        
        $(document).on('click', '.btn-minus', function(e){
            e.preventDefault();
            e.stopPropagation();
            var cart_id = $(this).attr('data-id');
            var current_qty = parseInt($(this).closest('.input-group').find('input').val());
            if(current_qty > 1){
                update_quantity(cart_id, "- 1");
            } else {
                // Remove item if quantity would be 0
                _conf("Remove this item from cart?", "remove_from_cart", [cart_id]);
            }
        })
        $(document).on('click', '.btn-plus', function(e){
            e.preventDefault();
            e.stopPropagation();
            var cart_id = $(this).attr('data-id');
            update_quantity(cart_id, "+ 1");
        })
        $(document).on('click', '.btn-remove', function(e){
            e.preventDefault();
            e.stopPropagation();
            var cartId = $(this).attr('data-id');
            _conf("Are you sure to remove this product from cart list?","remove_from_cart",[cartId])
        })
        // Item checkbox change handler
        $(document).on('change', '.item-checkbox', function(){
            updateCartTotals();
        });
        
        $('#checkout').click(function(e){
            // Prevent action if button is disabled
            if($(this).prop('disabled')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            
            var checkedItems = $('.item-checkbox:checked').length;
            if(checkedItems > 0){
                proceedToCheckout();
            }else{
                alert_toast('Please select at least one item to proceed.','error')
            }
        })
        
        function proceedToCheckout(){
                try{
                    // Get only checked cart item IDs
                    var selectedCartItems = [];
                    $('.item-checkbox:checked').each(function(){
                        var cartId = $(this).val();
                        selectedCartItems.push(cartId);
                    });
                    
                    // Validate that we have selected items
                    if(selectedCartItems.length === 0){
                        alert_toast('Please select at least one item to proceed.','error');
                        return;
                    }
                    
                    // Get add-ons data from localStorage
                    var selectedAddons = localStorage.getItem('selected_addons') || '';
                    var selectedAddonDetails = localStorage.getItem('selected_addon_details') || '[]';
                    var addonsTotal = localStorage.getItem('addons_total') || '0';

                    // Set form data and submit
                    $('#selectedItemsInput').val(selectedCartItems.join(','));
                    $('#addonsInput').val(selectedAddons);
                    $('#addonDetailsInput').val(selectedAddonDetails);
                    $('#addonsTotalInput').val(addonsTotal);
                    
                    // Submit the form to place_order
                    $('#checkoutForm').submit();
                }catch(e){
                    console.error('Checkout error', e);
                    alert_toast('An error occurred while proceeding to checkout.','error');
                }
        }
    })
    function remove_from_cart($id){
        start_loader();
        console.log('Removing cart item with ID:', $id);
        
        // Validate cart ID
        if(!$id || $id == 0 || $id == '0') {
            alert_toast('Invalid cart item ID.','error');
            end_loader();
            return;
        }
        
        $.ajax({
            url:_base_url_+'classes/Master.php?f=remove_from_cart',
            data:{cart_id : $id},
            method:'POST',
            dataType:'json',
            error:err=>{
                console.error('AJAX Error:', err);
                // Close the confirmation modal on error
                $('#confirm_modal').modal('hide');
                alert_toast('An error occurred while removing item.','error')
                end_loader()
            },
            success:function(resp){
                console.log('Remove response:', resp);
                if(resp.status == 'success'){
                    // Close the confirmation modal
                    $('#confirm_modal').modal('hide');
                    
                    // Remove the cart item from DOM immediately
                    $('.cart-item[data-id="' + $id + '"]').fadeOut(300, function(){
                        $(this).remove();
                        // Check if cart is empty
                        if($('#cart-list .cart-item').length === 0){
                            $('#cart-list').html('<div class="d-flex align-items-center w-100 border justify-content-center"><div class="col-12 flex-grow-1 flex-shrink-1 px-1 py-1"><small class="text-muted">No Data</small></div></div>');
                        }
                        // Update totals
                        updateCartTotals();
                    });
                    alert_toast('Item removed from cart successfully.','success');
                }else if(!!resp.msg){
                    // Close the confirmation modal even on error
                    $('#confirm_modal').modal('hide');
                    alert_toast(resp.msg,'error')
                }else{
                    // Close the confirmation modal even on error
                    $('#confirm_modal').modal('hide');
                    alert_toast('An error occurred while removing item.','error')
                }
                end_loader();
            }
        })
    }
    
    function updateCartTotals(){
        var subtotal = 0;
        var hasCheckedItems = false;
        var totalItems = $('#cart-list .cart-item').length;
        var checkedCount = 0;
        
        $('#cart-list .cart-item').each(function(){
            var isChecked = $(this).find('.item-checkbox').is(':checked');
            if(isChecked) {
                checkedCount++;
                var quantity = parseInt($(this).find('input[type="text"]').val()) || 0;
                var priceText = $(this).find('.text-primary b').text();
                var price = parseFloat(priceText.replace('₱', '').replace(/,/g, '')) || 0;
                
                if(quantity > 0 && price > 0) {
                    subtotal += (quantity * price);
                    hasCheckedItems = true;
                }
            }
        });
        
        
        // Update totals based on checked items only
        $('#subtotal_display').text('₱' + subtotal.toLocaleString());
        var addonsTotal = parseFloat($('#addons_total_display').text().replace('₱', '').replace(/,/g, '')) || 0;
        var grandTotal = subtotal + addonsTotal;
        $('#grand_total').text('₱' + grandTotal.toLocaleString());
        
        // Enable/disable checkout button based on checked items
        if(hasCheckedItems) {
            $('#checkout').prop('disabled', false).removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('#checkout').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');
        }
    }
    
    // Add-ons functionality
    $('.addon-checkbox').change(function() {
        updateAddonsTotal();
    });
    
    function updateAddonsTotal() {
        var total = 0;
        var selectedAddons = [];
        var selectedAddonDetails = [];
        
        // Only count addons for checked cart items
        $('.cart-item').each(function() {
            var isChecked = $(this).find('.item-checkbox').is(':checked');
            if(isChecked) {
                $(this).find('.addon-checkbox:checked').each(function() {
                    var price = parseFloat($(this).data('price'));
                    var value = $(this).val();
                    var partName = $(this).data('part-name');
                    total += price;
                    selectedAddons.push(value);
                    selectedAddonDetails.push({
                        id: value,
                        name: partName,
                        price: price
                    });
                });
            }
        });
        
        // Update display
        $('#addons_total_display').text('₱' + total.toFixed(2));
        
        // Update grand total by calling updateCartTotals
        updateCartTotals();
        
        // Store add-ons data for checkout
        localStorage.setItem('selected_addons', selectedAddons.join(','));
        localStorage.setItem('selected_addon_details', JSON.stringify(selectedAddonDetails));
        localStorage.setItem('addons_total', total);
    }
    
    // Initialize cart - check all items by default and calculate totals
    $('.item-checkbox').prop('checked', true);
    updateCartTotals();
    updateAddonsTotal();
    
    // Color selector change functionality
    $('.color-selector').change(function() {
        const cartId = $(this).data('cart-id');
        const newColor = $(this).val();
        
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=update_cart_color",
            method: "POST",
            data: {
                cart_id: cartId,
                color: newColor
            },
            dataType: "json",
            error: err => {
                console.error(err);
                alert_toast("An error occurred while updating color", "error");
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    alert_toast(resp.msg, 'success');
                } else {
                    alert_toast(resp.msg || 'An error occurred', 'error');
                }
                end_loader();
            }
        });
    });
    
    // Bulk selection functionality removed per request
</script>