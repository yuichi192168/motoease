<?php 
require_once(__DIR__ . '/../inc/sess_auth.php');
?>
<div class="content py-5 mt-3">
    <div class="container">
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
                        
                        // Enhanced motorcycle detection logic (case-insensitive and excludes parts/oils/accessories variants)
                        $motorcycle_items = $conn->query("SELECT COUNT(*) as count FROM cart_list c 
                                                        INNER JOIN product_list p ON c.product_id = p.id 
                                                        INNER JOIN categories cat ON p.category_id = cat.id 
                                                        WHERE c.client_id = '{$customer_id}' 
                                                        AND (LOWER(cat.category) LIKE '%motorcycle%' OR LOWER(cat.category) LIKE '%bike%' OR LOWER(p.name) LIKE '%motorcycle%' OR LOWER(p.name) LIKE '%bike%')
                                                        AND LOWER(cat.category) NOT IN ('motorcycle parts', 'oils', 'genuine oils', 'accessories')");
                        $has_motorcycles = $motorcycle_items->fetch_assoc()['count'] > 0;
                        
                        // Check for non-motorcycle items (parts, oils, accessories) including 'genuine oils' (case-insensitive)
                        $non_motorcycle_items = $conn->query("SELECT COUNT(*) as count FROM cart_list c 
                                                             INNER JOIN product_list p ON c.product_id = p.id 
                                                             INNER JOIN categories cat ON p.category_id = cat.id 
                                                             WHERE c.client_id = '{$customer_id}' 
                                                             AND LOWER(cat.category) IN ('motorcycle parts', 'oils', 'genuine oils', 'accessories')");
                        $has_parts_oils = $non_motorcycle_items->fetch_assoc()['count'] > 0;
                        
                        // Always check application status for motorcycle orders
                        $application_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = '{$customer_id}'")->fetch_assoc();
                        $application_completed = $application_status && $application_status['credit_application_completed'] == 1;
                        
                        // Display appropriate checkout flow information
                        if($has_motorcycles):
                        ?>
                        <div class="alert <?= $application_completed ? 'alert-success' : 'alert-warning' ?> mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-file-alt fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Motorcentral Credit Application Required</h6>
                                    <?php if($application_completed): ?>
                                        <p class="mb-0"><i class="fa fa-check-circle text-success"></i> Application completed - Ready to proceed with motorcycle order</p>
                                        <small class="text-muted">You can now complete your motorcycle purchase</small>
                                    <?php else: ?>
                                        <p class="mb-0"><i class="fa fa-exclamation-triangle text-warning"></i> Credit application required for motorcycle purchase</p>
                                        <small class="text-muted">You'll be redirected to complete the Motorcentral Credit Application form</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php elseif($has_parts_oils): ?>
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-shopping-cart fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Advance Order Process</h6>
                                    <p class="mb-0"><i class="fa fa-info-circle text-info"></i> Parts and accessories order - No credit application required</p>
                                    <small class="text-muted">You can proceed directly to advance order</small>
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
                        
                        <form action="" id="place_order">
                            <input type="hidden" name="selected_items" value="<?= htmlspecialchars(is_array($selected_items) ? implode(',', $selected_items) : $selected_items) ?>">
                            <input type="hidden" name="addons" value="<?= htmlspecialchars($addons) ?>">
                            <input type="hidden" name="addons_total" value="<?= $addons_total ?>">
                            
                            <!-- Payment Method Selection -->
                            <div class="form-group payment-method-section">
                                <label for="payment_method" class="form-label"><strong>Payment Method *</strong></label>
                                <select class="form-control" name="payment_method" id="payment_method" required>
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="full_payment">Full Payment (Cash/Card)</option>
                                    <?php if(isset($has_motorcycles) && $has_motorcycles): ?>
                                    <option value="installment">Installment Plan</option>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback" id="payment_method_error">Please select a payment method.</div>
                                
                                <?php if(isset($has_motorcycles) && $has_motorcycles): ?>
                                <div class="installment-requirements">
                                    <h6><i class="fa fa-info-circle"></i> Installment Requirements</h6>
                                    <ul>
                                        <li>Minimum 20% down payment required</li>
                                        <li>Credit application must be completed</li>
                                        <li>Monthly payments due on the same date each month</li>
                                        <li>Late payment fees may apply for overdue installments</li>
                                    </ul>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info mt-2">
                                    <i class="fa fa-info-circle"></i> <strong>Motorcycle Parts Only:</strong> Installment plans are available only for motorcycle purchases. You can proceed directly to Advance Order.
                                </div>
                                <?php endif; ?>
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
                            
                            <!-- Installment Options -->
                            <div id="installment_section" class="form-group" style="display: none;">
                                <label for="installment_months" class="form-label"><strong>Installment Period *</strong></label>
                                <select class="form-control" name="installment_months" id="installment_months">
                                    <option value="">-- Select Installment Period --</option>
                                    <option value="6">6 Months</option>
                                    <option value="12">12 Months</option>
                                    <option value="18">18 Months</option>
                                    <option value="24">24 Months</option>
                                </select>
                                <div class="invalid-feedback" id="installment_months_error"></div>
                                
                                <div class="mt-2">
                                    <label for="down_payment" class="form-label"><strong>Down Payment Amount *</strong></label>
                                    <input type="number" class="form-control" name="down_payment" id="down_payment" 
                                           min="0" step="0.01" placeholder="Enter down payment amount">
                                    <small class="form-text text-muted">Minimum down payment: ₱<?= number_format($grand_total * 0.2, 2) ?></small>
                                    <div class="invalid-feedback" id="down_payment_error"></div>
                                </div>
                                
                                <div class="mt-2">
                                    <label for="monthly_payment" class="form-label"><strong>Monthly Payment</strong></label>
                                    <input type="text" class="form-control" name="monthly_payment" id="monthly_payment" 
                                           readonly placeholder="Will be calculated automatically">
                                </div>
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
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" required>
                                    <label class="form-check-label" for="terms_accepted">
                                        I agree to the <a href="#" data-toggle="modal" data-target="#termsModal" class="terms-link">Terms and Conditions</a> <span class="text-danger">*</span>
                                    </label>
                                    <div class="invalid-feedback" id="terms_accepted_error">Please agree to the terms and conditions before proceeding.</div>
                                </div>
                            </div>
                            
                            <div class="form-group text-right">
                                <button class="btn btn-flat btn-primary" type="submit" id="place_order_btn" disabled>
                                    <i class="fa fa-shopping-cart"></i> 
                                    <?php if(isset($has_motorcycles) && $has_motorcycles): ?>
                                        <?php echo isset($application_completed) && $application_completed ? 'Continue to Order' : 'Proceed to Credit Application'; ?>
                                    <?php else: ?>
                                        Advance Order
                                    <?php endif; ?>
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
                    <li>Installment: Minimum 20% down payment required</li>
                    <li>Monthly payments are due on the same date each month</li>
                    <li>Late payment fees may apply for overdue installments</li>
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

<!-- Motorcentral Credit Application Modal (Embedded iframe) -->
<div class="modal fade" id="creditAppModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Motorcentral Credit Application</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="creditAppFrame" src="https://form.jotform.com/242488642552463" style="border:0;width:100%;height:100%;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="creditAppCompletedBtn" class="btn btn-success">I've Completed the Application</button>
            </div>
        </div>
    </div>
    
</div>

<style>
    /* Terms and Conditions Link Styling */
    .terms-link {
        color: #dc3545;
        text-decoration: underline;
        font-weight: 500;
    }
    
    .terms-link:hover {
        color: #a71e2a;
        text-decoration: none;
    }
    
    /* Button Disabled State */
    #place_order_btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    #place_order_btn:disabled:hover {
        background-color: #6c757d;
        border-color: #6c757d;
        transform: none;
    }
    
    /* Payment Method Section Styling */
    .payment-method-section {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        background-color: #ffffff;
    }
    
    .installment-requirements {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 12px;
        margin: 10px 0;
    }
    
    .installment-requirements .fa-info-circle {
        color: #856404;
    }
    
    .installment-requirements h6 {
        color: #856404;
        margin-bottom: 8px;
    }
    
    .installment-requirements ul {
        margin-bottom: 0;
        padding-left: 20px;
    }
    
    .installment-requirements li {
        color: #856404;
        font-size: 13px;
        margin-bottom: 4px;
    }
    
    /* Red and Black Theme for Checkout */
    .btn-primary {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
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
</style>

<script>
    $(function(){
        // Payment method change handler
        $('#payment_method').change(function(){
            var paymentMethod = $(this).val();
            
            // Hide all sections first
            $('#full_payment_section').hide();
            $('#installment_section').hide();
            
            // Clear validation states
            $('#payment_type, #installment_months, #down_payment').removeClass('is-invalid is-valid');
            $('#payment_type_error, #installment_months_error, #down_payment_error').text('');
            
            // Show relevant section
            if(paymentMethod === 'full_payment') {
                $('#full_payment_section').show();
                $('#payment_type').prop('required', true);
                $('#installment_months, #down_payment').prop('required', false);
            } else if(paymentMethod === 'installment') {
                if(!hasMotorcycles){
                    // Prevent installment for parts-only carts
                    $(this).val('full_payment').trigger('change');
                    alert_toast('Installment is available only for motorcycle purchases.', 'info');
                    return;
                }
                $('#installment_section').show();
                $('#installment_months, #down_payment').prop('required', true);
                $('#payment_type').prop('required', false);
            }
        });
        
        // Installment calculation
        $('#installment_months, #down_payment').on('input change', function(){
            calculateInstallment();
        });
        
        function calculateInstallment() {
            var months = parseInt($('#installment_months').val());
            var downPayment = parseFloat($('#down_payment').val()) || 0;
            var totalAmount = <?= $grand_total ?>;
            var minimumDown = totalAmount * 0.2;
            
            if(months > 0 && downPayment >= minimumDown) {
                var remainingAmount = totalAmount - downPayment;
                var monthlyPayment = remainingAmount / months;
                $('#monthly_payment').val('₱' + monthlyPayment.toFixed(2));
            } else {
                $('#monthly_payment').val('');
            }
        }
        
        // Form validation
        function validateForm() {
            var isValid = true;
            var paymentMethod = $('#payment_method').val();
            
            // Clear previous validation
            $('.form-control, .form-check-input').removeClass('is-invalid is-valid');
            $('.invalid-feedback').text('');
            
            // Validate payment method
            if(!paymentMethod) {
                $('#payment_method').addClass('is-invalid');
                $('#payment_method_error').text('Please select a payment method.').show();
                isValid = false;
            } else {
                $('#payment_method').addClass('is-valid');
                $('#payment_method_error').hide();
            }
            
            // Validate full payment section
            if(paymentMethod === 'full_payment') {
                var paymentType = $('#payment_type').val();
                if(!paymentType) {
                    $('#payment_type').addClass('is-invalid');
                    $('#payment_type_error').text('Please select a payment type (Cash or Card).').show();
                    isValid = false;
                } else {
                    $('#payment_type').addClass('is-valid');
                    $('#payment_type_error').hide();
                }
            }
            
            // Validate installment section (only when motorcycles are present)
            if(paymentMethod === 'installment' && hasMotorcycles) {
                var months = $('#installment_months').val();
                var downPayment = parseFloat($('#down_payment').val()) || 0;
                var totalAmount = <?= $grand_total ?>;
                var minimumDown = totalAmount * 0.2;
                
                if(!months) {
                    $('#installment_months').addClass('is-invalid');
                    $('#installment_months_error').text('Please select an installment period.').show();
                    isValid = false;
                } else {
                    $('#installment_months').addClass('is-valid');
                    $('#installment_months_error').hide();
                }
                
                if(!downPayment || downPayment < minimumDown) {
                    $('#down_payment').addClass('is-invalid');
                    $('#down_payment_error').text('Down payment must be at least ₱' + minimumDown.toFixed(2) + ' (20% of total amount)').show();
                    isValid = false;
                } else {
                    $('#down_payment').addClass('is-valid');
                    $('#down_payment_error').hide();
                }
            }
            
            // Validate terms acceptance
            if(!$('#terms_accepted').is(':checked')) {
                $('#terms_accepted').addClass('is-invalid');
                $('#terms_accepted_error').text('Please agree to the terms and conditions before proceeding.').show();
                isValid = false;
            } else {
                $('#terms_accepted').addClass('is-valid');
                $('#terms_accepted_error').text('').hide();
            }
            
            return isValid;
        }
        
        // Enhanced conditional checkout logic
        var hasMotorcycles = <?php echo isset($has_motorcycles) && $has_motorcycles ? 'true' : 'false'; ?>;
        var hasPartsOils = <?php echo isset($has_parts_oils) && $has_parts_oils ? 'true' : 'false'; ?>;
        var applicationCompleted = <?php echo isset($application_completed) && $application_completed ? 'true' : 'false'; ?>;
        
        // Terms and Conditions checkbox handler
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
            
            // Update button text based on cart contents and application status
            updateButtonText();
        }).trigger('change');
        
        // Function to update button text based on current state
        function updateButtonText() {
            if(hasMotorcycles) {
                if(!applicationCompleted) {
                    $('#place_order_btn').html('<i class="fa fa-shopping-cart"></i> Proceed to Credit Application');
                } else {
                    $('#place_order_btn').html('<i class="fa fa-shopping-cart"></i> Continue to Order');
                }
            } else if(hasPartsOils) {
                $('#place_order_btn').html('<i class="fa fa-shopping-cart"></i> Advance Order');
            } else {
                $('#place_order_btn').html('<i class="fa fa-shopping-cart"></i> Place Order');
            }
        }

        // Submit order
        $('#place_order').submit(function(e){
            e.preventDefault();
            
            // Clear previous validation errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('').hide();
            
            // Validate Terms and Conditions first
            if(!$('#terms_accepted').is(':checked')) {
                $('#terms_accepted').addClass('is-invalid');
                $('#terms_accepted_error').text('Please agree to the terms and conditions before proceeding.').show();
                alert_toast('Please agree to the terms and conditions before proceeding.', 'warning');
                return false;
            }
            
            // Validate form before submission
            if(!validateForm()) {
                alert_toast('Please fill in all required fields correctly.', 'warning');
                return false;
            }
            
            // Redirect to credit application for motorcycle orders if not completed
            console.log('Debug - hasMotorcycles:', hasMotorcycles, 'applicationCompleted:', applicationCompleted);
            if(hasMotorcycles && !applicationCompleted) {
                // Prefer embedded modal; fallback to new tab if modal fails
                try {
                    $('#creditAppModal').modal('show');
                } catch(e) {
                    window.open("https://form.jotform.com/242488642552463", '_blank');
                }
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
                    // Try to parse responseText in case server returned JSON but parsing failed due to extra output
                    try{
                        if(err && err.responseText){
                            var parsed = null;
                            try{ parsed = JSON.parse(err.responseText); }catch(e){ parsed = null; }
                            if(!parsed){
                                var txt = err.responseText;
                                var s = txt.indexOf('{');
                                var e = txt.lastIndexOf('}');
                                if(s !== -1 && e !== -1 && e > s){
                                    var sub = txt.substring(s, e+1);
                                    try{ parsed = JSON.parse(sub); }catch(e2){ parsed = null; }
                                }
                            }
                            if(parsed){
                                // Call the success handler path by simulating resp
                                end_loader();
                                $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Advance Order');
                                if(parsed.status == 'success'){
                                    // Show success message before redirecting
                                    uni_modal('Order Placed Successfully','./success_msg.php');
                                    if(parsed.ref_code){
                                        alert_toast('Reference Code: '+parsed.ref_code,'success');
                                    }
                                    setTimeout(function(){ window.location.replace('./?p=my_orders'); }, 3000);
                                    return;
                                } else if(parsed.status == 'failed' && parsed.msg){
                                    var el = $('<div>');
                                    el.addClass("alert alert-danger err-msg").text(parsed.msg);
                                    _this.prepend(el);
                                    el.show('slow');
                                    $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                                    return;
                                }
                            }
                        }
                    }catch(parseErr){ console.log('Response parse failed', parseErr); }
                    alert_toast("An error occurred",'error');
                    end_loader();
                    // Reset button text based on context
                    $('#place_order_btn').prop('disabled', false);
                    updateButtonText();
                },
                success:function(resp){
                    // Always clear loader first
                    end_loader();
                    $('#place_order_btn').prop('disabled', false);
                    updateButtonText();
                    
                    if(typeof resp =='object' && resp.status == 'success'){
                        // Use existing uni_modal success template for consistency
                        uni_modal('Order Placed Successfully','./success_msg.php');
                        // Also show ref code via toast for quick copy
                        if(resp.ref_code){
                            alert_toast('Reference Code: '+resp.ref_code,'success');
                        }
                        // Increase delay to allow user to see the success message
                        setTimeout(function(){ location.replace('./?p=my_orders'); }, 3000);
                    }else if(resp.status == 'failed' && !!resp.msg){
                        if(resp.application_required){
                            // Show application form modal
                            showCreditApplicationModal(resp.application_url, resp.msg);
                        } else {
                            var el = $('<div>');
                            el.addClass("alert alert-danger err-msg").text(resp.msg);
                            _this.prepend(el);
                            el.show('slow');
                            $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                        }
                    }else{
                        alert_toast("An error occurred while processing your order.", 'error');
                        console.log(resp);
                    }
                }
            });
        });
    });

    // Embedded modal completion button
    $(document).on('click', '#creditAppCompletedBtn', function(){
        markApplicationCompleted();
    });

    // Function to show credit application modal
    function showCreditApplicationModal(applicationUrl, message) {
        Swal.fire({
            title: 'Motorcentral Credit Application Required',
            html: `
                <div class="text-center">
                    <i class="fa fa-file-alt text-warning" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Credit Application Required</h4>
                    <p class="text-muted">${message}</p>
                    <div class="alert alert-info">
                        <strong>Required Documents for Motorcycle Purchase:</strong><br>
                        • 2 Valid IDs with 3 signatures (front & back)<br>
                        • Proof of billing (Meralco, Water, or Internet bill)<br>
                        • Proof of income (Payslip, COE, or Bank Statement)<br>
                        • Sketch of your house location<br>
                        <small class="text-muted">Note: This application is required for motorcycle purchases only</small>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Proceed to Credit Application',
            cancelButtonText: 'Cancel Order',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Open application form in new tab
                window.open(applicationUrl, '_blank');
                
                // Show completion confirmation dialog
                Swal.fire({
                    title: 'Credit Application Form Opened',
                    html: `
                        <div class="text-center">
                            <i class="fa fa-external-link-alt text-info" style="font-size: 3rem;"></i>
                            <p class="mt-3">Please complete the Motorcentral Credit Application form in the new tab.</p>
                            <p class="text-muted">After completing the form, return here and click "I've Completed the Application" to proceed with your motorcycle order.</p>
                            <div class="alert alert-warning">
                                <small><strong>Important:</strong> You must complete the credit application before proceeding with your motorcycle purchase.</small>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'I\'ve Completed the Application',
                    cancelButtonText: 'I\'ll Complete It Later',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mark application as completed and retry order
                        markApplicationCompleted();
                    }
                });
            } else {
                // User cancelled, redirect to cart
                location.replace('./?p=cart');
            }
        });
    }

    // Function to mark application as completed
    function markApplicationCompleted() {
        start_loader();
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=mark_credit_application_completed',
            method: 'POST',
            data: { customer_id: '<?= $_settings->userdata('id') ?>' },
            dataType: 'json',
            success: function(resp) {
                end_loader();
                if(resp.status == 'success') {
                    Swal.fire({
                        title: 'Application Marked as Completed!',
                        text: 'You can now proceed with your motorcycle order.',
                        icon: 'success',
                        confirmButtonText: 'Place Order Now'
                    }).then(() => {
                        // Refresh the page to update application status
                        location.reload();
                    });
                } else {
                    alert_toast('Failed to update application status. Please try again.', 'error');
                }
            },
            error: function() {
                end_loader();
                alert_toast('An error occurred. Please try again.', 'error');
            }
        });
    }
</script>