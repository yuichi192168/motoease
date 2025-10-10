<style>
    .prod-cart-img{
        width:10em;
        height:10em;
        object-fit:scale-down;
        object-position: center center;
    }
    .stock-warning {
        color: #dc3545;
        font-size: 0.8em;
    }
    .stock-info {
        color: #dc3545;
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
    
    .addon-checkbox:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
</style>
<div class="content py-5 mt-3">
    <div class="container">
        <h3><b>My Shopping Cart</b></h3>
        <hr>
        <div class="card card-outline card-primary shadow rounded-0">
            <div class="w-100" id="cart-list">
                <?php 
                $total = 0;
                $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$_settings->userdata('id')}' order by p.name asc");
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
                <div class="d-flex align-items-center w-100 border cart-item" data-id="<?= $row['id'] ?>">
                    <div class="col-auto flex-grow-1 flex-shrink-1 px-1 py-1">
                        <div class="d-flex align-items-center w-100 ">
                            <div class="col-auto">
                                <img src="<?= validate_image($row['image_path']) ?>" alt="Product Image" class="img-thumbnail prod-cart-img">
                            </div>
                            <div class="col-auto flex-grow-1 flex-shrink-1">
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
                                <?php if(!empty($row['color'])): ?>
                                    <br><small class="text-muted">Color: <strong><?= htmlspecialchars($row['color']) ?></strong></small>
                                <?php endif; ?>
                                
                                <!-- Add-ons Section -->
                                <div class="add-ons-section mt-2">
                                    <small class="text-muted">Recommended Motorcycle Parts:</small>
                                    <div class="add-ons-options">
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
                                                if($part_count % 3 == 1): ?>
                                                    <div class="row">
                                                <?php endif; ?>
                                                <div class="col-md-4 mb-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input addon-checkbox" type="checkbox" 
                                                               value="<?= $part['id'] ?>" 
                                                               data-product="<?= $row['product_id'] ?>" 
                                                               id="part_<?= $part['id'] ?>_<?= $row['id'] ?>" 
                                                               data-price="<?= $part['price'] ?>"
                                                               data-part-name="<?= htmlspecialchars($part['name']) ?>">
                                                        <label class="form-check-label" for="part_<?= $part['id'] ?>_<?= $row['id'] ?>">
                                                            <small><strong><?= htmlspecialchars($part['name']) ?></strong></small><br>
                                                            <small class="text-muted"><?= $part['brand'] ?> - <?= $part['category'] ?></small><br>
                                                            <small class="text-success">(+₱<?= number_format($part['price'], 2) ?>)</small>
                                                        </label>
                                                    </div>
                                                </div>
                                                <?php if($part_count % 3 == 0): ?>
                                                    </div>
                                                <?php endif;
                                            endwhile;
                                            if($part_count % 3 != 0): ?>
                                                </div>
                                            <?php endif;
                                        else: ?>
                                            <div class="text-muted">
                                                <small>No additional parts available at the moment.</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
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
                                
                                <div class="d-flex align-items-center w-100 mb-1">
                                    <div class="input-group " style="width:8em">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-sm btn-outline-secondary btn-minus" data-id='<?= $row['id'] ?>' <?= $available < 1 ? 'disabled' : '' ?>><i class="fa fa-minus"></i></button>
                                        </div>
                                        <input type="text" value="<?= $row['quantity'] ?>" readonly class="form-control form-control-sm text-center">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-outline-secondary btn-plus" data-id='<?= $row['id'] ?>' <?= $available <= $row['quantity'] ? 'disabled' : '' ?>><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <span class="ml-2">X <?= number_format($row['price'],2) ?></span>
                                </div>
                                <button class="btn btn-sm btn-flat btn-outline-danger btn-remove" data-id="<?= $row['id'] ?>"><i class="fa fa-times"></i> Remove</button>
                            </div>
                         </div>
                    </div>
                    <div class="col-auto text-right">
                        <h3><b><?= number_format($row['quantity'] * $row['price'],2) ?></b></h3>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php if($cart->num_rows <= 0): ?>
                <div class="d-flex align-items-center w-100 border justify-content-center">
                    <div class="col-12 flex-grow-1 flex-shrink-1 px-1 py-1">
                           <small class="text-muted">No Data</small>
                    </div>
                </div>
                <?php endif; ?>
                <div class="d-flex align-items-center w-100 border">
                    <div class="col-auto flex-grow-1 flex-shrink-1 px-1 py-1">
                            <h4 class="text-center">SUBTOTAL</h4>
                    </div>
                    <div class="col-auto text-right">
                        <h4><b>₱<?= number_format($total,2) ?></b></h4>
                    </div>
                </div>
                <div class="d-flex align-items-center w-100 border">
                    <div class="col-auto flex-grow-1 flex-shrink-1 px-1 py-1">
                            <h4 class="text-center text-success">ADD-ONS TOTAL</h4>
                    </div>
                    <div class="col-auto text-right">
                        <h4 class="text-success"><b id="addons_total_display">₱0.00</b></h4>
                    </div>
                </div>
                <div class="d-flex align-items-center w-100 border border-primary">
                    <div class="col-auto flex-grow-1 flex-shrink-1 px-1 py-1">
                            <h3 class="text-center text-primary">GRAND TOTAL</h3>
                    </div>
                    <div class="col-auto text-right">
                        <h3 class="text-primary"><b id="grand_total">₱<?= number_format($total,2) ?></b></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear-fix my-2"></div>
        <div class="text-right">
            <button class="btn btn-flat btn-sm btn-dark" type="button" id="checkout">Checkout</button>
        </div>
    </div>
</div>
<script>
    window.update_quantity = function($cart_id = 0, $quantity = ""){
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=update_cart_quantity',
            data:{cart_id : $cart_id, quantity : $quantity},
            method:'POST',
            dataType:'json',
            error:err=>{
                console.error(err)
                alert_toast('An error occurred.','error')
                end_loader()
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else if(!!resp.msg){
                    alert_toast(resp.msg,'error')
                }else{
                    alert_toast('An error occurred.','error')
                }
                end_loader();
            }
        })
    }
    $(function(){
        $('.btn-minus').click(function(){
            var cart_id = $(this).attr('data-id');
            var current_qty = parseInt($(this).closest('.input-group').find('input').val());
            if(current_qty > 1){
                update_quantity(cart_id, "- 1");
            } else {
                // Remove item if quantity would be 0
                _conf("Remove this item from cart?", "remove_from_cart", [cart_id]);
            }
        })
        $('.btn-plus').click(function(){
            var cart_id = $(this).attr('data-id');
            update_quantity(cart_id, "+ 1");
        })
        $('.btn-remove').click(function(){
            _conf("Are you sure to remove this product from cart list?","remove_from_cart",[$(this).attr('data-id')])
        })
        $('#checkout').click(function(){
            if($('#cart-list .cart-item').length > 0){
                // Check if cart contains motorcycle items
                var hasMotorcycles = false;
                $('#cart-list .cart-item').each(function(){
                    var itemText = $(this).text().toLowerCase();
                    if(itemText.includes('motorcycle') || itemText.includes('bike') || itemText.includes('honda')){
                        hasMotorcycles = true;
                        return false; // break loop
                    }
                });
                
                if(hasMotorcycles){
                    // Show credit application reminder
                    Swal.fire({
                        title: 'Credit Application Required',
                        html: `
                            <div class="text-center">
                                <i class="fa fa-file-alt text-warning" style="font-size: 3rem;"></i>
                                <p class="mt-3">Your cart contains motorcycle items.</p>
                                <p class="text-muted">You'll need to complete the Motorcentral Credit Application form before checkout.</p>
                                <div class="alert alert-info text-left">
                                    <strong>Required Documents:</strong><br>
                                    • 2 Valid IDs with 3 signatures (front & back)<br>
                                    • Proof of billing (Meralco, Water, or Internet bill)<br>
                                    • Proof of income (Payslip, COE, or Bank Statement)<br>
                                    • Sketch of your house location
                                </div>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Continue to Checkout',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#007bff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceedToCheckout();
                        }
                    });
                } else {
                    proceedToCheckout();
                }
            }else{
                alert_toast('Shopping cart is empty.','error')
            }
        })
        
        function proceedToCheckout(){
            // Get add-ons data from localStorage
            var selectedAddons = localStorage.getItem('selected_addons') || '';
            var selectedAddonDetails = localStorage.getItem('selected_addon_details') || '[]';
            var addonsTotal = localStorage.getItem('addons_total') || '0';
            
            // Pass add-ons data to checkout page
            var url = './?p=place_order';
            if(selectedAddons) {
                url += '&addons=' + encodeURIComponent(selectedAddons);
                url += '&addon_details=' + encodeURIComponent(selectedAddonDetails);
                url += '&addons_total=' + encodeURIComponent(addonsTotal);
            }
            
            location.href = url;
        }
    })
    function remove_from_cart($id){
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=remove_from_cart',
            data:{cart_id : $id},
            method:'POST',
            dataType:'json',
            error:err=>{
                console.error(err)
                alert_toast('An error occurred.','error')
                end_loader()
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else if(!!resp.msg){
                    alert_toast(resp.msg,'error')
                }else{
                    alert_toast('An error occurred.','error')
                }
                end_loader();
            }
        })
    }
    
    // Add-ons functionality
    $('.addon-checkbox').change(function() {
        updateAddonsTotal();
    });
    
    function updateAddonsTotal() {
        var total = 0;
        var selectedAddons = [];
        var selectedAddonDetails = [];
        
        $('.addon-checkbox:checked').each(function() {
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
        
        // Update display
        $('#addons_total_display').text('₱' + total.toFixed(2));
        
        // Update grand total
        var subtotal = parseFloat('<?= $total ?>');
        var grandTotal = subtotal + total;
        $('#grand_total').text('₱' + grandTotal.toFixed(2));
        
        // Store add-ons data for checkout
        localStorage.setItem('selected_addons', selectedAddons.join(','));
        localStorage.setItem('selected_addon_details', JSON.stringify(selectedAddonDetails));
        localStorage.setItem('addons_total', total);
    }
    
    // Initialize addons total
    updateAddonsTotal();
</script>