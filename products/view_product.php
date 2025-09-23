<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT p.*, b.name as brand,c.category from `product_list` p inner join brand_list b on p.brand_id = b.id inner join categories c on p.category_id = c.id where p.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
        $stocks = $conn->query("SELECT SUM(quantity) FROM stock_list where product_id = '$id'")->fetch_array()[0];
        $out = $conn->query("SELECT SUM(quantity) FROM order_items where product_id = '{$id}' and order_id in (SELECT id FROM order_list where `status` != 5) ")->fetch_array()[0];
        $stocks = $stocks > 0 ? $stocks : 0;
        $out = $out > 0 ? $out : 0;
        $available = $stocks - $out;
    }else{
    echo "<script> alert('Unknown Product ID!'); location.replace('./?page=products');</script>";

    }
}else{
    echo "<script> alert('Product ID is required!'); location.replace('./?page=products');</script>";
}
?>
<style>
    .product-img{
        width:20em;
        height:17em;
        object-fit:scale-down;
        object-position:center center;
    }
    .stock-status {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.9em;
        font-weight: bold;
    }
    .stock-available {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .stock-low {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    .stock-out {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>
<div class="content py-5 mt-3">
    <div class="container">
        <div class="card card-outline rounded-0 card-primary shadow">
            <div class="card-header">
                <h4 class="card-title">Product Details</h4>
                <div class="card-tools">
                    <?php if($available > 0): ?>
                        <button class="btn btn-default border btn-sm btn-flat" type="button" id="add_to_cart">
                            <i class="fa fa-cart-plus"></i> Add to Cart
                        </button>
                    <?php else: ?>
                        <button class="btn btn-default border btn-sm btn-flat" disabled>
                            <i class="fa fa-times"></i> Out of Stock
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <img src="<?= validate_image(isset($image_path) ? $image_path : "") ?>" alt="Product Image <?= isset($name) ? $name : "" ?>" class="img-thumbnail product-img">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="mx-2 text-muted">Brand Name</small>
                            <div class="pl-4"><?= isset($brand) ? $brand : '' ?></div>
                        </div>
                        <div class="col-md-6">
                            <small class="mx-2 text-muted">Category</small>
                            <div class="pl-4"><?= isset($category) ? $category : '' ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="mx-2 text-muted">Compatible Models</small>
                            <div class="pl-4"><?= isset($models) ? $models : '' ?></div>
                        </div>
                    </div>
                    <?php 
                        $colors = [];
                        if(isset($available_colors) && trim($available_colors) !== ''){
                            foreach(explode(',', $available_colors) as $c){
                                $c = trim($c);
                                if($c !== '') $colors[] = $c;
                            }
                        }
                    ?>
                    <?php if(!empty($colors)): ?>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <small class="mx-2 text-muted">Available Colors</small>
                            <div class="pl-4">
                                <div class="d-flex flex-wrap" id="color_options">
                                    <?php foreach($colors as $c): ?>
                                    <div class="custom-control custom-radio mr-3 mb-2">
                                        <input type="radio" id="color_<?= md5($c) ?>" name="selected_color" class="custom-control-input" value="<?= htmlspecialchars($c) ?>">
                                        <label class="custom-control-label" for="color_<?= md5($c) ?>"><?= htmlspecialchars($c) ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="mx-2 text-muted">Price</small>
                            <div class="pl-4">
                                <h3 class="text-primary">₱<?= number_format(isset($price) ? $price : 0,2) ?></h3>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <small class="mx-2 text-muted">Stock Status</small>
                            <div class="pl-4">
                                <?php if($available > 10): ?>
                                    <span class="stock-status stock-available">
                                        <i class="fa fa-check-circle"></i> In Stock (<?= $available ?> available)
                                    </span>
                                <?php elseif($available > 0): ?>
                                    <span class="stock-status stock-low">
                                        <i class="fa fa-exclamation-triangle"></i> Low Stock (<?= $available ?> available)
                                    </span>
                                <?php else: ?>
                                    <span class="stock-status stock-out">
                                        <i class="fa fa-times-circle"></i> Out of Stock
                                    </span>
                                    
                                    <!-- Product Recommendations for Out of Stock Items -->
                                    <div id="product_recommendations" class="mt-3">
                                        <h6 class="text-muted">Alternative Products:</h6>
                                        <div id="recommendations_container">
                                            <!-- Recommendations will be loaded here -->
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="mx-2 text-muted">Description</small>
                            <div class="pl-4">
                                <?= isset($description) ? html_entity_decode($description) : '' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content py-0">
    <div class="container">
        <div class="card card-outline rounded-0 card-secondary shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer Reviews</h5>
            </div>
            <div class="card-body">
                <div id="reviews_summary" class="mb-3 text-muted"></div>
                <div id="reviews_list"></div>
                <hr>
                <div class="review-form">
                    <h6 class="mb-2">Leave a Review</h6>
                    <div class="form-group mb-2">
                        <label class="mb-1">Rating</label>
                        <div id="rating_stars">
                            <span class="text-warning h5 mx-1 star" data-val="1">★</span>
                            <span class="text-warning h5 mx-1 star" data-val="2">★</span>
                            <span class="text-warning h5 mx-1 star" data-val="3">★</span>
                            <span class="text-warning h5 mx-1 star" data-val="4">★</span>
                            <span class="text-warning h5 mx-1 star" data-val="5">★</span>
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-1">Comment</label>
                        <textarea id="review_comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                    </div>
                    <button id="submit_review" class="btn btn-sm btn-primary">Submit Review</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        // Load product recommendations if out of stock
        <?php if($available <= 0): ?>
        loadProductRecommendations();
        <?php endif; ?>

        // Reviews
        loadReviews();
        let selectedRating = 0;
        $('#rating_stars .star').on('mouseenter', function(){
            const v = parseInt($(this).data('val'));
            highlightStars(v);
        }).on('mouseleave', function(){
            highlightStars(selectedRating);
        }).on('click', function(){
            selectedRating = parseInt($(this).data('val'));
            highlightStars(selectedRating);
        });
        function highlightStars(v){
            $('#rating_stars .star').each(function(){
                const s = parseInt($(this).data('val'));
                $(this).css('opacity', s <= v ? 1 : 0.3);
            });
        }
        highlightStars(0);

        $('#submit_review').click(function(){
            if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" != 1){
                Swal.fire({
                    title: 'Login Required',
                    text: 'Please login to submit a review.',
                    icon: 'warning',
                    confirmButtonText: 'Login Now',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = './login.php';
                    }
                });
                return;
            }
            if(selectedRating < 1){
                alert_toast('Please select a rating.', 'warning');
                return;
            }
            const comment = $('#review_comment').val();
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_review",
                method:'POST',
                data:{
                    target_type:'product',
                    target_id:'<?= isset($id) ? $id : "" ?>',
                    rating:selectedRating,
                    comment:comment
                },
                dataType:'json',
                error:err=>{
                    console.error(err);
                    alert_toast("An error occurred","error");
                    end_loader();
                },
                success:function(resp){
                    if(resp.status =='success'){
                        $('#review_comment').val('');
                        selectedRating = 0; highlightStars(0);
                        loadReviews();
                        alert_toast(resp.msg,'success');
                    }else if(!!resp.msg){
                        alert_toast(resp.msg,'error');
                    }else{
                        alert_toast("An error occurred","error");
                    }
                    end_loader();
                }
            });
        });
        
        $('#add_to_cart').click(function(){
            if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" == 1){
                if('<?= $available > 0 ?>' == 1){
                    // Show quantity selector
                    Swal.fire({
                        title: 'Add to Cart',
                        html: `
                            <div class="text-center">
                                <h5><?= isset($name) ? $name : '' ?></h5>
                                <p class="text-muted">Price: ₱<?= number_format(isset($price) ? $price : 0,2) ?></p>
                                <p class="text-muted">Available: <?= $available ?> units</p>
                                <?php if(!empty($colors)): ?>
                                <div class="form-group">
                                    <label for="swal_color">Color:</label>
                                    <select id="swal_color" class="form-control" required>
                                        <option value="" selected disabled>Choose color</option>
                                        <?php foreach($colors as $c): ?>
                                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label for="quantity">Quantity:</label>
                                    <input type="number" id="quantity" class="form-control" value="1" min="1" max="<?= $available ?>" style="width: 100px; margin: 0 auto;">
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Add to Cart',
                        cancelButtonText: 'Cancel',
                        preConfirm: () => {
                            const quantity = document.getElementById('quantity').value;
                            <?php if(!empty($colors)): ?>
                            const color = document.getElementById('swal_color').value;
                            if(!color){
                                Swal.showValidationMessage('Please choose a color');
                                return false;
                            }
                            <?php endif; ?>
                            if (quantity < 1 || quantity > <?= $available ?>) {
                                Swal.showValidationMessage('Please enter a valid quantity (1-<?= $available ?>)');
                                return false;
                            }
                            return {
                                quantity: quantity,
                                <?php if(!empty($colors)): ?>
                                color: color
                                <?php else: ?>
                                color: null
                                <?php endif; ?>
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const quantity = result.value.quantity;
                            const color = result.value.color;
                            start_loader();
                    $.ajax({
                        url:_base_url_+"classes/Master.php?f=save_to_cart",
                        method:'POST',
                                data:{
                                    product_id: '<?= isset($id) ? $id : "" ?>',
                                    quantity: quantity,
                                    <?php if(!empty($colors)): ?>
                                    color: color
                                    <?php endif; ?>
                                },
                        dataType:'json',
                        error:err=>{
                                    console.error(err);
                                    alert_toast("An error occurred","error");
                            end_loader();
                        },
                        success:function(resp){
                            if(resp.status =='success'){
                                update_cart_count(resp.cart_count);
                                        Swal.fire({
                                            title: 'Success!',
                                            text: resp.msg,
                                            icon: 'success',
                                            confirmButtonText: 'Continue Shopping',
                                            showCancelButton: true,
                                            cancelButtonText: 'View Cart'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                location.href = './?p=products';
                                            } else {
                                                location.href = './?p=cart';
                                            }
                                        });
                            }else if(!!resp.msg){
                                        alert_toast(resp.msg,'error');
                            }else{
                                        alert_toast("An error occurred","error");
                            }
                            end_loader();
                        }
                            });
                        }
                    });
                } else {
                    alert_toast('Product is out of stock.','warning');
                }
            }else{
                Swal.fire({
                    title: 'Login Required',
                    text: 'Please login to add items to your cart.',
                    icon: 'warning',
                    confirmButtonText: 'Login Now',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = './login.php';
                    }
                });
            }
        });
    });
    
    function loadReviews(){
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_reviews",
            method: "POST",
            data: {
                target_type: 'product',
                target_id: '<?= isset($id) ? $id : "" ?>',
                limit: 10,
                offset: 0
            },
            dataType: "json",
            error: err => {
                console.log(err);
            },
            success: function(resp){
                if(resp.status == 'success'){
                    let s = resp.count + ' review' + (resp.count==1?'':'s');
                    if(resp.count>0){ s += ' • Avg ' + (resp.avg_rating||0) + '/5'; }
                    $('#reviews_summary').text(s);
                    if(resp.reviews.length > 0){
                        var html = '';
                        $.each(resp.reviews, function(idx, r){
                            const name = r.reviewer_name ? r.reviewer_name : 'Customer';
                            const stars = '★★★★★'.slice(0, r.rating) + '☆☆☆☆☆'.slice(0, 5 - r.rating);
                            html += '<div class="mb-3">';
                            html += '<div><strong>'+ name +'</strong> <span class="text-warning">'+ stars +'</span></div>';
                            if(r.comment){ html += '<div class="text-muted">'+ $('<div>').text(r.comment).html() +'</div>'; }
                            html += '<div class="text-xs text-muted">'+ r.date_created +'</div>';
                            html += '</div>';
                        });
                        $('#reviews_list').html(html);
                    } else {
                        $('#reviews_list').html('<p class="text-muted">No reviews yet. Be the first to review this product.</p>');
                    }
                }
            }
        });
    }

    function loadProductRecommendations(){
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_product_recommendations",
            method: "POST",
            data: {
                product_id: '<?= isset($id) ? $id : "" ?>'
            },
            dataType: "json",
            error: err => {
                console.log(err);
            },
            success: function(resp){
                if(resp.status == 'success' && resp.recommendations.length > 0){
                    var html = '<div class="row">';
                    $.each(resp.recommendations, function(index, rec){
                        var stock_status = '';
                        if(rec.available_stock > 10){
                            stock_status = '<span class="badge badge-success">In Stock</span>';
                        } else if(rec.available_stock > 0){
                            stock_status = '<span class="badge badge-warning">Low Stock</span>';
                        } else {
                            stock_status = '<span class="badge badge-danger">Out of Stock</span>';
                        }
                        
                        html += '<div class="col-md-4 mb-2">';
                        html += '<div class="card h-100">';
                        html += '<img src="' + _base_url_ + rec.image_path + '" class="card-img-top" style="height: 100px; object-fit: cover;">';
                        html += '<div class="card-body">';
                        html += '<h6 class="card-title">' + rec.name + '</h6>';
                        html += '<p class="card-text">₱' + parseFloat(rec.price).toLocaleString() + '</p>';
                        html += '<p class="card-text"><small class="text-muted">' + rec.recommendation_type + '</small></p>';
                        html += stock_status;
                        html += '<br><br>';
                        html += '<a href="./?p=products/view_product&id=' + rec.recommended_product_id + '" class="btn btn-sm btn-primary">View Product</a>';
                        html += '</div></div></div>';
                    });
                    html += '</div>';
                    $('#recommendations_container').html(html);
                } else {
                    $('#recommendations_container').html('<p class="text-muted">No alternative products available at this time.</p>');
                }
            }
        });
    }
    
    function notifyWhenAvailable(product_id){
        if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" == 1){
            Swal.fire({
                title: 'Notification Set',
                text: 'You will be notified when this product becomes available.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                title: 'Login Required',
                text: 'Please login to set notifications.',
                icon: 'warning',
                confirmButtonText: 'Login Now',
                showCancelButton: true,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.href = './login.php';
                }
            });
        }
    }
</script>