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
                        $total = 0;
                        $cart = $conn->query("SELECT c.*,p.name, p.price, p.image_path,b.name as brand, cc.category FROM `cart_list` c inner join product_list p on c.product_id = p.id inner join brand_list b on p.brand_id = b.id inner join categories cc on p.category_id = cc.id where c.client_id = '{$_settings->userdata('id')}' order by p.name asc");
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
                                <small class="text-muted">Qty: <?= $row['quantity'] ?> x ₱<?= number_format($row['price'],2) ?></small>
                            </div>
                            <div class="col-auto">
                                <strong>₱<?= number_format($row['quantity'] * $row['price'],2) ?></strong>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        
                        <?php if($cart->num_rows <= 0): ?>
                        <div class="text-center py-3">
                            <small class="text-muted">No items in cart</small>
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center w-100 border-top pt-3 mt-3">
                            <h5 class="mb-0">Total Amount:</h5>
                            <h4 class="mb-0 text-primary">₱<?= number_format($total,2) ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Checkout Form -->
            <div class="col-md-6">
                <div class="card card-outline card-dark shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title">Checkout Information</h4>
                    </div>
                    <div class="card-body">
                        <form action="" id="place_order">
                            <div class="form-group">
                                <label for="delivery_address" class="control-label">Delivery Address <span class="text-danger">*</span></label>
                                <textarea name="delivery_address" id="delivery_address" class="form-control form-control-sm rounded-0" rows="4" required><?= $_settings->userdata('address') ?></textarea>
                                <small class="text-muted">Please provide your complete delivery address</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_number" class="control-label">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control form-control-sm rounded-0" value="<?= $_settings->userdata('contact') ?>" readonly>
                                <small class="text-muted">This is your registered contact number</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_method" class="control-label">Payment Method</label>
                                <select name="payment_method" id="payment_method" class="form-control form-control-sm rounded-0">
                                    <option value="cod">Cash on Delivery (COD)</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                </select>
                                <small class="text-muted">Select your preferred payment method</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes" class="control-label">Additional Notes</label>
                                <textarea name="notes" id="notes" class="form-control form-control-sm rounded-0" rows="3" placeholder="Any special instructions or notes for delivery..."></textarea>
                            </div>
                            
                            <div class="form-group text-right">
                                <button class="btn btn-flat btn-primary" type="submit" id="place_order_btn">
                                    <i class="fa fa-shopping-cart"></i> Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        // Validate form before submission
        $('#place_order').submit(function(e){
            e.preventDefault();
            
            var delivery_address = $('#delivery_address').val().trim();
            if(delivery_address.length < 10){
                alert_toast('Please provide a complete delivery address (at least 10 characters)', 'warning');
                $('#delivery_address').focus();
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
                    $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Place Order');
                },
                success:function(resp){
                    if(typeof resp =='object' && resp.status == 'success'){
                        // Show success message with order details
                        Swal.fire({
                            title: 'Order Placed Successfully!',
                            html: `
                                <div class="text-center">
                                    <i class="fa fa-check-circle text-success" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3">Reference Code: <strong>${resp.ref_code}</strong></h4>
                                    <p class="text-muted">Please save this reference code for tracking your order.</p>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'View My Orders',
                            showCancelButton: true,
                            cancelButtonText: 'Continue Shopping'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.replace('./?p=my_orders');
                            } else {
                                location.replace('./?p=products');
                            }
                        });
                    }else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>');
                        el.addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                        end_loader();
                        $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Place Order');
                    }else{
                        alert_toast("An error occurred",'error');
                        end_loader();
                        $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Place Order');
                        console.log(resp);
                    }
                }
            });
        });
        
        // Auto-resize textarea
        $('#delivery_address').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
</script>