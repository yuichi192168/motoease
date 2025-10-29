<?php 
require_once('./config.php');
// Hide the modal footer (Save button) for this view since there is no form to submit here
echo "<style>#uni_modal .modal-footer{display:none}</style>";
if(isset($_GET['id'])){
    // cast to integers to avoid injection and unexpected values
    $order_id = intval($_GET['id']);
    $client_id = intval($_settings->userdata('id'));
    $qry = $conn->query("SELECT * FROM `order_list` WHERE id = '{$order_id}' AND client_id = '{$client_id}'");
    if($qry && $qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k))
            $$k = $v;
        }
    }else{
        // When loaded inside a modal, avoid redirecting the whole page. Show a friendly message instead.
        ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-muted">Access Denied</h5>
                <p class="text-muted">You are not allowed to view this order or it does not exist.</p>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php
        return;
    }
}else{
    ?>
    <div class="card">
        <div class="card-body text-center">
            <h5 class="text-muted">Invalid Request</h5>
            <p class="text-muted">No order ID provided.</p>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
    <?php
    return;
}
?>
<style>
    .prod-cart-img{
        width:7em;
        height:7em;
        object-fit:scale-down;
        object-position: center center;
    }
</style>
<div class="card card-outline card-dark shadow rounded-0">
    <div class="card-body">
        <div class="d-flex justify-content-end mb-2">
            <!-- <button type="button" class="btn btn-secondary btn-sm" onclick="$('#uni_modal').modal('hide')">
                <i class="fa fa-arrow-left"></i> Close
            </button> -->
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <label for="" class="text-muted">Reference Code</label>
                    <div class="ml-3"><b><?= isset($ref_code) ? $ref_code : "N/A" ?></b></div>
                </div>
                <div class="col-md-6">
                    <label for="" class="text-muted">Date Created</label>
                    <div class="ml-3"><b><?= isset($date_created) ? date("M d, Y h:i A", strtotime($date_created)) : "N/A" ?></b></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="" class="text-muted">Status</label>
                    <div class="ml-3">
                        <?php if(isset($status)): ?>
                            <?php if($status == 0): ?>
                                <span class="badge badge-secondary px-3 rounded-pill">Pending</span>
                            <?php elseif($status == 1): ?>
                                <span class="badge badge-primary px-3 rounded-pill">Ready for pickup</span>
                            <?php elseif($status == 2): ?>
                                <span class="badge badge-success px-3 rounded-pill">For Delivery</span>
                            <?php elseif($status == 3): ?>
                                <span class="badge badge-warning px-3 rounded-pill">On the Way</span>
                            <?php elseif($status == 4): ?>
                                <span class="badge badge-default bg-gradient-teal px-3 rounded-pill">Delivered</span>
                            <?php elseif($status == 6): ?>
                                <span class="badge badge-success px-3 rounded-pill">Claimed</span>
                            <?php else: ?>
                                <span class="badge badge-danger px-3 rounded-pill">Cancelled</span>
                            <?php endif; ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="" class="text-muted">Total Amount</label>
                    <div class="ml-3"><b>₱<?= isset($total_amount) ? number_format($total_amount,2) : "0.00" ?></b></div>
                </div>
            </div>
            <div class="clear-fix my-2"></div>
            <div class="row">
                <div class="col-12">
                <div class="w-100" id="order-list">
                        <?php 
                        $total = 0;
                        if(isset($id)):
                        // Use LEFT JOINs so order items still show even if the product/brand/category was removed later.
                        // Preserve the price at time of order by using o.price as unit_price.
                        // use the current product price (p.price) as unit_price because order_items table does not store price
                        $order_item = $conn->query("SELECT o.*, p.name, p.price as unit_price, p.image_path, b.name as brand, cc.category FROM `order_items` o 
                            LEFT JOIN product_list p on o.product_id = p.id 
                            LEFT JOIN brand_list b on p.brand_id = b.id 
                            LEFT JOIN categories cc on p.category_id = cc.id 
                            WHERE o.order_id = '{$id}' ORDER BY p.name ASC");
                        while($row = $order_item->fetch_assoc()):
                            $unit_price = isset($row['unit_price']) ? $row['unit_price'] : 0;
                            $total += ($row['quantity'] * $unit_price);
                        ?>
                        <div class="d-flex align-items-center w-100 border cart-item" data-id="<?= $row['id'] ?>">
                            <div class="col-auto flex-grow-1 flex-shrink-1 px-1 py-1">
                                <div class="d-flex align-items-center w-100 ">
                                    <div class="col-auto">
                                        <img src="<?= validate_image($row['image_path']) ?>" alt="Product Image" class="img-thumbnail prod-cart-img">
                                    </div>
                                    <div class="col-auto flex-grow-1 flex-shrink-1">
                                        <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>" class="h4 text-muted" target="_blank">
                                            <p class="text-truncate-1 m-0"><?= $row['name'] ?></p>
                                        </a>
                                        <small><?= $row['brand'] ?></small><br>
                                        <small><?= $row['category'] ?></small><br>
                                        <div class="d-flex align-items-center w-100 mb-1">
                                            <span><?= number_format($row['quantity']) ?></span>
                                            <span class="ml-2">X ₱<?= number_format($unit_price,2) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto text-right">
                                <h3><b>₱<?= number_format($row['quantity'] * $unit_price,2) ?></b></h3>
                            </div>
                        </div>
                        <?php 
                            endwhile; 
                            endif;
                        ?>
                        <?php if(isset($order_item) && $order_item->num_rows <= 0): ?>
                        <div class="d-flex align-items-center w-100 border justify-content-center">
                            <div class="col-12 flex-grow-1 flex-shrink-1 px-1 py-1">
                                <small class="text-muted">No Data</small>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex align-items-center w-100 border">
                            <div class="col-auto flex-grow-1 flex-shrink-1 px-1 py-1">
                                    <h3 class="text-center">TOTAL</h3>
                            </div>
                            <div class="col-auto text-right">
                                <h3><b>₱<?= number_format($total,2) ?></b></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear-fix my-2"></div>
            
            <!-- Order Status Information -->
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5><i class="fa fa-info-circle"></i> Order Status Information</h5>
                        <?php if(isset($status)): ?>
                            <?php if($status == 0): ?>
                                <p class="mb-0">Your order is currently <strong>pending</strong>. We will process it shortly and notify you when it's ready for pickup or delivery.</p>
                            <?php elseif($status == 1): ?>
                                <p class="mb-0">Your order is <strong>ready for pickup</strong>! Please visit our store to claim your items. Bring a valid ID and your reference code: <strong><?= $ref_code ?></strong></p>
                            <?php elseif($status == 2): ?>
                                <p class="mb-0">Your order is <strong>being prepared for delivery</strong>. We will contact you soon with delivery details.</p>
                            <?php elseif($status == 3): ?>
                                <p class="mb-0">Your order is <strong>on the way</strong>! Our delivery team will contact you shortly.</p>
                            <?php elseif($status == 4): ?>
                                <p class="mb-0">Your order has been <strong>delivered</strong>. Thank you for your purchase!</p>
                            <?php elseif($status == 6): ?>
                                <p class="mb-0">Your order has been <strong>claimed</strong>. Thank you for your purchase!</p>
                            <?php else: ?>
                                <p class="mb-0">Your order has been <strong>cancelled</strong>. If you have any questions, please contact our customer service.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>