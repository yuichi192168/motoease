<?php
$search = isset($_GET['search']) ? $_GET['search'] : '';
$brand_filter = isset($_GET['brand_filter']) ? explode(",",$_GET['brand_filter']) : 'all';
$category_filter = isset($_GET['category_filter']) ? explode(",",$_GET['category_filter']) : 'all';
?>
<div class="content py-5 mt-3">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Categories Filter and Search Bar in Same Row -->
                <div class="row mb-3">
                    <!-- Categories Filter -->
                    <div class="col-lg-8 col-md-12 mb-3 mb-lg-0">
                        <div class="categories-filter-container">
                            <h6 class="filter-title mb-2 text-muted">Filter by Category</h6>
                            <div class="horizontal-categories">
                                <div class="category-filter-item">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="category_all" value="all" <?= !is_array($category_filter) && $category_filter =='all' ? 'checked' : '' ?>>
                                        <label for="category_all" class="custom-control-label">All</label>
                                    </div>
                                </div>
                            <?php 
                                $categories = $conn->query("SELECT * FROM `categories` where `delete_flag` =0 and `status` = 1 order by `category` asc");
                                while($row = $categories->fetch_assoc()):
                            ?>
                                    <div class="category-filter-item">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input category_filter" type="checkbox" id="category_<?= $row['id'] ?>" value="<?= $row['id'] ?>" <?= ((is_array($category_filter) && in_array($row['id'],$category_filter)) || (!is_array($category_filter) && $category_filter =='all')) ? 'checked' : '' ?>>
                                            <label for="category_<?= $row['id'] ?>" class="custom-control-label"><?= $row['category'] ?></label>
                                        </div>
                                    </div>
                            <?php endwhile; ?>
                    </div>
                </div>
            </div>
                    
                    <!-- Search Bar -->
                    <div class="col-lg-4 col-md-12">
                        <div class="search-container">
                            <form action="" id="search_prod">
                                <div class="input-group input-group-sm">
                                    <input type="search" name="search" value="<?= $search ?>" class="form-control" placeholder="Search Product...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4">
                    <?php 
                    $where="";
                    if(is_array($brand_filter)){
                        $where.=" and p.brand_id in (".(implode(',',$brand_filter)).") ";
                    }
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
                        <div class="col px-1 py-2">
                            <div class="card rounded-0 shadow h-100">
                                <div class="product-img-holder overflow-hidden position-relative">
                                    <img src="<?= validate_image($row['image_path']) ?>" alt="Product Image" class="img-top"/>
                                    
                                    <!-- Stock Status Badge -->
                                    <div class="position-absolute" style="top:6px; left:6px;">
                                        <?php if($available > 10): ?>
                                            <span class="badge badge-success">In Stock</span>
                                        <?php elseif($available > 0): ?>
                                            <span class="badge badge-warning">Low Stock</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Compare Checkbox -->
                                    <div class="position-absolute" style="top:6px; right:6px;">
                                        <label class="badge mb-0" style="cursor:pointer; background:#111; color:#fff; border:1px solid #2c2c2c;">
                                            <input type="checkbox" class="compare-checkbox" data-id="<?= $row['id'] ?>" style="vertical-align:middle;"> <span style="color:#dc3545;font-weight:600;">Compare</span>
                                        </label>
                                    </div>
                                    
                                    <!-- Price Tag with Peso Symbol -->
                                    <span class="position-absolute price-tag rounded-pill bg-success text-light px-3" style="bottom:6px; right:6px;">
                                        <i class="fa fa-tags"></i> <b>₱<?= number_format($row['price'],2) ?></b>
                                    </span>
                                    
                                    <!-- Color Swatches -->
                                    <!-- <?php 
                                    $swatches = $conn->query("SELECT color, image_path FROM product_color_images WHERE product_id = '{$row['id']}'");
                                    if($swatches && $swatches->num_rows > 0): ?>
                                    <div class="position-absolute" style="bottom:6px; left:6px; right:6px;">
                                        <div class="d-flex flex-wrap">
                                            <?php while($sw = $swatches->fetch_assoc()): ?>
                                            <div class="mr-1 mb-1" title="<?= htmlspecialchars($sw['color']) ?>">
                                                <img src="<?= validate_image($sw['image_path']) ?>" alt="<?= htmlspecialchars($sw['color']) ?>" style="width:28px; height:28px; object-fit:cover; border:1px solid #ddd; border-radius:3px;">
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?> -->
                                </div>
                                <div class="card-body border-top d-flex flex-column">
                                    <h4 class="card-title my-0 line-clamp-1"><b><?= $row['name'] ?></b></h4>
                                    <small class="text-muted line-clamp-1"><?= $row['brand'] ?></small>
                                    <small class="text-muted line-clamp-1"><?= $row['category'] ?></small>
                                    <p class="m-0 line-clamp-2 flex-grow-1"><?= strip_tags(html_entity_decode($row['description'])) ?></p>
                                    
                                    <!-- Color Selection -->
                                    <?php 
                                    $swatches = $conn->query("SELECT color, image_path FROM product_color_images WHERE product_id = '{$row['id']}'");
                                    if($swatches && $swatches->num_rows > 0): ?>
                                    <div class="mt-2 color-selection-container">
                                        <small class="text-muted">Available Colors:</small>
                                        <div class="d-flex flex-wrap mt-1 color-options">
                                            <?php while($sw = $swatches->fetch_assoc()): ?>
                                            <div class="color-option mr-1 mb-1" data-color="<?= htmlspecialchars($sw['color']) ?>" data-image="<?= validate_image($sw['image_path']) ?>" title="<?= htmlspecialchars($sw['color']) ?>">
                                                <img src="<?= validate_image($sw['image_path']) ?>" alt="<?= htmlspecialchars($sw['color']) ?>" style="width:20px; height:20px; object-fit:cover; border:1px solid #ddd; border-radius:50%;">
                                            </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <!-- Add to Cart Button -->
                                    <div class="mt-3">
                                        <?php if($available > 0): ?>
                                            <button class="btn btn-primary btn-sm w-100" onclick="addToCart(<?= $row['id'] ?>)">
                                                <i class="fa fa-cart-plus"></i> Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                                <i class="fa fa-times"></i> Out of Stock
                                            </button>
                                        <?php endif; ?>
                                        
                                        <a href="./?p=products/view_product&id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm w-100 mt-1">
                                            <i class="fa fa-eye"></i> View Details
                                        </a>
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
        if($('.brand_filter').length == $('.brand_filter:checked').length){
            $('#brand_all').prop("checked",true)
        }else{
            $('#brand_all').prop("checked",false)
        }
        if($('.category_filter').length == $('.category_filter:checked').length){
            $('#category_all').prop("checked",true)
        }else{
            $('#category_all').prop("checked",false)
        }
        $('#brand_all').change(function(){
            if($(this).is(':checked') ==true){
                $('.brand_filter').prop("checked",true).trigger('change')
            }
        })
        $('#category_all').change(function(){
            if($(this).is(':checked') ==true){
                $('.category_filter').prop("checked",true).trigger('change')
            }
        })
        $('#search_prod').submit(function(e){
            e.preventDefault()
            var search = $(this).serialize()
            location.href="./?p=products"+(search != '' ? "&"+search : "")+"<?= isset($_GET['brand_filter']) ? "&brand_filter=".$_GET['brand_filter'] : "" ?><?= isset($_GET['category_filter']) ? "&category_filter=".$_GET['category_filter'] : "" ?>";

        })
        $('.brand_filter').change(function(){
            var brand_ids = [];
            if($('.brand_filter').length == $('.brand_filter:checked').length){
                $('#brand_all').prop("checked",true)
            }else{
                $('#brand_all').prop("checked",false)
                $('.brand_filter:checked').each(function(){
                    brand_ids.push($(this).val())
                })  
                brand_ids = brand_ids.join(",")
            }
            
            location.href="./?p=products"+(brand_ids.length > 0 ? "&brand_filter="+brand_ids : "")+"<?= isset($_GET['category_filter']) ? "&category_filter=".$_GET['category_filter'] : "" ?><?= isset($_GET['search']) ? "&search=".$_GET['search'] : "" ?>";
        })
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
             
            location.href="./?p=products"+(category_ids.length > 0 ? "&category_filter="+category_ids : "")+"<?= isset($_GET['brand_filter']) ? "&brand_filter=".$_GET['brand_filter'] : "" ?><?= isset($_GET['search']) ? "&search=".$_GET['search'] : "" ?>";
        })
    })
</script>
<style>
/* Enhanced Product Card Styles */
.product-img-holder {
    height: 200px;
    overflow: hidden;
}

.img-top {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.card:hover .img-top {
    transform: scale(1.05);
}

/* Line Clamp Styles for Consistent Card Heights */
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
    max-height: 1.2em;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
    max-height: 2.4em;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.2;
    max-height: 3.6em;
}

/* Card Body Flex Layout */
.card-body {
    min-height: 200px;
    display: flex;
    flex-direction: column;
}

.card-body .mt-3 {
    margin-top: auto !important;
}

/* Stock Status Badges - Red and Black Theme */
.badge-success {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.badge-warning {
    background: linear-gradient(135deg, #fd7e14, #e8590c);
    color: white;
    box-shadow: 0 2px 8px rgba(253, 126, 20, 0.3);
}

.badge-danger {
    background: linear-gradient(135deg, #000000, #343a40);
    color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

/* Price Tag with Peso Symbol - Red Theme */
.price-tag {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

/* Color Selection Styles - Red and Black Theme */
.color-selection-container {
    max-height: 60px;
    overflow: hidden;
}

.color-options {
    max-height: 40px;
    overflow: hidden;
}

.color-option {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    border-radius: 50%;
    padding: 2px;
    flex-shrink: 0;
}

.color-option:hover {
    border-color: #dc3545;
    transform: scale(1.1);
}

.color-option.selected {
    border-color: #dc3545;
    background: #f8d7da;
    transform: scale(1.1);
}

/* Add to Cart Button Styles - Red and Black Theme */
.btn-primary {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.btn-outline-primary {
    border: 2px solid #dc3545;
    color: #dc3545;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
}

/* Card Hover Effects */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Responsive Design */
@media (max-width: 768px) {
    .product-img-holder {
        height: 150px;
    }
    
    .color-option {
        width: 25px;
        height: 25px;
    }
}

/* Horizontal Categories Styles */
.horizontal-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-start;
    margin: 0;
    padding: 0;
    min-height: 32px;
}

.category-filter-item {
    flex: 0 0 auto;
    white-space: nowrap;
    margin: 0;
    padding: 4px 8px;
    display: inline-block;
    position: relative;
}

.category-filter-item .custom-control {
    display: inline-flex;
    align-items: center;
    margin-bottom: 0;
    padding: 0;
    white-space: nowrap;
    position: relative;
}

.category-filter-item .custom-control-label {
    margin-left: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    color: #495057;
    cursor: pointer;
    transition: color 0.3s ease;
    line-height: 1.2;
    white-space: nowrap;
    display: inline-block;
    padding-right: 4px;
}

.category-filter-item .custom-control-label:hover {
    color: #dc3545;
}

.category-filter-item .custom-control-input:checked + .custom-control-label {
    color: #dc3545;
    font-weight: 600;
}

.category-filter-item .custom-control-input:focus + .custom-control-label::before {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.category-filter-item .custom-control-input:checked + .custom-control-label::before {
    background-color: #dc3545;
    border-color: #dc3545;
}

/* Categories Filter Container (No Card) */
.categories-filter-container {
    padding: 0.5rem 0;
    background: transparent;
    border: none;
}

.filter-title {
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.4rem;
    color: #6c757d;
}

/* Search Container (No Card) */
.search-container {
    padding: 0.5rem 0;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    min-height: 32px;
}

.search-container form {
    width: 100%;
    margin: 0;
}

.search-container .input-group {
    margin: 0;
}

/* Responsive horizontal categories */
@media (max-width: 992px) {
    .horizontal-categories {
        gap: 16px;
    }
    
    .category-filter-item {
        padding: 3px 6px;
    }
    
    .category-filter-item .custom-control-label {
        font-size: 0.75rem;
        padding-right: 3px;
    }
    
    .categories-filter-container {
        padding: 0.4rem 0;
    }
    
    .search-container {
        padding: 0.4rem 0;
    }
}

@media (max-width: 768px) {
    .horizontal-categories {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        min-height: auto;
    }
    
    .category-filter-item {
        width: 100%;
        padding: 2px 4px;
    }
    
    .category-filter-item .custom-control {
        width: 100%;
        justify-content: flex-start;
    }
    
    .categories-filter-container {
        padding: 0.3rem 0;
    }
    
    .filter-title {
        font-size: 0.75rem;
        margin-bottom: 0.3rem;
    }
    
    .search-container {
        padding: 0.3rem 0;
    }
}

@media (max-width: 576px) {
    .horizontal-categories {
        gap: 6px;
    }
    
    .category-filter-item {
        padding: 1px 3px;
    }
    
    .category-filter-item .custom-control-label {
        font-size: 0.7rem;
        padding-right: 2px;
    }
    
    .categories-filter-container {
        padding: 0.2rem 0;
    }
    
    .filter-title {
        font-size: 0.7rem;
        margin-bottom: 0.2rem;
    }
    
    .search-container {
        padding: 0.2rem 0;
    }
}

/* Compare Bar Styles */
.compare-bar{
    position:fixed;
    bottom:15px;
    left:50%;
    transform:translateX(-50%);
    z-index:9999;
    background:#ffffff;
    border:1px solid #ddd;
    border-radius:30px;
    padding:8px 12px;
    box-shadow:0 2px 8px rgba(0,0,0,.1);
}

.compare-bar .item{
    display:inline-block;
    margin:0 6px;
    padding:2px 8px;
    border:1px solid #ccc;
    border-radius:16px;
    font-size:.9em;
    background:#f9f9f9;
}
</style>
<div id="compareBar" class="compare-bar d-none">
    <span class="mr-2">Compare:</span>
    <span id="compareItems"></span>
    <button id="compareBtn" class="btn btn-sm btn-primary ml-2">Compare</button>
    <button id="clearCompare" class="btn btn-sm btn-link">Clear</button>
    <input type="hidden" id="compareIds" value="">
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
        var productCard = $(this).closest('.card');
        var colorOptions = productCard.find('.color-option');
        
        // Remove selected class from all options
        colorOptions.removeClass('selected');
        
        // Add selected class to clicked option
        $(this).addClass('selected');
        
        // Update main product image if color has different image
        var newImage = $(this).data('image');
        if(newImage) {
            var mainImage = productCard.find('.img-top');
            mainImage.attr('src', newImage);
        }
    });
});
</script>