<?php 
require_once('./inc/sess_auth.php');
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
                        
                        $motorcycle_items = $conn->query("SELECT COUNT(*) as count FROM cart_list c 
                                                        INNER JOIN product_list p ON c.product_id = p.id 
                                                        INNER JOIN categories cat ON p.category_id = cat.id 
                                                        WHERE c.client_id = '{$customer_id}' 
                                                        AND (cat.category LIKE '%motorcycle%' OR cat.category LIKE '%bike%' OR p.name LIKE '%motorcycle%' OR p.name LIKE '%bike%')");
                        $has_motorcycles = $motorcycle_items->fetch_assoc()['count'] > 0;
                        
                        if($has_motorcycles):
                            $application_status = $conn->query("SELECT credit_application_completed FROM client_list WHERE id = '{$customer_id}'")->fetch_assoc();
                            $application_completed = $application_status && $application_status['credit_application_completed'] == 1;
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
                        $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$customer_id}' AND c.product_id > 0 AND p.id > 0 AND p.delete_flag = 0 AND p.status = 1 order by p.name asc");
                        
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
                            <small class="text-muted">No items in cart</small>
                        </div>
                        <?php } ?>
                        
                        <?php 
                        // Get add-ons data from URL parameters
                        $addons = isset($_GET['addons']) ? $_GET['addons'] : '';
                        $addon_details = isset($_GET['addon_details']) ? json_decode($_GET['addon_details'], true) : [];
                        $addons_total = isset($_GET['addons_total']) ? floatval($_GET['addons_total']) : 0;
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
                            <input type="hidden" name="addons" value="<?= htmlspecialchars($addons) ?>">
                            <input type="hidden" name="addons_total" value="<?= $addons_total ?>">
                            
                            <!-- Payment Method Selection -->
                            <div class="form-group">
                                <label for="payment_method" class="form-label"><strong>Payment Method *</strong></label>
                                <select class="form-control" name="payment_method" id="payment_method" required>
                                    <option value="">-- Select Payment Method --</option>
                                    <option value="full_payment">Full Payment (Cash/Card)</option>
                                    <option value="installment">Installment Plan</option>
                                </select>
                                <div class="invalid-feedback" id="payment_method_error"></div>
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
                            
                            <!-- Pickup-based orders: Delivery address removed. Show client info summary instead. -->
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
                            
                            <!-- Contact Information removed; shown in Customer Information on Cart page -->
                            
                            <!-- Terms and Conditions -->
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" required>
                                    <label class="form-check-label" for="terms_accepted">
                                        I agree to the <a href="#" data-toggle="modal" data-target="#termsModal">Terms and Conditions</a> *
                                    </label>
                                    <div class="invalid-feedback" id="terms_accepted_error"></div>
                                </div>
                            </div>
                            
                            <div class="form-group text-right">
                                <button class="btn btn-flat btn-primary" type="submit" id="place_order_btn">
                                    <i class="fa fa-shopping-cart"></i> Advance Order
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
                $('#payment_method_error').text('Please select a payment method.');
                isValid = false;
            } else {
                $('#payment_method').addClass('is-valid');
            }
            
            // Validate full payment section
            if(paymentMethod === 'full_payment') {
                var paymentType = $('#payment_type').val();
                if(!paymentType) {
                    $('#payment_type').addClass('is-invalid');
                    $('#payment_type_error').text('Please select a payment type.');
                    isValid = false;
                } else {
                    $('#payment_type').addClass('is-valid');
                }
            }
            
            // Validate installment section
            if(paymentMethod === 'installment') {
                var months = $('#installment_months').val();
                var downPayment = parseFloat($('#down_payment').val()) || 0;
                var totalAmount = <?= $grand_total ?>;
                var minimumDown = totalAmount * 0.2;
                
                if(!months) {
                    $('#installment_months').addClass('is-invalid');
                    $('#installment_months_error').text('Please select installment period.');
                    isValid = false;
                } else {
                    $('#installment_months').addClass('is-valid');
                }
                
                if(!downPayment || downPayment < minimumDown) {
                    $('#down_payment').addClass('is-invalid');
                    $('#down_payment_error').text('Down payment must be at least ₱' + minimumDown.toFixed(2));
                    isValid = false;
                } else {
                    $('#down_payment').addClass('is-valid');
                }
            }
            
            // Delivery address validation removed (pickup-based orders)
            
            // Contact number validation removed (already captured in customer profile)
            
            // Validate terms acceptance
            if(!$('#terms_accepted').is(':checked')) {
                $('#terms_accepted').addClass('is-invalid');
                $('#terms_accepted_error').text('Please accept the terms and conditions.');
                isValid = false;
            } else {
                $('#terms_accepted').addClass('is-valid');
            }
            
            return isValid;
        }
        
        // Submit order
        $('#place_order').submit(function(e){
            e.preventDefault();
            
            // Validate form before submission
            if(!validateForm()) {
                alert_toast('Please fill in all required fields correctly.', 'warning');
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
                                    window.location.replace('./?p=my_orders');
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
                    $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Advance Order');
                },
                success:function(resp){
                    // Always clear loader first
                    end_loader();
                    $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Advance Order');
                    
                    if(typeof resp =='object' && resp.status == 'success'){
                        // Use existing uni_modal success template for consistency
                        uni_modal('Order Placed Successfully','success_msg.php');
                        // Also show ref code via toast for quick copy
                        if(resp.ref_code){
                            alert_toast('Reference Code: '+resp.ref_code,'success');
                        }
                        setTimeout(function(){ location.replace('./?p=my_orders'); }, 1200);
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

    // Function to show credit application modal
    function showCreditApplicationModal(applicationUrl, message) {
        Swal.fire({
            title: 'Credit Application Required',
            html: `
                <div class="text-center">
                    <i class="fa fa-file-alt text-warning" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Motorcentral Credit Application</h4>
                    <p class="text-muted">${message}</p>
                    <div class="alert alert-info">
                        <strong>Required for Motorcycle Orders:</strong><br>
                        • 2 Valid IDs with 3 signatures (front & back)<br>
                        • Proof of billing (Meralco, Water, or Internet bill)<br>
                        • Proof of income (Payslip, COE, or Bank Statement)<br>
                        • Sketch of your house location
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Fill Application Form',
            cancelButtonText: 'Cancel Order',
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                // Open application form in new tab
                window.open(applicationUrl, '_blank');
                
                // Show completion confirmation dialog
                Swal.fire({
                    title: 'Application Form Opened',
                    html: `
                        <div class="text-center">
                            <i class="fa fa-external-link-alt text-info" style="font-size: 3rem;"></i>
                            <p class="mt-3">Please complete the Motorcentral Credit Application form in the new tab.</p>
                            <p class="text-muted">After completing the form, return here and click "I've Completed the Application" to proceed with your order.</p>
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
                        // Retry placing the order
                        $('#place_order').submit();
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
<style>
/* Add-ons Styling */
.addons-section {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.addon-category {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.addon-category h6 {
    color: #dc3545;
    font-weight: bold;
    border-bottom: 2px solid #dc3545;
    padding-bottom: 5px;
}

.form-check {
    padding: 10px;
    border: 1px solid #e9ecef;
    border-radius: 5px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.form-check:hover {
    border-color: #dc3545;
    background: #fff5f5;
}

.form-check-input:checked {
    background-color: #dc3545;
    border-color: #dc3545;
}

.form-check-label {
    cursor: pointer;
    width: 100%;
}

.addons-total {
    background: #dc3545;
    color: white;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.addons-total h5 {
    color: white;
    font-weight: bold;
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
</style>