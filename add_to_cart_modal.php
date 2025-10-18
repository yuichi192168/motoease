<?php
require_once('./inc/sess_auth.php');
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : '';
if(empty($product_id)){
    echo '<div class="alert alert-danger">Invalid Product ID</div>';
    exit;
}

$product = $conn->query("SELECT p.*, b.name as brand, c.category FROM product_list p 
                        INNER JOIN brand_list b ON p.brand_id = b.id 
                        INNER JOIN categories c ON p.category_id = c.id 
                        WHERE p.id = '{$product_id}' AND p.delete_flag = 0 AND p.status = 1")->fetch_assoc();

if(!$product){
    echo '<div class="alert alert-danger">Product not found</div>';
    exit;
}

// Get available stock
$stocks = $conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$product_id}' AND type = 1")->fetch_assoc()['total_stock'];
$out = $conn->query("SELECT SUM(oi.quantity) as total_out FROM order_items oi 
                    INNER JOIN order_list ol ON oi.order_id = ol.id 
                    WHERE oi.product_id = '{$product_id}' AND ol.status != 5")->fetch_assoc()['total_out'];

$stocks = $stocks > 0 ? $stocks : 0;
$out = $out > 0 ? $out : 0;
$available = $stocks - $out;
?>

<style>
.modal-header {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border-bottom: none;
}

.modal-header .close {
    color: white;
    opacity: 0.8;
    font-size: 1.5rem;
}

.modal-header .close:hover {
    opacity: 1;
    color: white;
}

.product-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 8px;
    background: #f8f9fa;
    height: 300px;
}

.product-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-image-container:hover img {
    transform: scale(1.05);
}

.stock-warning {
    color: #dc3545;
    font-size: 0.9em;
}

.stock-info {
    color: #28a745;
    font-size: 0.9em;
}

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
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="product-image-container">
                <img src="<?= validate_image($product['image_path']) ?>" alt="Product Image" class="img-fluid">
            </div>
        </div>
        <div class="col-md-6">
            <div class="product-details">
                <h4 class="text-primary mb-2"><?= htmlspecialchars($product['name']) ?></h4>
                <p class="text-muted mb-2">
                    <strong>Brand:</strong> <?= htmlspecialchars($product['brand']) ?><br>
                    <strong>Category:</strong> <?= htmlspecialchars($product['category']) ?>
                </p>
                
                <!-- Stock Availability -->
                <?php if($available > 10): ?>
                    <div class="stock-info mb-3">
                        <i class="fa fa-check-circle"></i> In Stock (<?= $available ?> available)
                    </div>
                <?php elseif($available > 0): ?>
                    <div class="stock-warning mb-3">
                        <i class="fa fa-exclamation-triangle"></i> Low Stock (<?= $available ?> available)
                    </div>
                <?php else: ?>
                    <div class="stock-warning mb-3">
                        <i class="fa fa-times-circle"></i> Out of Stock
                    </div>
                <?php endif; ?>
                
                <div class="price-section mb-3">
                    <h3 class="text-primary mb-0">₱<?= number_format($product['price'], 2) ?></h3>
                </div>
                
                <!-- Color Selection -->
                <?php if(!empty($product['available_colors'])): ?>
                    <?php 
                    $available_colors = explode(',', $product['available_colors']);
                    $available_colors = array_map('trim', $available_colors);
                    $available_colors = array_filter($available_colors);
                    
                    if(!empty($available_colors)):
                    ?>
                    <div class="mb-3">
                        <label class="form-label"><strong>Color:</strong></label>
                        <select class="form-control" id="color_selector">
                            <option value="">Select Color</option>
                            <?php foreach($available_colors as $color): ?>
                                <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Quantity Selection -->
                <div class="mb-3">
                    <label class="form-label"><strong>Quantity:</strong></label>
                    <div class="input-group" style="width: 150px;">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="qty_minus">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="<?= $available ?>" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary btn-sm" type="button" id="qty_plus">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Add-ons Section - Only show for motorcycles -->
                <?php 
                $is_motorcycle = (isset($product['category']) && strtolower($product['category']) === 'motorcycles');
                if($is_motorcycle): 
                ?>
                <div class="add-ons-section mb-3">
                    <div class="card border-info">
                        <div class="card-header py-2 bg-info text-white">
                            <h6 class="mb-0"><i class="fa fa-wrench"></i> Recommended Motorcycle Parts</h6>
                        </div>
                        <div class="card-body p-2">
                            <?php 
                            // Get suggested motorcycle parts
                            $suggested_parts = $conn->query("SELECT p.*, b.name as brand, c.category FROM product_list p 
                                                            INNER JOIN brand_list b ON p.brand_id = b.id 
                                                            INNER JOIN categories c ON p.category_id = c.id 
                                                            WHERE p.delete_flag = 0 AND p.status = 1 
                                                            AND c.category IN ('Motorcycle Parts', 'Oils', 'Accessories') 
                                                            AND p.id != '{$product_id}'
                                                            ORDER BY RAND() LIMIT 4");
                            
                            if($suggested_parts && $suggested_parts->num_rows > 0):
                                while($part = $suggested_parts->fetch_assoc()): ?>
                                    <div class="form-check border rounded p-2 mb-2" style="background-color: #f8f9fa;">
                                        <input class="form-check-input addon-checkbox" type="checkbox" 
                                               value="<?= $part['id'] ?>" 
                                               id="part_<?= $part['id'] ?>" 
                                               data-price="<?= $part['price'] ?>"
                                               data-part-name="<?= htmlspecialchars($part['name']) ?>">
                                        <label class="form-check-label" for="part_<?= $part['id'] ?>" style="cursor: pointer;">
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
                                <?php endwhile;
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
                
                <!-- Total Price Display -->
                <div class="total-section mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Subtotal:</strong></span>
                        <span class="h5 text-primary mb-0" id="subtotal">₱<?= number_format($product['price'], 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Add-ons:</strong></span>
                        <span class="h6 text-success mb-0" id="addons_total">₱0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Total:</strong></span>
                        <span class="h4 text-primary mb-0" id="grand_total">₱<?= number_format($product['price'], 2) ?></span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary flex-fill" id="add_to_cart" <?= $available <= 0 ? 'disabled' : '' ?>>
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    var basePrice = <?= $product['price'] ?>;
    var selectedAddons = [];
    var selectedAddonDetails = [];
    
    // Quantity controls
    $('#qty_minus').click(function(){
        var currentQty = parseInt($('#quantity').val());
        if(currentQty > 1){
            $('#quantity').val(currentQty - 1);
            updateTotals();
        }
    });
    
    $('#qty_plus').click(function(){
        var currentQty = parseInt($('#quantity').val());
        var maxQty = parseInt($('#quantity').attr('max'));
        if(currentQty < maxQty){
            $('#quantity').val(currentQty + 1);
            updateTotals();
        }
    });
    
    // Add-ons change handler
    $('.addon-checkbox').change(function(){
        updateTotals();
    });
    
    function updateTotals(){
        var quantity = parseInt($('#quantity').val()) || 1;
        var subtotal = basePrice * quantity;
        var addonsTotal = 0;
        
        selectedAddons = [];
        selectedAddonDetails = [];
        
        $('.addon-checkbox:checked').each(function(){
            var price = parseFloat($(this).data('price'));
            var value = $(this).val();
            var partName = $(this).data('part-name');
            addonsTotal += price;
            selectedAddons.push(value);
            selectedAddonDetails.push({
                id: value,
                name: partName,
                price: price
            });
        });
        
        var grandTotal = subtotal + addonsTotal;
        
        $('#subtotal').text('₱' + subtotal.toLocaleString());
        $('#addons_total').text('₱' + addonsTotal.toFixed(2));
        $('#grand_total').text('₱' + grandTotal.toLocaleString());
    }
    
    // Add to cart functionality
    $('#add_to_cart').click(function(){
        var quantity = parseInt($('#quantity').val()) || 1;
        var color = $('#color_selector').val() || '';
        
        if(quantity <= 0){
            alert_toast('Please select a valid quantity', 'error');
            return;
        }
        
        start_loader();
        
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=add_to_cart',
            method: 'POST',
            data: {
                product_id: '<?= $product_id ?>',
                quantity: quantity,
                color: color,
                addons: selectedAddons.join(','),
                addon_details: JSON.stringify(selectedAddonDetails)
            },
            dataType: 'json',
            error: function(err){
                console.error('AJAX Error:', err);
                alert_toast('An error occurred while adding to cart', 'error');
                end_loader();
            },
            success: function(resp){
                if(resp.status == 'success'){
                    alert_toast('Product added to cart successfully', 'success');
                    $('#uni_modal').modal('hide');
                    // Update cart count if on a page that has it
                    if(typeof updateCartCount === 'function'){
                        updateCartCount();
                    }
                } else {
                    alert_toast(resp.msg || 'An error occurred', 'error');
                }
                end_loader();
            }
        });
    });
    
    // Initialize totals
    updateTotals();
});
</script>
