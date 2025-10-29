<?php
if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

$client_id = $_settings->userdata('id');

// Get wishlist items
$wishlist = $conn->query("SELECT w.*, p.name, p.price, p.image_path, p.description, b.name as brand, c.category 
                         FROM wishlist w 
                         INNER JOIN product_list p ON w.product_id = p.id 
                         INNER JOIN brand_list b ON p.brand_id = b.id 
                         INNER JOIN categories c ON p.category_id = c.id 
                         WHERE w.client_id = '{$client_id}' AND p.delete_flag = 0 AND p.status = 1 
                         ORDER BY w.date_added DESC");
?>

<div class="content py-5 mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>My Wishlist</b></h4>
                        <div class="card-tools">
                            <a href="./?p=products" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> Browse More Products
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($wishlist->num_rows > 0): ?>
                        <div class="row">
                            <?php while($item = $wishlist->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="position-relative">
                                        <img src="<?= validate_image($item['image_path']) ?>" class="card-img-top" alt="<?= $item['name'] ?>" style="height: 200px; object-fit: cover;">
                                        <div class="position-absolute top-0 right-0 p-2">
                                            <button class="btn btn-sm btn-danger remove-wishlist" data-id="<?= $item['id'] ?>" title="Remove from wishlist">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                        <div class="position-absolute bottom-0 left-0 w-100 p-2" style="background: rgba(0,0,0,0.7);">
                                            <h5 class="text-white mb-0">₱<?= number_format($item['price'], 2) ?></h5>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title"><?= $item['name'] ?></h6>
                                        <p class="text-muted small mb-2">
                                            <i class="fa fa-tag"></i> <?= $item['brand'] ?> | <?= $item['category'] ?>
                                        </p>
                                        <p class="card-text small"><?= strip_tags(html_entity_decode($item['description'])) ?></p>
                                        
                                        <?php
                                        // Check stock availability
                                        $stocks = $conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$item['product_id']}' AND type = 1")->fetch_assoc()['total_stock'];
                                        $out = $conn->query("SELECT SUM(oi.quantity) as total_out FROM order_items oi 
                                                          INNER JOIN order_list ol ON oi.order_id = ol.id 
                                                          WHERE oi.product_id = '{$item['product_id']}' AND ol.status != 5")->fetch_assoc()['total_out'];
                                        
                                        $stocks = $stocks > 0 ? $stocks : 0;
                                        $out = $out > 0 ? $out : 0;
                                        $available = $stocks - $out;
                                        ?>
                                        
                                        <div class="mb-3">
                                            <?php if($available > 0): ?>
                                            <span class="badge badge-success">
                                                <i class="fa fa-check"></i> In Stock (<?= $available ?> available)
                                            </span>
                                            <?php else: ?>
                                            <span class="badge badge-danger">
                                                <i class="fa fa-times"></i> Out of Stock
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Added: <?= date('M d, Y', strtotime($item['date_added'])) ?>
                                            </small>
                                            <div>
                                                <?php if($available > 0): ?>
                                                <button class="btn btn-sm btn-primary add-to-cart" data-id="<?= $item['product_id'] ?>">
                                                    <i class="fa fa-shopping-cart"></i> Add to Cart
                                                </button>
                                                <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="fa fa-bell"></i> Notify When Available
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa fa-heart text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">Your wishlist is empty</h4>
                            <p class="text-muted">Start adding products you're interested in to your wishlist.</p>
                            <a href="./?p=products" class="btn btn-primary">
                                <i class="fa fa-shopping-cart"></i> Browse Products
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Remove from wishlist
    $('.remove-wishlist').click(function(){
        var wishlist_id = $(this).data('id');
        
        if(confirm('Are you sure you want to remove this item from your wishlist?')){
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=remove_from_wishlist",
                method: "POST",
                data: {wishlist_id: wishlist_id},
                dataType: "json",
                success: function(resp){
                    if(resp.status == 'success'){
                        alert_toast(resp.msg, 'success');
                        setTimeout(function(){
                            location.reload();
                        }, 1000);
                    } else {
                        alert_toast(resp.msg, 'error');
                    }
                }
            });
        }
    });
    
    // Add to cart
    $('.add-to-cart').click(function(){
        var product_id = $(this).data('id');
        
        // Show quantity selector modal
        uni_modal("Add to Cart", "add_to_cart_modal.php?product_id=" + product_id, 'mid-large');
    });
});
</script>
