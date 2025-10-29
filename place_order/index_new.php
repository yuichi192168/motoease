<?php 
require_once('../inc/sess_auth.php');
?>
<style>
    /* Red and Black Theme for Place Order */
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
    
    .text-primary {
        color: #dc3545 !important;
    }
    
    .card-outline.card-info {
        border-color: #dc3545;
    }
    
    .card-outline.card-info .card-header {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }
    
    .card-outline.card-dark {
        border-color: #343a40;
    }
    
    .card-outline.card-dark .card-header {
        background: linear-gradient(135deg, #343a40, #000000);
        color: white;
    }
    
    .form-check-input:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 15px;
        }
        
        .row {
            flex-direction: column;
        }
        
        .col-md-6 {
            margin-bottom: 20px;
        }
    }
</style>

<div class="content py-5 mt-3">
    <div class="container">
        <h3><b>Place Order</b></h3>
        <hr>
        <div class="row">
            <!-- Order Summary -->
            <div class="col-md-6">
                <div class="card card-outline card-info shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title">Order Summary</h4>
                    </div>
                    <div class="card-body">
                        <?php 
                        // Check if order contains motorcycle items and application status
                        $customer_id = $_settings->userdata('id');
                        
                        // Check if user is logged in
                        if(empty($customer_id) || $customer_id <= 0) {
                            echo '<div class="alert alert-danger">Please log in to proceed with checkout.</div>';
                            return;
                        }
                        
                        $motorcycle_items = $conn->query("SELECT COUNT(*) as count FROM cart_list c 
                                                        INNER JOIN product_list p ON c.product_id = p.id 
                                                        INNER JOIN categories cat ON p.category_id = cat.id 
                                                        WHERE c.client_id = '{$customer_id}' 
                                                        AND (cat.category LIKE '%motorcycle%' OR cat.category LIKE '%bike%' OR p.name LIKE '%motorcycle%' OR p.name LIKE '%bike%')");
                        $has_motorcycles = $motorcycle_items->fetch_assoc()['count'] > 0;
                        
                        // Always check application status for motorcycle orders
                        $application_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = '{$customer_id}'")->fetch_assoc();
                        $application_completed = $application_status && $application_status['credit_application_completed'] == 1;
                        
                        if($has_motorcycles):
                        ?>
                        <div class="alert <?= $application_completed ? 'alert-success' : 'alert-warning' ?> mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-file-alt fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Motorcentral Credit Application</h6>
                                    <?php if($application_completed): ?>
                                        <p class="mb-0"><i class="fa fa-check-circle text-success"></i> Application completed - Ready to proceed</p>
                                    <?php else: ?>
                                        <p class="mb-0"><i class="fa fa-exclamation-triangle text-warning"></i> Application required before checkout</p>
                                        <small class="text-muted">You'll be redirected to complete the application form</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php 
                        $total = 0;
                        
                        // Get selected cart items from POST data (preferred) or URL parameter (fallback)
                        $selected_items = isset($_POST['selected_items']) ? $_POST['selected_items'] : (isset($_GET['selected_items']) ? $_GET['selected_items'] : '');
                        
                        if(!empty($selected_items)) {
                            // Filter cart items to only include selected ones
                            $selected_items = explode(',', $selected_items);
                            $selected_items = array_map('intval', $selected_items); // Convert to integers for security
                            $selected_items = array_filter($selected_items); // Remove empty values
                            
                            if(!empty($selected_items)) {
                                $selected_items_str = implode(',', $selected_items);
                                
                                // First, check if the cart items exist for this user
                                $check_cart = $conn->query("SELECT COUNT(*) as count FROM cart_list WHERE client_id = '{$customer_id}' AND id IN ({$selected_items_str})");
                                $cart_exists = $check_cart ? $check_cart->fetch_assoc()['count'] : 0;
                                
                                if($cart_exists > 0) {
                                    // Cart items exist, now get the full details
                                    $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$customer_id}' AND c.id IN ({$selected_items_str}) AND c.product_id > 0 AND p.id > 0 AND p.delete_flag = 0 AND p.status = 1 order by p.name asc");
                                    
                                    // If no results due to product issues, try a more lenient query
                                    if(!$cart || $cart->num_rows == 0) {
                                        $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$customer_id}' AND c.id IN ({$selected_items_str}) order by p.name asc");
                                    }
                                } else {
                                    // Cart items don't exist, show all items instead
                                    $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$customer_id}' AND c.product_id > 0 AND p.id > 0 AND p.delete_flag = 0 AND p.status = 1 order by p.name asc");
                                }
                            } else {
                                // If no valid selected items, show all items
                                $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$customer_id}' AND c.product_id > 0 AND p.id > 0 AND p.delete_flag = 0 AND p.status = 1 order by p.name asc");
                            }
                        } else {
                            // Fallback: if no selected items, show all items (for backward compatibility)
                            $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$customer_id}' AND c.product_id > 0 AND p.id > 0 AND p.delete_flag = 0 AND p.status = 1 order by p.name asc");
                        }
                        
                        if($cart && $cart->num_rows > 0) {
                            while($row = $cart->fetch_assoc()):
                            $total += ($row['quantity'] * $row['price']);
                        ?>
                        <div class="d-flex align-items-center w-100 border-bottom py-2">
                            <div class="col-auto">
                                <img src="<?= validate_image($row['image_path']) ?>" alt="Product Image" style="width:3em; height:3em; object-fit:scale-down;">
                            </div>
                            <div class="col flex-grow-1">
                                <h6 class="mb-0"><?= $row['name'] ?></h6>
                                <small class="text-muted"><?= $row['brand'] ?> - <?= $row['category'] ?></small><br>
                                <?php if(!empty($row['color'])): ?>
                                <small class="text-muted">Color: <?= htmlspecialchars($row['color']) ?></small><br>
                                <?php endif; ?>
                                <small class="text-muted">Qty: <?= $row['quantity'] ?> x ₱<?= number_format($row['price'],2) ?></small>
                            </div>
                            <div class="col-auto">
                                <strong>₱<?= number_format($row['quantity'] * $row['price'],2) ?></strong>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php } else { ?>
                        <div class="text-center py-3">
                            <i class="fa fa-shopping-cart fa-2x text-muted mb-2"></i>
                            <h6 class="text-muted">No items selected for checkout</h6>
                            <small class="text-muted">Please go back to your cart and select items to proceed.</small>
                            <div class="mt-3">
                                <a href="../?p=cart" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Cart
                                </a>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <?php 
                        // Get add-ons data from POST data (preferred) or URL parameters (fallback)
                        $addons = isset($_POST['addons']) ? $_POST['addons'] : (isset($_GET['addons']) ? $_GET['addons'] : '');
                        $addon_details = isset($_POST['addon_details']) ? json_decode($_POST['addon_details'], true) : (isset($_GET['addon_details']) ? json_decode($_GET['addon_details'], true) : []);
                        $addons_total = isset($_POST['addons_total']) ? floatval($_POST['addons_total']) : (isset($_GET['addons_total']) ? floatval($_GET['addons_total']) : 0);
                        $grand_total = $total + $addons_total;
                        ?>
                        
                        <?php if($addons_total > 0): ?>
                        <div class="d-flex justify-content-between align-items-center w-100 border-top pt-3 mt-3">
                            <h5 class="mb-0">Subtotal:</h5>
                            <h4 class="mb-0">₱<?= number_format($total,2) ?></h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center w-100 pt-2">
                            <h5 class="mb-0 text-success">Add-ons Total:</h5>
                            <h4 class="mb-0 text-success">₱<?= number_format($addons_total,2) ?></h4>
                        </div>
                        <div class="d-flex justify-content-between align-items-center w-100 border-top pt-3 mt-3">
                            <h5 class="mb-0">Grand Total:</h5>
                            <h4 class="mb-0 text-primary">₱<?= number_format($grand_total,2) ?></h4>
                        </div>
                        <?php else: ?>
                        <div class="d-flex justify-content-between align-items-center w-100 border-top pt-3 mt-3">
                            <h5 class="mb-0">Total Amount:</h5>
                            <h4 class="mb-0 text-primary">₱<?= number_format($total,2) ?></h4>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Checkout Form -->
            <div class="col-md-6">
                <div class="card card-outline card-dark shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title">Complete Your Order</h4>
                    </div>
                    <div class="card-body">
                        <?php if($addons_total > 0 && !empty($addon_details)): ?>
                        <div class="alert alert-info">
                            <h6><i class="fa fa-info-circle"></i> Selected Motorcycle Parts:</h6>
                            <ul class="mb-2">
                                <?php foreach($addon_details as $addon): ?>
                                <li><strong><?= htmlspecialchars($addon['name']) ?></strong> - ₱<?= number_format($addon['price'], 2) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <small class="text-muted">Total add-ons: ₱<?= number_format($addons_total, 2) ?></small>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(!$cart || $cart->num_rows == 0): ?>
                        <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>No items available for checkout.</strong> 
                            <?php if(!empty($selected_items)): ?>
                                The selected items may have been removed or are no longer available. 
                            <?php endif; ?>
                            Please go back to your cart and select items to proceed with your order.
                        </div>
                        <?php endif; ?>
                        
                        <form action="" id="place_order" <?= ($cart && $cart->num_rows > 0) ? '' : 'style="opacity: 0.5; pointer-events: none;"' ?>>
                            <input type="hidden" name="selected_items" value="<?= htmlspecialchars(is_array($selected_items) ? implode(',', $selected_items) : $selected_items) ?>">
                            <input type="hidden" name="addons" value="<?= htmlspecialchars($addons) ?>">
                            <input type="hidden" name="addons_total" value="<?= $addons_total ?>">
                            
                            <!-- Payment Method Selection -->
                            <div class="form-group payment-method-section">
                                <label for="payment_method" class="form-label"><strong>Payment Method *</strong></label>
                                <select class="form-control" name="payment_method" id="payment_method" required>
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="full_payment">Full Payment (Cash/Card)</option>
                                    <option value="installment">Installment Plan (Motorcycle Orders Only)</option>
                                </select>
                                <div class="invalid-feedback" id="payment_method_error">Please select a payment method.</div>
                                
                                <div class="alert alert-info mt-2">
                                    <i class="fa fa-info-circle"></i> <strong>Payment Information:</strong> Full payment is required for all orders. You can proceed directly to Advance Order.
                                </div>
                            </div>
                            
                            <!-- Full Payment Option -->
                            <div id="full_payment_section" class="form-group" style="display: none;">
                                <label for="payment_type" class="form-label"><strong>Payment Type *</strong></label>
                                <select class="form-control" name="payment_type" id="payment_type">
                                    <option value="">-- Select Payment Type --</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Credit/Debit Card</option>
                                </select>
                                <div class="invalid-feedback" id="payment_type_error"></div>
                            </div>
                            
                            
                            <!-- Customer Information -->
                            <div class="form-group">
                                <label class="form-label"><strong>Customer Information</strong></label>
                                <?php 
                                $cust = $conn->query("SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) AS fullname, contact FROM client_list WHERE id = '{$_settings->userdata('id')}'");
                                $cinfo = $cust && $cust->num_rows ? $cust->fetch_assoc() : ['fullname'=>'','contact'=>''];
                                ?>
                                <div class="border rounded p-2 bg-light">
                                    <div><small class="text-muted">Name</small><br><strong><?= htmlspecialchars($cinfo['fullname']) ?></strong></div>
                                    <div class="mt-2"><small class="text-muted">Contact</small><br><strong><?= htmlspecialchars($cinfo['contact']) ?></strong></div>
                                </div>
                                <small class="text-muted">Pickup-based orders: Please verify your contact details.</small>
                            </div>
                            
                            <!-- Terms and Conditions -->
                            <div class="form-group">
                                <div class="form-check terms-checkbox-container">
                                    <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" required>
                                    <label class="form-check-label" for="terms_accepted">
                                        I agree to the <a href="#" data-toggle="modal" data-target="#termsModal" class="terms-link">Terms and Conditions</a> <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback" id="terms_accepted_error">Please agree to the terms and conditions before proceeding.</div>
                                </div>
                            </div>
                            
                            <div class="form-group text-right">
                                <button class="btn btn-flat btn-primary" type="submit" id="place_order_btn" disabled>
                                    <i class="fa fa-shopping-cart"></i> <?php if(isset($has_motorcycles) && $has_motorcycles): ?><?php echo isset($application_completed) && $application_completed ? 'Continue to Order' : 'Complete Credit Application'; ?><?php else: ?>Advance Order<?php endif; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Payment Terms</h6>
                <ul>
                    <li>Full Payment: Payment must be completed before delivery</li>
                    <li>Cash or Card payments are accepted</li>
                    <li>Payment confirmation required before order processing</li>
                </ul>
                
                <h6>Delivery Terms</h6>
                <ul>
                    <li>Delivery is free within Calamba City area</li>
                    <li>Out-of-town deliveries may incur additional charges</li>
                    <li>Delivery time: 3-5 business days for in-stock items</li>
                    <li>Customer must be present to receive the order</li>
                </ul>
                
                <h6>Return and Exchange Policy</h6>
                <ul>
                    <li>Returns accepted within 7 days of delivery</li>
                    <li>Items must be in original condition with tags</li>
                    <li>Custom orders are non-returnable</li>
                    <li>Refunds will be processed within 5-7 business days</li>
                </ul>
                
                <h6>Warranty</h6>
                <ul>
                    <li>All products come with manufacturer warranty</li>
                    <li>Warranty terms vary by product and manufacturer</li>
                    <li>Warranty does not cover normal wear and tear</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        // Payment method change handler
        $('#payment_method').change(function(){
            var paymentMethod = $(this).val();
            
            // Hide all sections first
            $('#full_payment_section').hide();
            
            // Clear validation states
            $('#payment_type').removeClass('is-invalid is-valid');
            $('#payment_type_error').text('');
            
            // Show relevant section
            if(paymentMethod === 'full_payment') {
                $('#full_payment_section').show();
                $('#payment_type').prop('required', true);
            }
        });
        
        
        // Terms and Conditions checkbox handler
        var hasMotorcycles = <?php echo isset($has_motorcycles) && $has_motorcycles ? 'true' : 'false'; ?>;
        var applicationCompleted = <?php echo isset($application_completed) && $application_completed ? 'true' : 'false'; ?>;
        
        $('#terms_accepted').on('change', function(){
            var isChecked = $(this).is(':checked');
            
            // Update button state
            if(isChecked) {
                $('#place_order_btn').prop('disabled', false);
                $('#terms_accepted').removeClass('is-invalid').addClass('is-valid');
                $('#terms_accepted_error').text('').hide();
            } else {
                $('#place_order_btn').prop('disabled', true);
                $('#terms_accepted').removeClass('is-valid');
            }
            
            // Update button text based on cart contents
            if(hasMotorcycles){
                if(!applicationCompleted){
                    $('#place_order_btn').html('<i class="fa fa-shopping-cart"></i> Complete Credit Application');
                } else {
                    $('#place_order_btn').html('<i class="fa fa-shopping-cart"></i> Continue to Order');
                }
            } else {
                $('#place_order_btn').html('<i class="fa fa-shopping-cart"></i> Advance Order');
            }
        }).trigger('change');

        // Submit order
        $('#place_order').submit(function(e){
            e.preventDefault();
            
            // Validate Terms and Conditions first
            if(!$('#terms_accepted').is(':checked')) {
                $('#terms_accepted').addClass('is-invalid');
                $('#terms_accepted_error').text('Please agree to the terms and conditions before proceeding.').show();
                alert_toast('Please agree to the terms and conditions before proceeding.', 'warning');
                return false;
            }
            
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            
            // Disable submit button to prevent double submission
            $('#place_order_btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
            
            $.ajax({
                url:_base_url_+"classes/Master.php?f=place_order",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err);
                    alert_toast("An error occurred",'error');
                    end_loader();
                    $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Advance Order');
                },
                success:function(resp){
                    end_loader();
                    $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Advance Order');
                    
                    if(typeof resp =='object' && resp.status == 'success'){
                        uni_modal('Order Placed Successfully','../success_msg.php');
                        if(resp.ref_code){
                            alert_toast('Reference Code: '+resp.ref_code,'success');
                        }
                        setTimeout(function(){ location.replace('../?p=my_orders'); }, 3000);
                    }else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>');
                        el.addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                    }else{
                        alert_toast("An error occurred while processing your order.", 'error');
                        console.log(resp);
                    }
                }
            });
        });
    });
</script>
