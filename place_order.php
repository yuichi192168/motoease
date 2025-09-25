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
                        
                        <?php if($cart->num_rows <= 0): ?>
                        <div class="text-center py-3">
                            <small class="text-muted">No items in cart</small>
                        </div>
                        <?php endif; ?>
                        
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
        // Submit order
        $('#place_order').submit(function(e){
            e.preventDefault();
            
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
                    // Always clear loader first
                    end_loader();
                    $('#place_order_btn').prop('disabled', false).html('<i class="fa fa-shopping-cart"></i> Place Order');
                    
                    if(typeof resp =='object' && resp.status == 'success'){
                        // Show success message with order details
                        Swal.fire({
                            title: 'Order Placed Successfully!',
                            html: `
                                <div class="text-center">
                                    <i class="fa fa-check-circle text-success" style="font-size: 4rem;"></i>
                                    <h4 class="mt-3">Reference Code: <strong>${resp.ref_code}</strong></h4>
                                    <p class="text-muted">Please save this reference code for tracking your order.</p>
                                    <p class="text-muted">Your motorcycle parts and accessories will be included in your order.</p>
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
                    }else{
                        alert_toast("An error occurred while processing your order.", 'error');
                        console.log(resp);
                    }
                }
            });
        });
    });
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