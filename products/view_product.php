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
        // Load multi-compatibility models if available
        $compat_models = [];
        if($id){
            $cm_rs = $conn->query("SELECT model_name FROM product_compatibility WHERE product_id = '{$id}' ORDER BY model_name ASC");
            if($cm_rs){
                while($cm = $cm_rs->fetch_assoc()) $compat_models[] = $cm['model_name'];
            }
        }
        
        // Determine product category and set related products title
        $related_title = "Related Products";
        $related_category_filter = "";
        
        if(isset($category_id)) {
            switch($category_id) {
                case 10: // Motorcycles
                    $related_title = "Related Motorcycles";
                    $related_category_filter = "motorcycles";
                    break;
                case 13: // Motorcycle Parts
                    $related_title = "Related Motorcycle Parts";
                    $related_category_filter = "motorcycle_parts";
                    break;
                case 15: // Oils
                    $related_title = "Related Genuine Oils";
                    $related_category_filter = "oils";
                    break;
                default:
                    $related_title = "Related Products";
                    $related_category_filter = "all";
                    break;
            }
        }
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
    
    /* Enhanced Star Rating Styles */
    .rating-stars-container {
        display: flex;
        gap: 5px;
        margin-bottom: 5px;
    }
    
    .rating-stars-container .star {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
        display: inline-block;
        padding: 2px;
        border-radius: 3px;
    }
    
    .rating-stars-container .star:hover {
        color: #ffc107;
        transform: scale(1.1);
        text-shadow: 0 0 8px rgba(255, 193, 7, 0.5);
    }
    
    .rating-stars-container .star.selected {
        color: #ffc107;
        text-shadow: 0 0 8px rgba(255, 193, 7, 0.8);
    }
    
    .rating-stars-container .star.hovered {
        color: #ffc107;
        transform: scale(1.05);
    }
    
    .rating-stars-container .star:not(.selected):not(.hovered) {
        opacity: 0.3;
    }
    
    .rating-stars-container .star:not(.selected):not(.hovered):hover {
        opacity: 1;
    }
    
    /* Review form validation styles */
    .review-form .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .review-form .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
    
    .rating-error {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: none;
    }
    
    /* Specifications Content Overflow Handling */
    .specifications-content {
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 100%;
    }
    
    /* Mobile-specific specifications handling */
    @media (max-width: 768px) {
        .specifications-content {
            overflow-x: auto;
            overflow-y: visible;
            white-space: nowrap;
            max-width: 100%;
            padding-right: 10px;
        }
        
        .specifications-content table {
            min-width: 100%;
            white-space: nowrap;
        }
        
        .specifications-content img {
            max-width: 100%;
            height: auto;
        }
        
        .specifications-content pre {
            white-space: pre-wrap;
            word-wrap: break-word;
            overflow-x: auto;
        }
        
        .specifications-content p {
            white-space: normal;
            word-wrap: break-word;
        }
    }
    
    /* Desktop specifications handling */
    @media (min-width: 769px) {
        .specifications-content {
            overflow-x: visible;
            white-space: normal;
        }
        
        .specifications-content table {
            width: 100%;
            table-layout: auto;
        }
    }
</style>
<div class="content py-5 mt-3">
    <div class="container">
        <div class="card card-outline rounded-0 card-primary shadow">
            <div class="card-header">
                <h4 class="card-title">Product Details</h4>
                <div class="card-tools">
                    <?php if($available > 0): ?>
                        <button onclick="if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' != 1){ Swal.fire({ title: 'Login Required', text: 'Please login first to add items to cart.', icon: 'warning', confirmButtonText: 'Login Now', showCancelButton: true, cancelButtonText: 'Cancel' }).then((result) => { if (result.isConfirmed) { location.href = './login.php'; } }); return false; } addToCart(<?= (int)$id ?>);" class="btn btn-default border btn-sm btn-flat" type="button">
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
                                
                                /* Image Zoom Styles */
                                .color-slide img {
                                    cursor: zoom-in;
                                    transition: transform 0.3s ease;
                                }
                                
                                .color-slide img:hover {
                                    transform: scale(1.05);
                                }
                                
                                /* Zoom Modal Styles */
                                .zoom-modal {
                                    display: none;
                                    position: fixed;
                                    z-index: 9999;
                                    left: 0;
                                    top: 0;
                                    width: 100%;
                                    height: 100%;
                                    background-color: rgba(0,0,0,0.9);
                                    cursor: zoom-out;
                                }
                                
                                .zoom-content {
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    max-width: 90%;
                                    max-height: 90%;
                                    cursor: default;
                                }
                                
                                .zoom-content img {
                                    width: 100%;
                                    height: auto;
                                    border-radius: 8px;
                                    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
                                }
                                
                                .zoom-close {
                                    position: absolute;
                                    top: 20px;
                                    right: 35px;
                                    color: #f1f1f1;
                                    font-size: 40px;
                                    font-weight: bold;
                                    cursor: pointer;
                                    z-index: 10000;
                                }
                                
                                .zoom-close:hover {
                                    color: #bbb;
                                }
                                
                                .zoom-info {
                                    position: absolute;
                                    bottom: 20px;
                                    left: 50%;
                                    transform: translateX(-50%);
                                    color: white;
                                    text-align: center;
                                    background: rgba(0,0,0,0.7);
                                    padding: 10px 20px;
                                    border-radius: 20px;
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
                            <div class="available-colors">
                                <h6 class="mb-2">Available Colors:</h6>
                                <div class="color-list">
                                    <?php if(!empty($colors)): ?>
                                        <?php foreach($colors as $color): ?>
                                            <span class="badge badge-primary mr-2 mb-2" style="font-size: 0.9rem; padding: 8px 12px;"><?= htmlspecialchars($color) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No specific colors available</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Image Carousel -->
                    <div class="color-carousel">
                        <button class="nav-btn nav-prev" id="ccPrev">‹</button>
                        <button class="nav-btn nav-next" id="ccNext">›</button>
                        <div class="color-track" id="colorTrack">
                            <?php foreach($slides as $index => $slide): ?>
                                <div class="color-slide" data-color="<?= htmlspecialchars($slide['color']) ?>">
                                    <img src="<?= validate_image($slide['img']) ?>" alt="<?= htmlspecialchars($slide['color']) ?>" class="product-img">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-2">
                            <span id="colorLabel" class="badge badge-info"><?= !empty($slides) ? htmlspecialchars($slides[0]['color']) : 'Default' ?></span>
                        </div>
                        <?php if(count($slides) > 1): ?>
                        <div class="color-dots">
                            <?php foreach($slides as $index => $slide): ?>
                                <div class="color-dot <?= $index == 0 ? 'active' : '' ?>" data-idx="<?= $index ?>">
                                    <img src="<?= validate_image($slide['img']) ?>" alt="<?= htmlspecialchars($slide['color']) ?>">
                                    <div class="color-label"><?= htmlspecialchars($slide['color']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Image Zoom Modal -->
                    <div id="zoomModal" class="zoom-modal">
                        <span class="zoom-close">&times;</span>
                        <div class="zoom-content">
                            <img id="zoomImage" src="" alt="Zoomed Product Image">
                        </div>
                        <div class="zoom-info">
                            <span id="zoomProductName"><?= isset($name) ? htmlspecialchars($name) : '' ?></span> - 
                            <span id="zoomColorName"></span>
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
                            <small class="mx-2 text-muted">Compatibility</small>
                            <div class="pl-4">
                                <?php if(!empty($compat_models)): ?>
                                    <?php foreach($compat_models as $m): ?>
                                        <span class="badge badge-secondary mr-1 mb-1"><?= htmlspecialchars($m) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?= isset($models) ? $models : '' ?>
                                <?php endif; ?>
                            </div>
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
                            <div class="pl-4 specifications-content">
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
                        <label class="mb-1">Rating <span class="text-danger">*</span></label>
                        <div id="rating_stars" class="rating-stars-container">
                            <span class="star" data-val="1" title="1 star - Poor">★</span>
                            <span class="star" data-val="2" title="2 stars - Fair">★</span>
                            <span class="star" data-val="3" title="3 stars - Good">★</span>
                            <span class="star" data-val="4" title="4 stars - Very Good">★</span>
                            <span class="star" data-val="5" title="5 stars - Excellent">★</span>
                        </div>
                        <small class="text-muted">Click on a star to rate this product</small>
                        <div class="rating-error" id="rating_error">Please select a rating before submitting your review.</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="mb-1">Comment</label>
                        <textarea id="review_comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                    </div>
                    <button onclick="if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' != 1){ Swal.fire({ title: 'Login Required', text: 'Please login first to submit a review.', icon: 'warning', confirmButtonText: 'Login Now', showCancelButton: true, cancelButtonText: 'Cancel' }).then((result) => { if (result.isConfirmed) { location.href = './login.php'; } }); return false; } submitReviewForm();" class="btn btn-sm btn-primary">Submit Review</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Related Products Section -->
<div class="content py-0">
    <div class="container">
        <div class="card card-outline rounded-0 card-info shadow">
            <div class="card-header">
                <h5 class="card-title mb-0"><?= $related_title ?></h5>
            </div>
            <div class="card-body">
                <div id="related_products_container">
                    <!-- Related products will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Global functions for add to cart functionality
    function addToCart(productId) {
        // Get product information from current page
        var productName = '<?= isset($name) ? addslashes($name) : "" ?>';
        var productPrice = '<?= isset($price) ? $price : 0 ?>';
        var productPriceValue = productPrice;
        
        // Check if this is a motorcycle product
        var category = '<?= isset($category) ? addslashes($category) : "" ?>'.toLowerCase();
        var isMotorcycle = category.includes('motorcycle') || category.includes('bike');
        
        // Get product details (colors and price) from database
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_product_details",
            method: 'POST',
            data: {
                product_id: productId
            },
            dataType: 'json',
            success: function(resp) {
                if(resp.status === 'success') {
                    // Use database values for price and colors
                    var dbPrice = resp.price || productPriceValue;
                    var dbColors = resp.colors || [];
                    
                    if(dbColors.length > 0) {
                        // Product has colors, show color selection modal
                        showColorSelectionModal(productId, productName, dbPrice, dbColors, isMotorcycle);
                    } else {
                        // No colors, add directly to cart
                        addToCartDirect(productId, '', '', 1);
                    }
                } else {
                    // Fallback to current page values
                    var availableColors = [];
                    <?php if(!empty($colors)): ?>
                    availableColors = <?= json_encode($colors) ?>;
                    <?php endif; ?>
                    
                    if(availableColors.length > 0) {
                        showColorSelectionModal(productId, productName, productPriceValue, availableColors, isMotorcycle);
                    } else {
                        addToCartDirect(productId, '', '', 1);
                    }
                }
            },
            error: function() {
                // Fallback to current page values on error
                var availableColors = [];
                <?php if(!empty($colors)): ?>
                availableColors = <?= json_encode($colors) ?>;
                <?php endif; ?>
                
                if(availableColors.length > 0) {
                    showColorSelectionModal(productId, productName, productPriceValue, availableColors, isMotorcycle);
                } else {
                    addToCartDirect(productId, '', '', 1);
                }
            }
        });
    }

    // Show color selection modal (similar to products/index.php)
    function showColorSelectionModal(productId, productName, productPrice, availableColors, isMotorcycle) {
        var colorOptionsHtml = '';
        if(availableColors.length > 0) {
            colorOptionsHtml = '<div class="form-group">';
            colorOptionsHtml += '<label for="swal_color">Color:</label>';
            colorOptionsHtml += '<select id="swal_color" class="form-control" required>';
            colorOptionsHtml += '<option value="" selected disabled>Choose color</option>';
            availableColors.forEach(function(color) {
                colorOptionsHtml += '<option value="' + color + '">' + color + '</option>';
            });
            colorOptionsHtml += '</select>';
            colorOptionsHtml += '</div>';
        }
        
        Swal.fire({
            title: 'Add to Cart',
            html: `
                <div class="text-center">
                    <h5>${productName}</h5>
                    <p class="text-muted">Price: ₱${parseFloat(productPrice || 0).toLocaleString()}</p>
                    <p class="text-muted">Available: <?= $available ?> units</p>
                    ${colorOptionsHtml}
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
                const color = document.getElementById('swal_color').value;
                
                if(availableColors.length > 0 && !color) {
                    Swal.showValidationMessage('Please choose a color');
                    return false;
                }
                
                if (quantity < 1 || quantity > <?= $available ?>) {
                    Swal.showValidationMessage('Please enter a valid quantity (1-<?= $available ?>)');
                    return false;
                }
                
                return {
                    quantity: quantity,
                    color: color || null
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const quantity = result.value.quantity;
                const color = result.value.color;
                addToCartDirect(productId, color, '', quantity);
            }
        });
    }

    function addToCartDirect(productId, selectedColor, motorcycleUnit, quantity = 1) {
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=save_to_cart",
            method: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
                color: selectedColor
            },
            dataType: 'json',
            beforeSend: function() {
                // Show loading state
                $('button[onclick*="addToCart(' + productId + ')"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');
            },
            success: function(resp) {
                if(resp.status === 'success') {
                    // Update cart count if available
                    if(resp.cart_count) {
                        update_cart_count(resp.cart_count);
                    }
                    // Show success modal similar to products/index.php
                    Swal.fire({
                        title: 'Success!',
                        text: resp.msg,
                        icon: 'success',
                        confirmButtonText: 'Continue Shopping',
                        showCancelButton: true,
                        cancelButtonText: 'View Cart'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Stay on current page
                        } else {
                            location.href = './?p=cart';
                        }
                    });
                } else if(resp.requires_color_selection) {
                    alert_toast('Please select a color first!', 'warning');
                } else {
                    alert_toast(resp.msg || 'Failed to add product to cart', 'error');
                }
            },
            error: function() {
                alert_toast('An error occurred', 'error');
            },
            complete: function() {
                $('button[onclick*="addToCart(' + productId + ')"]').prop('disabled', false).html('<i class="fa fa-cart-plus"></i> Add to Cart');
            }
        });
    }

    // Global variables for review functionality
    let selectedRating = 0;
    let isHovering = false;

    // Global function for review submission
    function submitReviewForm(){
        // Clear previous validation errors
        $('#rating_error').hide();
        $('#review_comment').removeClass('is-invalid');
        
        // Validate rating selection
        if(selectedRating < 1){
            $('#rating_error').show();
            return;
        }
        
        // Validate comment (optional but if provided, should not be empty)
        const comment = $('#review_comment').val().trim();
        if(comment.length > 0 && comment.length < 10){
            $('#review_comment').addClass('is-invalid');
            alert_toast('Please provide a more detailed comment (at least 10 characters) or leave it empty.', 'warning');
            return;
        }
        
        // Submit the review
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
                    selectedRating = 0; 
                    highlightStars(0, false);
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
    }

    // Global function for highlighting stars
    function highlightStars(v, isHover = false){
        $('#rating_stars .star').each(function(){
            const s = parseInt($(this).data('val'));
            const $star = $(this);
            
            // Remove all classes
            $star.removeClass('selected hovered');
            
            if (isHover) {
                // During hover, show hover effect up to hovered star
                if (s <= v) {
                    $star.addClass('hovered');
                }
            } else {
                // When not hovering, show selected stars
                if (s <= v) {
                    $star.addClass('selected');
                }
            }
        });
    }

    // Global function for loading reviews
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

    // Global function for generating review HTML
    function generateReviewHTML(r){
        const name = r.reviewer_name ? r.reviewer_name : 'Customer';
        const stars = '★★★★★'.slice(0, r.rating) + '☆☆☆☆☆'.slice(0, 5 - r.rating);
        const ratingText = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][r.rating] || '';
        const formattedDate = new Date(r.date_created).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        var html = '<div class="mb-3 p-3 border rounded shadow-sm" style="background-color: #f8f9fa; border-left: 4px solid #dc3545;">';
        html += '<div class="d-flex justify-content-between align-items-start mb-2">';
        html += '<div>';
        html += '<strong class="text-primary">'+ name +'</strong>';
        html += '<div class="mt-1">';
        html += '<span class="text-warning h5" style="font-size: 1.2rem;">'+ stars +'</span>';
        html += '<span class="ml-2 text-muted small">('+ ratingText +')</span>';
        html += '</div>';
        html += '</div>';
        html += '<small class="text-muted">'+ formattedDate +'</small>';
        html += '</div>';
        if(r.comment && r.comment.trim()){ 
            html += '<div class="text-dark mt-2" style="line-height: 1.5;">';
            html += '<i class="fa fa-quote-left text-muted mr-1"></i>';
            html += $('<div>').text(r.comment).html();
            html += '</div>'; 
        } else {
            html += '<div class="text-muted small mt-2"><em>No comment provided</em></div>';
        }
        html += '</div>';
        return html;
    }

    $(function(){
        // Load product recommendations if out of stock
        <?php if($available <= 0): ?>
        loadProductRecommendations();
        <?php endif; ?>
        
        // Load related products based on category
        loadRelatedProducts();

        // Reviews
        loadReviews();
        
        // Enhanced star rating functionality
        $('#rating_stars .star').on('mouseenter', function(){
            isHovering = true;
            const v = parseInt($(this).data('val'));
            highlightStars(v, true);
        }).on('mouseleave', function(){
            isHovering = false;
            highlightStars(selectedRating, false);
        }).on('click', function(){
            selectedRating = parseInt($(this).data('val'));
            highlightStars(selectedRating, false);
            $('#rating_error').hide();
        });
        
        // Initialize with no rating selected
        highlightStars(0, false);

        // Define login validation function using the working pattern from footer.php
        function validateLoginRequired(action = 'perform this action') {
            if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" != 1){
                Swal.fire({
                    title: 'Login Required',
                    text: 'Please login first to ' + action + '.',
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
            return true;
        }

        
        

        // Function to handle add to cart (called from onclick) - OLD FUNCTION
        function addToCartForm(){
            
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
        }
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
    
    // Image Zoom Functionality
    $('.color-slide img').click(function() {
        const imgSrc = $(this).attr('src');
        const colorName = $(this).closest('.color-slide').data('color');
        
        $('#zoomImage').attr('src', imgSrc);
        $('#zoomColorName').text(colorName);
        $('#zoomModal').fadeIn(300);
        
        // Prevent body scroll when modal is open
        $('body').css('overflow', 'hidden');
    });
    
    // Close zoom modal
    $('.zoom-close, #zoomModal').click(function(e) {
        if (e.target === this) {
            $('#zoomModal').fadeOut(300);
            $('body').css('overflow', 'auto');
        }
    });
    
    // Close modal with Escape key
    $(document).keyup(function(e) {
        if (e.keyCode === 27) { // Escape key
            $('#zoomModal').fadeOut(300);
            $('body').css('overflow', 'auto');
        }
    });
    
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
    
    function loadRelatedProducts(){
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_related_products",
            method: "POST",
            data: {
                product_id: '<?= isset($id) ? $id : "" ?>',
                category_id: '<?= isset($category_id) ? $category_id : "" ?>',
                brand_id: '<?= isset($brand_id) ? $brand_id : "" ?>',
                category_filter: '<?= $related_category_filter ?>'
            },
            dataType: "json",
            error: err => {
                console.log(err);
            },
            success: function(resp){
                if(resp.status == 'success' && resp.related_products.length > 0){
                    var html = '<div class="row">';
                    $.each(resp.related_products, function(index, product){
                        var stock_status = '';
                        if(product.available_stock > 10){
                            stock_status = '<span class="badge badge-success">In Stock</span>';
                        } else if(product.available_stock > 0){
                            stock_status = '<span class="badge badge-warning">Low Stock</span>';
                        } else {
                            stock_status = '<span class="badge badge-danger">Out of Stock</span>';
                        }
                        
                        html += '<div class="col-md-4 mb-3">';
                        html += '<div class="card h-100 shadow-sm">';
                        html += '<div class="position-relative">';
                        html += '<img src="' + _base_url_ + product.image_path + '" class="card-img-top" style="height: 200px; object-fit: cover;" alt="' + product.name + '">';
                        html += '<div class="position-absolute top-0 end-0 m-2">' + stock_status + '</div>';
                        html += '</div>';
                        html += '<div class="card-body d-flex flex-column">';
                        html += '<h6 class="card-title">' + product.name + '</h6>';
                        html += '<p class="card-text text-muted"><small>' + product.brand + ' - ' + product.category + '</small></p>';
                        html += '<p class="card-text"><strong class="text-primary">₱' + parseFloat(product.price).toLocaleString() + '</strong></p>';
                        html += '<div class="mt-auto">';
                        html += '<a href="./?p=products/view_product&id=' + product.id + '" class="btn btn-sm btn-primary w-100">View Details</a>';
                        html += '</div>';
                        html += '</div></div></div>';
                    });
                    html += '</div>';
                    $('#related_products_container').html(html);
                } else {
                    $('#related_products_container').html('<p class="text-muted text-center">No related products found.</p>');
                }
            }
        });
    }
    
    function notifyWhenAvailable(product_id){
        if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" == 1){
            // Show loading state
            Swal.fire({
                title: 'Setting Notification...',
                text: 'Please wait while we set up your notification.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make AJAX call to save notification
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=set_product_notification',
                method: 'POST',
                data: {
                    product_id: product_id,
                    customer_id: <?= $_settings->userdata('id') ?>
                },
                dataType: 'json',
                success: function(resp){
                    if(resp.status == 'success'){
                        Swal.fire({
                            title: 'Notification Set!',
                            text: 'You will be notified when this product becomes available.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: resp.message || 'Failed to set notification. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error setting notification:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to set notification. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
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
    
    // Fullscreen Image Modal Functions
    function openImageModal(imageSrc, imageAlt) {
        var modal = document.getElementById('imageModal');
        var modalImg = document.getElementById('modalImage');
        var modalCaption = document.getElementById('modalCaption');
        
        modal.style.display = 'block';
        modalImg.src = imageSrc;
        modalCaption.innerHTML = imageAlt;
        
        // Prevent body scroll
        document.body.style.overflow = 'hidden';
        
        // Add zoom functionality
        var zoom = 1;
        modalImg.style.transform = 'scale(' + zoom + ')';
        modalImg.style.transition = 'transform 0.3s ease';
        
        // Touch zoom for mobile
        var startDistance = 0;
        var startZoom = 1;
        
        modalImg.addEventListener('touchstart', function(e) {
            if (e.touches.length === 2) {
                startDistance = getDistance(e.touches[0], e.touches[1]);
                startZoom = zoom;
            }
        });
        
        modalImg.addEventListener('touchmove', function(e) {
            if (e.touches.length === 2) {
                e.preventDefault();
                var currentDistance = getDistance(e.touches[0], e.touches[1]);
                var scale = currentDistance / startDistance;
                zoom = Math.max(1, Math.min(3, startZoom * scale));
                modalImg.style.transform = 'scale(' + zoom + ')';
            }
        });
        
        function getDistance(touch1, touch2) {
            var dx = touch1.clientX - touch2.clientX;
            var dy = touch1.clientY - touch2.clientY;
            return Math.sqrt(dx * dx + dy * dy);
        }
    }
    
    function closeImageModal() {
        var modal = document.getElementById('imageModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    // Close modal when clicking outside the image
    window.onclick = function(event) {
        var modal = document.getElementById('imageModal');
        if (event.target === modal) {
            closeImageModal();
        }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeImageModal();
        }
    });
    }); // Close the $(function(){ that starts on line 536
</script>

<!-- Fullscreen Image Modal -->
<div id="imageModal" class="zoom-modal">
    <span class="zoom-close" onclick="closeImageModal()">&times;</span>
    <div class="zoom-content">
        <img id="modalImage" src="" alt="">
        <div class="zoom-info">
            <p id="modalCaption"></p>
            <small>Click outside or press ESC to close</small>
        </div>
    </div>
</div>