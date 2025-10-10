<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$brand_filter = isset($_GET['brand_filter']) ? explode(",",$_GET['brand_filter']) : 'all';
$category_filter = isset($_GET['category_filter']) ? explode(",",$_GET['category_filter']) : 'all';
?>
<div class="content py-5 mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Search and Categories Bar -->
                <div class="products-header mb-4">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-6">
                            <form action="" id="search_prod" class="search-form">
                                <div class="input-group">
                                    <input type="search" id="product_search" name="search" value="<?= $search ?>" class="form-control search-input" placeholder="Search products..." autocomplete="off">
                                    <button class="btn btn-primary" type="submit" aria-label="Search products">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <!-- Categories Filter -->
                            <div class="categories-filter">
                                <div class="categories-header" data-bs-toggle="collapse" data-bs-target="#categoriesCollapse" aria-expanded="true" aria-controls="categoriesCollapse">
                                    <label class="filter-label">
                                        <i class="fa fa-tags text-primary"></i> Categories:
                                    </label>
                                    <i class="fa fa-chevron-down collapse-icon"></i>
                                </div>
                                <div class="collapse show" id="categoriesCollapse">
                                    <div class="categories-container">
                                        <div class="category-item">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="category_all" value="all" <?= !is_array($category_filter) && $category_filter =='all' ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="category_all">
                                                    <strong>All</strong>
                                                </label>
                                            </div>
                                        </div>
                                        <?php 
                                            $categories = $conn->query("SELECT * FROM `categories` where `delete_flag` =0 and `status` = 1 order by `category` asc");
                                            while($row = $categories->fetch_assoc()):
                                        ?>
                                            <div class="category-item">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input category_filter" type="checkbox" id="category_<?= $row['id'] ?>" value="<?= $row['id'] ?>" <?= ((is_array($category_filter) && in_array($row['id'],$category_filter)) || (!is_array($category_filter) && $category_filter =='all')) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="category_<?= $row['id'] ?>">
                                                        <?= $row['category'] ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-12 text-lg-end">
                            <div class="products-info">
                                <span class="text-muted">
                                    <i class="fa fa-cube"></i> 
                                    <span id="productCount">Loading...</span> products
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="products-grid">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4" id="productsContainer">
                    <?php 
                    $where="";
                    if(is_array($category_filter)){
                        $where.=" and p.category_id in (".(implode(',',$category_filter)).") ";
                    }
                    if(!empty($search)){
                        $where.=" and (p.name LIKE '%{$search}%' or p.description LIKE '%{$search}%' or b.name LIKE '%{$search}%' or c.category LIKE '%{$search}%') ";
                    }
                        $products = $conn->query("SELECT p.*,b.name as brand, c.category FROM `product_list` p inner join brand_list b on p.brand_id = b.id inner join `categories` c on p.category_id = c.id where p.delete_flag = 0 and p.status = 1 {$where} order by RAND()");
                        while($row= $products->fetch_assoc()):
                            // Calculate stock availability
                            $stocks = $conn->query("SELECT SUM(quantity) FROM stock_list where product_id = '{$row['id']}'")->fetch_array()[0];
                            $out = $conn->query("SELECT SUM(quantity) FROM order_items where product_id = '{$row['id']}' and order_id in (SELECT id FROM order_list where `status` != 5) ")->fetch_array()[0];
                            $stocks = $stocks > 0 ? $stocks : 0;
                            $out = $out > 0 ? $out : 0;
                            $available = $stocks - $out;
                    ?>
                        <div class="col mb-4">
                            <div class="product-card h-100">
                                <div class="product-image-container">
                                    <img src="<?= validate_image($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="product-image"/>
                                    
                                    <!-- Stock Status Badge -->
                                    <div class="stock-badge">
                                        <?php if($available > 10): ?>
                                            <span class="badge badge-success">
                                                <i class="fa fa-check-circle"></i> In Stock
                                            </span>
                                        <?php elseif($available > 0): ?>
                                            <span class="badge badge-warning">
                                                <i class="fa fa-exclamation-triangle"></i> Low Stock
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">
                                                <i class="fa fa-times-circle"></i> Out of Stock
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Compare Checkbox -->
                                    <div class="compare-badge">
                                        <label class="compare-label" for="compare_<?= $row['id'] ?>">
                                            <input type="checkbox" id="compare_<?= $row['id'] ?>" name="compare_<?= $row['id'] ?>" class="compare-checkbox" data-id="<?= $row['id'] ?>">
                                            <span class="compare-text">Compare</span>
                                        </label>
                                    </div>
                                    
                                    <!-- Quick Actions Overlay -->
                                    <div class="product-overlay">
                                        <div class="overlay-actions">
                                            <a href="./?p=products/view_product&id=<?= $row['id'] ?>" class="btn btn-light btn-sm" title="Quick View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="product-info">
                                    <div class="product-category"><?= $row['category'] ?></div>
                                    <h5 class="product-title"><?= $row['name'] ?></h5>
                                    <div class="product-brand"><?= $row['brand'] ?></div>
                                    
                                    <!-- Product Description with Clamp -->
                                    <?php if(!empty($row['description'])): ?>
                                    <div class="product-description">
                                        <div class="description-text" data-full-text="<?= htmlspecialchars(strip_tags(html_entity_decode($row['description']))) ?>">
                                            <?= htmlspecialchars(strip_tags(html_entity_decode($row['description']))) ?>
                                        </div>
                                        <button class="btn-link see-more-btn" onclick="toggleDescription(this)">
                                            <span class="see-more-text">See more</span>
                                            <span class="see-less-text" style="display: none;">See less</span>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Color Selection -->
                                    <?php 
                                    $swatches = $conn->query("SELECT color, image_path FROM product_color_images WHERE product_id = '{$row['id']}'");
                                    if($swatches && $swatches->num_rows > 0): ?>
                                    <div class="color-selection">
                                        <small class="color-label">Colors:</small>
                                        <div class="color-options">
                                            <?php while($sw = $swatches->fetch_assoc()): ?>
                                            <div class="color-option" data-color="<?= htmlspecialchars($sw['color']) ?>" data-image="<?= validate_image($sw['image_path']) ?>" title="<?= htmlspecialchars($sw['color']) ?>">
                                                <img src="<?= validate_image($sw['image_path']) ?>" alt="<?= htmlspecialchars($sw['color']) ?>">
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="product-price">
                                        <span class="price">₱<?= number_format($row['price'],2) ?></span>
                                    </div>
                                    
                                    <div class="product-actions">
                                        <?php if($available > 0): ?>
                                            <button class="btn btn-primary btn-sm w-100" onclick="addToCart(<?= $row['id'] ?>)">
                                                <i class="fa fa-cart-plus"></i> Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <div class="out-of-stock-actions">
                                                <button class="btn btn-warning btn-sm w-100 mb-2" onclick="requestNotification(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name']) ?>')">
                                                    <i class="fa fa-bell"></i> Notify When Available
                                                </button>
                                                <button class="btn btn-info btn-sm w-100" onclick="showRecommendations(<?= $row['id'] ?>)">
                                                    <i class="fa fa-lightbulb"></i> See Alternatives
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php if($products->num_rows <= 0): ?>
                    <div class="w-100 d-flex justify-content-center align-items-center" style="min-height:10em">
                    <center><em class="text-muted">No data.</em></center>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        // Initialize category filter state
        if($('.category_filter').length == $('.category_filter:checked').length){
            $('#category_all').prop("checked",true)
        }else{
            $('#category_all').prop("checked",false)
        }
        
        // Category "All" checkbox handler
        $('#category_all').change(function(){
            if($(this).is(':checked') ==true){
                $('.category_filter').prop("checked",true).trigger('change')
            }
        })
        
        // Search form handler
        $('#search_prod').submit(function(e){
            e.preventDefault()
            var search = $(this).serialize()
            location.href="./?p=products"+(search != '' ? "&"+search : "")+"<?= isset($_GET['category_filter']) ? "&category_filter=".$_GET['category_filter'] : "" ?>";
        })
        
        // Category filter handler
        $('.category_filter').change(function(){
            var category_ids = [];
            if($('.category_filter').length == $('.category_filter:checked').length){
                $('#category_all').prop("checked",true)
            }else{
                $('#category_all').prop("checked",false)
                $('.category_filter:checked').each(function(){
                    category_ids.push($(this).val())
                }) 
                category_ids = category_ids.join(",")
            }
             
            location.href="./?p=products"+(category_ids.length > 0 ? "&category_filter="+category_ids : "")+"<?= isset($_GET['search']) ? "&search=".$_GET['search'] : "" ?>";
        })
        
        
        // Update product count
        updateProductCount();
    })
    
    function updateProductCount() {
        var count = $('#productsContainer .col').length;
        $('#productCount').text(count);
    }
</script>
<style>
/* Modern Products Page Styles */


/* Products Header Styles */
.products-header {
    background: #fff;
    border-radius: 0;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.search-input {
    border: 2px solid #e9ecef;
    border-radius: 0;
    padding: 12px 16px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.search-input:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.products-info {
    font-size: 0.9rem;
}

/* Categories Filter Styles */
.categories-filter {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.categories-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    padding: 8px 0;
    transition: all 0.3s ease;
}

.categories-header:hover {
    background: #f8f9fa;
    margin: 0 -10px;
    padding: 8px 10px;
}

.filter-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin: 0;
    white-space: nowrap;
}

.collapse-icon {
    transition: transform 0.3s ease;
    font-size: 0.875rem;
    color: #6c757d;
}

.categories-header[aria-expanded="true"] .collapse-icon {
    transform: rotate(180deg);
}

.categories-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.category-item {
    display: inline-block;
}

.category-item .form-check {
    margin: 0;
}

.category-item .form-check-label {
    font-size: 0.85rem;
    color: #6c757d;
    cursor: pointer;
    transition: color 0.3s ease;
    white-space: nowrap;
}

.category-item .form-check-label:hover {
    color: #dc3545;
}

.category-item .form-check-input:checked + .form-check-label {
    color: #dc3545;
    font-weight: 500;
}

/* Product Card Styles */
.product-card {
    background: #fff;
    border-radius: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.15);
}

.product-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: #f8f9fa;
    flex-shrink: 0;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.stock-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    z-index: 2;
}

.badge {
    font-size: 0.75rem;
    padding: 6px 10px;
    border-radius: 20px;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.badge-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: #212529;
}

.badge-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.compare-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
}

.compare-label {
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
}

.compare-label:hover {
    background: rgba(220, 53, 69, 0.9);
}

.compare-checkbox {
    margin: 0;
}

.product-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-card:hover .product-overlay {
    opacity: 1;
}

.overlay-actions {
    display: flex;
    gap: 10px;
}

.overlay-actions .btn {
    border-radius: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.overlay-actions .btn:hover {
    transform: scale(1.1);
}

.product-info {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.product-category {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.6em;
}

.product-brand {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 10px;
}

/* Product Description Styles */
.product-description {
    margin-bottom: 15px;
    flex-grow: 1;
}

.description-text {
    font-size: 0.85rem;
    color: #6c757d;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: all 0.3s ease;
}

.description-text.expanded {
    -webkit-line-clamp: unset;
    display: block;
}

.see-more-btn {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 0.8rem;
    padding: 0;
    margin-top: 5px;
    cursor: pointer;
    text-decoration: none;
    transition: color 0.3s ease;
}

.see-more-btn:hover {
    color: #c82333;
    text-decoration: underline;
}

.see-more-btn:focus {
    outline: none;
    text-decoration: underline;
}

.color-selection {
    margin-bottom: 15px;
}

.color-label {
    font-size: 0.8rem;
    color: #6c757d;
    display: block;
    margin-bottom: 8px;
}

.color-options {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.color-option {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
}

.color-option:hover {
    border-color: #dc3545;
    transform: scale(1.1);
}

.color-option.selected {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.3);
}

.color-option img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-price {
    margin-bottom: 15px;
}

.price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #dc3545;
}

.product-actions {
    margin-top: auto;
    padding-top: 15px;
}

.product-actions .btn {
    border-radius: 0;
    padding: 10px 16px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
}

.btn-secondary {
    background: #6c757d;
    border: none;
}

/* Compare Bar Styles */
.compare-bar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 0;
    padding: 12px 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    backdrop-filter: blur(10px);
}

.compare-bar .item {
    display: inline-block;
    margin: 0 8px;
    padding: 4px 12px;
    border: 1px solid #dc3545;
    border-radius: 20px;
    font-size: 0.85rem;
    background: #dc3545;
    color: white;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-image-container {
        height: 180px;
    }
    
    .product-info {
        padding: 15px;
    }
    
    .products-header {
        padding: 15px;
    }
    
    .search-input {
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .categories-header {
        padding: 6px 0;
    }
    
    .categories-header:hover {
        margin: 0 -5px;
        padding: 6px 5px;
    }
    
    .categories-container {
        width: 100%;
        justify-content: flex-start;
    }
    
    .filter-label {
        font-size: 0.85rem;
    }
    
    .category-item .form-check-label {
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .product-image-container {
        height: 160px;
    }
    
    .overlay-actions .btn {
        width: 35px;
        height: 35px;
    }
    
    .compare-bar {
        bottom: 10px;
        padding: 8px 15px;
        font-size: 0.8rem;
    }
}
</style>
<div id="compareBar" class="compare-bar d-none">
    <span class="mr-2">Compare:</span>
    <span id="compareItems"></span>
    <button id="compareBtn" class="btn btn-sm btn-primary ml-2">Compare</button>
    <button id="clearCompare" class="btn btn-sm btn-link">Clear</button>
    <input type="hidden" id="compareIds" name="compareIds" value="">
</div>
<script>
(function(){
    const maxCompare = 3;
    const bar = $('#compareBar');
    const items = $('#compareItems');
    const idsInput = $('#compareIds');
    function renderBar(){
        const ids = (idsInput.val()||'').split(',').filter(Boolean);
        items.empty();
        ids.forEach(function(id){ items.append('<span class="item" data-id="'+id+'">#'+id+'</span>'); });
        if(ids.length>0){ bar.removeClass('d-none'); } else { bar.addClass('d-none'); }
        $('.compare-checkbox').each(function(){
            const id=$(this).data('id')+'';
            $(this).prop('checked', ids.includes(id));
        });
    }
    function toggleId(id){
        let ids = (idsInput.val()||'').split(',').filter(Boolean);
        if(ids.includes(id)) ids = ids.filter(x=>x!==id);
        else{
            if(ids.length>=maxCompare){ alert_toast('You can compare up to '+maxCompare+' items.','warning'); return; }
            ids.push(id);
        }
        idsInput.val(ids.join(','));
        renderBar();
    }
    $(document).on('change','.compare-checkbox',function(){ toggleId(($(this).data('id')+'')); });
    $('#clearCompare').on('click',function(){ idsInput.val(''); renderBar(); });
    $('#compareBtn').on('click',function(){
        const ids = idsInput.val();
        if(!ids){ return; }
        location.href = './?p=products/compare&ids='+encodeURIComponent(ids);
    });
    renderBar();
})();

// Add to Cart function
function addToCart(productId) {
    // Find the product card containing this button
    var productCard = $('button[onclick="addToCart(' + productId + ')"]').closest('.card');
    
    // Check if color selection is required
    var colorOptions = productCard.find('.color-option');
    var selectedColor = '';
    
    if(colorOptions.length > 0) {
        var selectedOption = productCard.find('.color-option.selected');
        if(selectedOption.length === 0) {
            alert_toast('Please select a color first!', 'warning');
            return;
        }
        selectedColor = selectedOption.attr('data-color');
    }
    
    $.ajax({
        url: '<?= base_url ?>classes/Master.php?f=save_to_cart',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: 1,
            color: selectedColor
        },
        dataType: 'json',
        beforeSend: function() {
            // Show loading state
            $('button[onclick="addToCart(' + productId + ')"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Adding...');
        },
        success: function(resp) {
            if(resp.status === 'success') {
                alert_toast('Product added to cart successfully!', 'success');
            } else {
                alert_toast('Failed to add product to cart', 'error');
            }
        },
        error: function() {
            alert_toast('An error occurred', 'error');
        },
        complete: function() {
            $('button[onclick="addToCart(' + productId + ')"]').prop('disabled', false).html('<i class="fa fa-cart-plus"></i> Add to Cart');
        }
    });
}

// Color selection functionality
$(document).ready(function() {
    $('.color-option').click(function() {
        var productCard = $(this).closest('.product-card');
        var colorOptions = productCard.find('.color-option');
        
        // Remove selected class from all options
        colorOptions.removeClass('selected');
        
        // Add selected class to clicked option
        $(this).addClass('selected');
        
        // Update main product image if color has different image
        var newImage = $(this).data('image');
        if(newImage) {
            var mainImage = productCard.find('.product-image');
            mainImage.attr('src', newImage);
        }
    });
});


// Toggle Description Function
function toggleDescription(button) {
    const descriptionText = button.previousElementSibling;
    const seeMoreText = button.querySelector('.see-more-text');
    const seeLessText = button.querySelector('.see-less-text');
    
    if (descriptionText.classList.contains('expanded')) {
        // Collapse
        descriptionText.classList.remove('expanded');
        seeMoreText.style.display = 'inline';
        seeLessText.style.display = 'none';
    } else {
        // Expand
        descriptionText.classList.add('expanded');
        seeMoreText.style.display = 'none';
        seeLessText.style.display = 'inline';
    }
}

// Initialize description clamping on page load
$(document).ready(function() {
    // Check if descriptions need "see more" button
    $('.description-text').each(function() {
        const $this = $(this);
        const fullText = $this.data('full-text');
        const $seeMoreBtn = $this.siblings('.see-more-btn');
        
        // If text is short enough, hide the "see more" button
        if (fullText && fullText.length <= 120) {
            $seeMoreBtn.hide();
        }
    });
});

// Product notification and recommendation functions
function requestNotification(productId, productName) {
    // Check if user is logged in
    var isLoggedIn = <?= ($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2) ? 'true' : 'false' ?>;
    
    if (!isLoggedIn) {
        alert_toast('Please login first to request notifications.', 'warning');
        return;
    }
    
    $.ajax({
        url: '<?= base_url ?>classes/Master.php?f=request_product_notification',
        method: 'POST',
        data: {
            product_id: productId
        },
        dataType: 'json',
        beforeSend: function() {
            start_loader();
        },
        success: function(resp) {
            if (resp.status === 'success') {
                alert_toast(resp.msg, 'success');
                // Change button to show it's subscribed
                $('button[onclick="requestNotification(' + productId + ', \'' + productName + '\')"]')
                    .removeClass('btn-warning')
                    .addClass('btn-success')
                    .html('<i class="fa fa-check"></i> Notification Set')
                    .prop('onclick', null);
            } else {
                alert_toast(resp.msg, 'error');
            }
        },
        error: function() {
            alert_toast('An error occurred while setting up notification.', 'error');
        },
        complete: function() {
            end_loader();
        }
    });
}

function showRecommendations(productId) {
    $.ajax({
        url: '<?= base_url ?>classes/Master.php?f=get_product_recommendations',
        method: 'POST',
        data: {
            product_id: productId
        },
        dataType: 'json',
        beforeSend: function() {
            start_loader();
        },
        success: function(resp) {
            if (resp.status === 'success' && resp.recommendations.length > 0) {
                displayRecommendations(resp.recommendations);
            } else {
                alert_toast('No alternative products found at the moment.', 'info');
            }
        },
        error: function() {
            alert_toast('An error occurred while loading recommendations.', 'error');
        },
        complete: function() {
            end_loader();
        }
    });
}

function displayRecommendations(recommendations) {
    var modalContent = '<div class="container-fluid">';
    modalContent += '<h5 class="mb-3">Alternative Products</h5>';
    modalContent += '<div class="row">';
    
    recommendations.forEach(function(rec) {
        var stockStatus = rec.available_stock > 10 ? 'In Stock' : 
                         rec.available_stock > 0 ? 'Low Stock' : 'Out of Stock';
        var stockClass = rec.available_stock > 10 ? 'success' : 
                        rec.available_stock > 0 ? 'warning' : 'danger';
        
        modalContent += '<div class="col-md-6 mb-3">';
        modalContent += '<div class="card h-100">';
        modalContent += '<div class="row no-gutters">';
        modalContent += '<div class="col-4">';
        modalContent += '<img src="' + rec.image_path + '" class="card-img h-100" style="object-fit: cover;" alt="' + rec.name + '">';
        modalContent += '</div>';
        modalContent += '<div class="col-8">';
        modalContent += '<div class="card-body p-2">';
        modalContent += '<h6 class="card-title mb-1">' + rec.name + '</h6>';
        modalContent += '<p class="card-text mb-1"><small class="text-muted">₱' + parseFloat(rec.price).toLocaleString() + '</small></p>';
        modalContent += '<span class="badge badge-' + stockClass + ' mb-2">' + stockStatus + '</span>';
        modalContent += '<div class="d-flex gap-1">';
        modalContent += '<a href="./?p=products/view_product&id=' + rec.recommended_product_id + '" class="btn btn-sm btn-outline-primary">View</a>';
        if (rec.available_stock > 0) {
            modalContent += '<button class="btn btn-sm btn-primary" onclick="addToCart(' + rec.recommended_product_id + ')">Add to Cart</button>';
        }
        modalContent += '</div>';
        modalContent += '</div>';
        modalContent += '</div>';
        modalContent += '</div>';
        modalContent += '</div>';
    });
    
    modalContent += '</div>';
    modalContent += '</div>';
    
    // Show modal
    $('#uni_modal .modal-title').html('Alternative Products');
    $('#uni_modal .modal-body').html(modalContent);
    $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-lg modal-dialog-centered");
    $('#uni_modal').modal({
        show: true,
        backdrop: 'static',
        keyboard: false,
        focus: true
    });
}
</script>