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
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        border: 1px solid #dc3545;
    }
    .stock-low {
        background: linear-gradient(135deg, #fd7e14, #e8590c);
        color: white;
        border: 1px solid #fd7e14;
    }
    .stock-out {
        background: linear-gradient(135deg, #000000, #343a40);
        color: white;
        border: 1px solid #000000;
    }
    
    /* Red and Black Theme for Product View */
    .btn-primary {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    }
    
    .btn-outline-primary {
        border: 2px solid #dc3545;
        color: #dc3545;
    }
    
    .btn-outline-primary:hover {
        background: #dc3545;
        color: white;
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
                        <div class="col-12">
                            <style>
                                .color-carousel{
                                    position: relative; 
                                    overflow: hidden; 
                                    padding: 0 40px;
                                }
                                @media (max-width: 767.98px){ 
                                    .color-carousel{ 
                                        padding: 0 10px; 
                                    } 
                                }
                                .color-track{
                                    display: flex; 
                                    transition: transform 0.3s ease;
                                }
                                .color-slide{
                                    flex: 0 0 100%; 
                                    display: flex; 
                                    justify-content: center; 
                                    align-items: center;
                                    min-height: 300px;
                                }
                                @media (max-width: 767.98px){ 
                                    .color-slide{ 
                                        min-height: 250px;
                                    } 
                                }
                                .color-slide img{
                                    max-width: 100%; 
                                    height: auto; 
                                    object-fit: contain; 
                                    border-radius: 6px; 
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                }
                                .nav-btn{
                                    position: absolute; 
                                    top: 50%; 
                                    transform: translateY(-50%); 
                                    background: #fff; 
                                    border: 1px solid #ddd; 
                                    width: 40px; 
                                    height: 40px; 
                                    border-radius: 50%; 
                                    display: none; 
                                    justify-content: center; 
                                    align-items: center; 
                                    cursor: pointer; 
                                    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                                    font-size: 18px;
                                    color: #333;
                                    z-index: 10;
                                }
                                .nav-btn:hover{
                                    background: #f8f9fa;
                                    border-color: #007bff;
                                    color: #007bff;
                                }
                                .nav-prev{left: 10px;} 
                                .nav-next{right: 10px;}
                                @media (min-width: 768px){ 
                                    .nav-btn{display: flex;} 
                                }
                                .color-dots{
                                    display: flex; 
                                    flex-wrap: wrap; 
                                    gap: 8px; 
                                    justify-content: center; 
                                    margin-top: 15px;
                                }
                                .color-dot{
                                    width: 30px;
                                    height: 30px;
                                    border: 2px solid #ccc;
                                    border-radius: 4px; 
                                    overflow: hidden; 
                                    cursor: pointer; 
                                    transition: all 0.2s ease;
                                }
                                .color-dot:hover{
                                    border-color: #007bff;
                                }
                                .color-dot img{
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                }
                                .color-dot.active{
                                    border-color: #007bff;
                                    box-shadow: 0 0 0 2px rgba(0,123,255,0.3);
                                }
                                .color-label{
                                    text-align: center; 
                                    font-size: 0.9rem; 
                                    color: #6c757d; 
                                    margin-top: 8px; 
                                    min-height: 1.2em;
                                }
                            </style>
                            <?php 
                                $colors = [];
                                if(isset($available_colors) && trim($available_colors) !== ''){
                                    foreach(explode(',', $available_colors) as $c){ $c = trim($c); if($c !== '') $colors[] = $c; }
                                }
                                $swatches = $conn->query("SELECT color, image_path FROM product_color_images WHERE product_id = '".$id."'");
                                $hasSw = $swatches && $swatches->num_rows>0; 
                                $colorToImg = [];
                                if($hasSw){ while($s=$swatches->fetch_assoc()){ $colorToImg[trim(strtolower($s['color']))]=$s['image_path']; } }
                                // Build slide list
                                $slides = [];
                                if(!empty($colors)){
                                    foreach($colors as $c){
                                        $key = strtolower(trim($c));
                                        $img = isset($colorToImg[$key]) ? $colorToImg[$key] : (isset($image_path) ? $image_path : '');
                                        $slides[] = ['color'=>$c, 'img'=>$img];
                                    }
                                } else {
                                    $slides[] = ['color'=> isset($name) ? $name : 'Default', 'img'=> (isset($image_path) ? $image_path : '')];
                                }
                            ?>
                            <div class="color-carousel" id="colorCarousel" data-index="0">
                                <div class="color-track" id="colorTrack">
                                    <?php foreach($slides as $s): ?>
                                        <div class="color-slide" data-color="<?= htmlspecialchars($s['color']) ?>">
                                            <img src="<?= validate_image($s['img']) ?>" loading="lazy" alt="<?= htmlspecialchars($s['color']) ?> - <?= isset($name) ? htmlspecialchars($name) : '' ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="nav-btn nav-prev" id="ccPrev" aria-label="Previous">‹</button>
                                <button type="button" class="nav-btn nav-next" id="ccNext" aria-label="Next">›</button>
                            </div>
                            <div class="color-dots" id="colorDots">
                                <?php foreach($slides as $idx=>$s): ?>
                                    <div class="color-dot<?= $idx==0 ? ' active':'' ?>" data-idx="<?= $idx ?>" title="<?= htmlspecialchars($s['color']) ?>">
                                        <img src="<?= validate_image($s['img']) ?>" loading="lazy" alt="<?= htmlspecialchars($s['color']) ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="color-label" id="colorLabel"><?= htmlspecialchars($slides[0]['color']) ?></div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small class="mx-2 text-muted">Product Name</small>
                            <div class="pl-4"><?= isset($name) ? $name : '' ?></div>
                        </div>
                        <div class="col-md-6">
                            <small class="mx-2 text-muted">Category</small>
                            <div class="pl-4"><?= isset($category) ? $category : '' ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="mx-2 text-muted">Compatible Motorcycle</small>
                            <div class="pl-4"><?= isset($models) ? $models : '' ?></div>
                        </div>
                    </div>
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
                            <small class="mx-2 text-muted">Specifications</small>
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
                <div id="reviews_collapsible" class="collapse" style="display: none;">
                    <div id="reviews_additional"></div>
                </div>
                <div id="reviews_controls" class="text-center mt-3" style="display: none;">
                    <button id="show_more_reviews" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-chevron-down"></i> Show More Reviews
                    </button>
                    <button id="show_less_reviews" class="btn btn-outline-secondary btn-sm" style="display: none;">
                        <i class="fa fa-chevron-up"></i> Show Less
                    </button>
                </div>
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
            if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" != 1){
                Swal.fire({
                    title: 'Login Required',
                    text: 'Please login first to add items to cart.',
                    icon: 'warning',
                    confirmButtonText: 'Login Now',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.href = './login.php';
                    }
                });
                return false;
            }
            
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
        });
    });
    // Swap main image when selecting a color with a swatch
    $(document).on('change','.color-radio', function(){
        var color = ($(this).val()||'').toLowerCase().trim();
        // map color to image via PHP-rendered dataset
        var map = {};
        <?php 
        $mapPairs = [];
        if(isset($colorToImg)){
            foreach($colorToImg as $ck=>$ip){
                $mapPairs[] = json_encode($ck).":".json_encode($ip);
            }
        }
        ?>
        map = {<?= implode(',', $mapPairs) ?>};
        if(map[color]){
            $('#mainProductImage').attr('src', _base_url_ + map[color]);
        }
    });
    
    // Simple Color Carousel Functionality
    let currentSlide = 0;
    const slides = $('.color-slide');
    const totalSlides = slides.length;
    
    function updateCarousel() {
        const track = $('#colorTrack');
        const translateX = -currentSlide * 100;
        track.css('transform', `translateX(${translateX}%)`);
        
        // Update dots
        $('.color-dot').removeClass('active');
        $(`.color-dot[data-idx="${currentSlide}"]`).addClass('active');
        
        // Update label
        const currentColor = slides.eq(currentSlide).data('color');
        $('#colorLabel').text(currentColor);
        
        // Update button states
        if (currentSlide === 0) {
            $('#ccPrev').hide();
        } else {
            $('#ccPrev').show();
        }
        
        if (currentSlide === totalSlides - 1) {
            $('#ccNext').hide();
        } else {
            $('#ccNext').show();
        }
    }
    
    // Navigation button handlers
    $('#ccPrev').click(function() {
        if (currentSlide > 0) {
            currentSlide--;
            updateCarousel();
        }
    });
    
    $('#ccNext').click(function() {
        if (currentSlide < totalSlides - 1) {
            currentSlide++;
            updateCarousel();
        }
    });
    
    // Dot navigation
    $('.color-dot').click(function() {
        currentSlide = parseInt($(this).data('idx'));
        updateCarousel();
    });
    
    // Initialize carousel
    if (totalSlides > 0) {
        updateCarousel();
    }
    
    // Review collapsible functionality
    $('#show_more_reviews').click(function(){
        $('#reviews_collapsible').slideDown(300);
        $('#show_more_reviews').hide();
        $('#show_less_reviews').show();
    });
    
    $('#show_less_reviews').click(function(){
        $('#reviews_collapsible').slideUp(300);
        $('#show_less_reviews').hide();
        $('#show_more_reviews').show();
    });
    
    function loadReviews(){
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_reviews",
            method: "POST",
            data: {
                target_type: 'product',
                target_id: '<?= isset($id) ? $id : "" ?>',
                limit: 1000, // Fetch all reviews
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
                        const initialDisplayCount = 3; // Show first 3 reviews initially
                        const allReviews = resp.reviews;
                        const initialReviews = allReviews.slice(0, initialDisplayCount);
                        const additionalReviews = allReviews.slice(initialDisplayCount);
                        
                        // Display initial reviews
                        var html = '';
                        $.each(initialReviews, function(idx, r){
                            html += generateReviewHTML(r);
                        });
                        $('#reviews_list').html(html);
                        
                        // Display additional reviews in collapsible section
                        if(additionalReviews.length > 0){
                            var additionalHtml = '';
                            $.each(additionalReviews, function(idx, r){
                                additionalHtml += generateReviewHTML(r);
                            });
                            $('#reviews_additional').html(additionalHtml);
                            $('#reviews_controls').show();
                        }
                    } else {
                        $('#reviews_list').html('<p class="text-muted">No reviews yet. Be the first to review this product.</p>');
                    }
                }
            }
        });
    }
    
    function generateReviewHTML(r){
        const name = r.reviewer_name ? r.reviewer_name : 'Customer';
        const stars = '★★★★★'.slice(0, r.rating) + '☆☆☆☆☆'.slice(0, 5 - r.rating);
        var html = '<div class="mb-3 p-3 border rounded" style="background-color: #f8f9fa;">';
        html += '<div class="d-flex justify-content-between align-items-start mb-2">';
        html += '<div><strong>'+ name +'</strong> <span class="text-warning">'+ stars +'</span></div>';
        html += '<small class="text-muted">'+ r.date_created +'</small>';
        html += '</div>';
        if(r.comment){ 
            html += '<div class="text-muted">'+ $('<div>').text(r.comment).html() +'</div>'; 
        }
        html += '</div>';
        return html;
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