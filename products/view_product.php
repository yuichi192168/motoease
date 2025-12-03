<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT p.*, b.name as brand,c.category from `product_list` p inner join brand_list b on p.brand_id = b.id inner join categories c on p.category_id = c.id where p.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
        $stock_levels = get_product_stock_levels($conn, $id);
        $available = (int)($stock_levels['available_stock'] ?? 0);
        $current_stock = (int)($stock_levels['current_stock'] ?? 0);
        $reserved_orders = (int)($stock_levels['reserved_orders'] ?? 0);
        
        // Load multi-compatibility models if available
        $compat_models = [];
        if($id){
            $cm_rs = $conn->query("SELECT model_name FROM product_compatibility WHERE product_id = '{$id}' ORDER BY model_name ASC");
            if($cm_rs){
                while($cm = $cm_rs->fetch_assoc()) $compat_models[] = $cm['model_name'];
            }
        }
        
        // Load installment plans for pricing
        $installment_plans = [];
        $plans_query = $conn->query("SELECT * FROM installment_plans WHERE status = 'active' ORDER BY number_of_installments ASC");
        if($plans_query){
            while($plan = $plans_query->fetch_assoc()){
                $installment_plans[] = $plan;
            }
        }

        // Predefined motorcycle-specific installment data (use when product matches)
        $motorcycle_installments = [
            'pcx160 (cbs)' => [
                'srp' => 132800,
                'rows' => [
                    ['dp'=>10000,'12'=>14594,'18'=>10881,'24'=>8969,'30'=>7877,'36'=>7136,'48'=>null],
                    ['dp'=>13300,'12'=>14091,'18'=>10367,'24'=>8460,'30'=>7385,'36'=>6653,'48'=>5807],
                    ['dp'=>20000,'12'=>13265,'18'=>9738,'24'=>7865,'30'=>6842,'36'=>6143,'48'=>5333],
                    ['dp'=>26600,'12'=>12506,'18'=>9146,'24'=>7344,'30'=>6375,'36'=>5712,'48'=>4940],
                    ['dp'=>39900,'12'=>11039,'18'=>8079,'24'=>6492,'30'=>5639,'36'=>5055,'48'=>4375],
                ]
            ],
            'rs125' => [
                'srp' => 74800,
                'rows' => [
                    ['dp'=>3800,'12'=>9144,'18'=>6862,'24'=>5690,'30'=>5004,'36'=>4523,'48'=>null],
                    ['dp'=>5700,'12'=>8563,'18'=>6406,'24'=>5295,'30'=>4661,'36'=>4230,'48'=>null],
                    ['dp'=>7500,'12'=>8284,'18'=>6117,'24'=>5007,'30'=>4382,'36'=>3955,'48'=>3463],
                    ['dp'=>11300,'12'=>7815,'18'=>5759,'24'=>4667,'30'=>4071,'36'=>3664,'48'=>3191],
                    ['dp'=>15000,'12'=>7388,'18'=>5425,'24'=>4373,'30'=>3807,'36'=>3420,'48'=>2969],
                ]
            ],
            'air blade150' => [
                'srp' => 110800,
                'rows' => [
                    ['dp'=>8400,'12'=>12303,'18'=>9181,'24'=>7573,'30'=>6655,'36'=>6032,'48'=>null],
                    ['dp'=>11100,'12'=>11889,'18'=>8755,'24'=>7150,'30'=>6246,'36'=>5630,'48'=>4918],
                    ['dp'=>16700,'12'=>11198,'18'=>8229,'24'=>6652,'30'=>5791,'36'=>5203,'48'=>4521],
                    ['dp'=>22200,'12'=>10565,'18'=>7734,'24'=>6217,'30'=>5401,'36'=>4843,'48'=>4193],
                    ['dp'=>33300,'12'=>9341,'18'=>6844,'24'=>5506,'30'=>4786,'36'=>4294,'48'=>3721],
                ]
            ],
            'supra gtr150' => [
                'srp' => 107800,
                'rows' => [
                    ['dp'=>8100,'12'=>11999,'18'=>8956,'24'=>7389,'30'=>6494,'36'=>5886,'48'=>null],
                    ['dp'=>10800,'12'=>11588,'18'=>8535,'24'=>6972,'30'=>6090,'36'=>5490,'48'=>4797],
                    ['dp'=>16200,'12'=>10921,'18'=>8027,'24'=>6490,'30'=>5650,'36'=>5077,'48'=>4412],
                    ['dp'=>21600,'12'=>10300,'18'=>7542,'24'=>6063,'30'=>5268,'36'=>4724,'48'=>4091],
                    ['dp'=>32400,'12'=>9109,'18'=>6676,'24'=>5372,'30'=>4670,'36'=>4190,'48'=>3632],
                ]
            ],
        ];
        // build normalized lookup map (strip non-alphanumeric, lowercase) for more robust matching
        $motorcycle_installments_map = [];
        foreach($motorcycle_installments as $mk => $mv){
            $norm = preg_replace('/[^a-z0-9]/','',strtolower($mk));
            $motorcycle_installments_map[$norm] = $mv;
        }
        
        // Load motorcycle specifications
        $specifications = null;
        $specs_query = $conn->query("SELECT * FROM motorcycle_specifications WHERE product_id = '{$id}' LIMIT 1");
        if($specs_query && $specs_query->num_rows > 0){
            $specifications = $specs_query->fetch_assoc();
        }
        
        // Load all available colors from product_color_images
        $all_colors = [];
        $colors_query = $conn->query("SELECT color, image_path FROM product_color_images WHERE product_id = '{$id}' ORDER BY color ASC");
        if($colors_query){
            while($color_row = $colors_query->fetch_assoc()){
                $all_colors[] = $color_row;
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
    
    /* Installment Options Styling */
    .installment-section {
        width: 100%;
    }

    .installment-options {
        max-height: 400px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .installment-option {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        border-left: 4px solid #dc3545 !important;
    }
    
    .installment-option:hover {
        background-color: #e9ecef;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.2);
    }

    .installment-option .row {
        margin: 0;
    }

    .installment-option .col-12,
    .installment-option .col-sm-6 {
        padding: 0;
    }

    .installment-option small {
        line-height: 1.4;
    }

    @media (max-width: 575.98px) {
        .installment-options {
            max-height: none;
        }

        .installment-option {
            margin-bottom: 1rem !important;
        }

        .installment-option .text-sm-right {
            text-align: left;
        }
    }

    @media (min-width: 576px) {
        .installment-option .text-sm-right {
            text-align: right;
        }
    }
    
    /* Color Badge Styling */
    .color-badge {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .color-badge:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #dc3545 !important;
    }
    
    /* Specifications Table Styling */
    .specifications-table-container {
        overflow-x: auto;
    }
    
    .specifications-table-container table {
        font-size: 0.9rem;
    }
    
    .specifications-table-container th {
        background-color: #dc3545;
        color: white;
        font-weight: 600;
        padding: 12px;
    }
    
    .specifications-table-container td {
        padding: 12px;
        vertical-align: middle;
    }
    
    .specifications-table-container tr:nth-child(even) {
        background-color: #f8f9fa;
    }
    
    .specifications-table-container tr:hover {
        background-color: #e9ecef;
    }
    
    /* Responsive Design Improvements */
    @media (max-width: 768px) {
        .product-img {
            width: 100%;
            height: auto;
            max-height: 300px;
        }
        
        .installment-options {
            max-height: none;
        }
        
        .installment-option {
            font-size: 0.85rem;
            padding: 0.75rem !important;
        }

        .installment-option .row {
            flex-direction: column;
        }
        
        .color-badge {
            min-width: 80px !important;
        }
        
        .color-badge img {
            width: 50px !important;
            height: 50px !important;
        }
        
        .specifications-table-container table {
            font-size: 0.8rem;
        }
        
        .specifications-table-container th,
        .specifications-table-container td {
            padding: 8px;
        }
        
        .pl-4 {
            padding-left: 0.5rem !important;
        }
        
        .mx-2 {
            margin-left: 0.5rem !important;
            margin-right: 0.5rem !important;
        }
    }
    
    /* Remove empty space from conditional sections */
    .row:empty,
    .row:has(> div:empty) {
        display: none;
    }
    
    /* Compact spacing */
    .card-body .row {
        margin-bottom: 1rem;
    }
    
    .card-body .row:last-child {
        margin-bottom: 0;
    }
    
    /* Remove extra padding on mobile */
    @media (max-width: 768px) {
        .card-body .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .card-body .row {
            margin-bottom: 0.75rem;
        }
        
        h3, h5 {
            font-size: 1.25rem;
        }
        
        .stock-status {
            font-size: 0.8em;
            padding: 4px 8px;
        }
    }
    
    /* Ensure no empty rows show */
    .row:empty {
        display: none !important;
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
                <div class="container-fluid px-0">
                    <div class="row">
                        <div class="col-12">
                            <style>
                                .color-carousel{
                                    position: relative; 
                                    overflow: hidden; 
                                    padding: 0 40px;
                                    margin-bottom: 1.5rem;
                                }
                                @media (max-width: 767.98px){ 
                                    .color-carousel{ 
                                        padding: 0 10px; 
                                        margin-bottom: 1rem;
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
                                .color-dots > * {
                                    margin: 0 4px 4px 0;
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
                            <!-- <div class="available-colors">
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
                            </div> -->
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
                        <div class="col-md-6 mb-2">
                            <small class="mx-2 text-muted d-block mb-1">Product Name</small>
                            <div class="pl-4"><strong><?= isset($name) ? htmlspecialchars($name) : '' ?></strong></div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="mx-2 text-muted d-block mb-1">Category</small>
                            <div class="pl-4"><?= isset($category) ? htmlspecialchars($category) : '' ?></div>
                        </div>
                    </div>
                    <?php if(!empty($compat_models) || (isset($models) && trim($models) !== '')): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="mx-2 text-muted d-block mb-1">Compatibility</small>
                            <div class="pl-4">
                                <?php if(!empty($compat_models)): ?>
                                    <?php foreach($compat_models as $m): ?>
                                        <span class="badge badge-secondary mr-1 mb-1"><?= htmlspecialchars($m) ?></span>
                                    <?php endforeach; ?>
                                <?php elseif(isset($models) && trim($models) !== ''): ?>
                                    <span class="badge badge-secondary"><?= htmlspecialchars($models) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <small class="mx-2 text-muted d-block mb-1">Cash Price</small>
                            <div class="pl-4">
                                <h3 class="text-primary mb-2">₱<?= number_format(isset($price) ? $price : 0,2) ?></h3>
                            </div>
                            
                            <!-- Installment Pricing -->
                            <?php if((!empty($installment_plans) || !empty($motorcycle_installments)) && isset($price) && $price > 0): ?>
                            <div class="installment-section mt-4">
                                <small class="text-muted d-block mb-3"><strong>Installment Options:</strong></small>
                                <div class="installment-options">
                                    <?php 
                                    // prefer predefined motorcycle-specific installments when available for this product name
                                    $cash_price = floatval($price);
                                    // normalize product name for matching (remove non-alphanumeric)
                                    $prod_key_norm = preg_replace('/[^a-z0-9]/','',strtolower($name ?? ''));
                                    if(isset($motorcycle_installments_map[$prod_key_norm])):
                                        $mc = $motorcycle_installments_map[$prod_key_norm];
                                        $srp = floatval($mc['srp']);
                                    ?>
                                    <div class="installment-option mb-3 p-3 border rounded">
                                        <div class="row mb-2">
                                            <!-- <div class="col-12">
                                                <strong class="d-block">SRP: ₱<?= number_format($srp,2) ?></strong>
                                            </div> -->
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Down Payment (₱)</th>
                                                        <th class="text-center">12 Months</th>
                                                        <th class="text-center">18 Months</th>
                                                        <th class="text-center">24 Months</th>
                                                        <th class="text-center">30 Months</th>
                                                        <th class="text-center">36 Months</th>
                                                        <th class="text-center">48 Months</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($mc['rows'] as $row): ?>
                                                    <tr>
                                                        <td>₱<?= number_format(floatval($row['dp']),2) ?></td>
                                                        <?php foreach(['12','18','24','30','36','48'] as $k): ?>
                                                            <td class="text-center"><?= (array_key_exists($k,$row) && $row[$k] !== null) ? '₱'.number_format(floatval($row[$k]),2) : 'N/A' ?></td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php
                                    else:
                                        // fallback to configured installment plans
                                        foreach($installment_plans as $plan): 
                                            // only allow these terms
                                            $allowed_terms = [12,18,24,30,36,48];
                                            $down_payment_pct = floatval($plan['down_payment_percentage'] ?? 20);
                                            $down_payment = ($cash_price * $down_payment_pct) / 100;
                                            $remaining = $cash_price - $down_payment;
                                            $num_months = intval($plan['number_of_installments'] ?? 12);
                                            // skip plans not in allowed terms
                                            if(!in_array($num_months, $allowed_terms)) continue;
                                            $interest_rate = floatval($plan['interest_rate'] ?? 0);
                                            
                                            // Calculate monthly payment
                                            if($interest_rate > 0){
                                                $monthly_rate = $interest_rate / 100 / 12;
                                                $monthly_payment = $remaining * ($monthly_rate * pow(1 + $monthly_rate, $num_months)) / (pow(1 + $monthly_rate, $num_months) - 1);
                                            } else {
                                                $monthly_payment = $remaining / $num_months;
                                            }
                                            $monthly_payment = round($monthly_payment, 2);
                                            $total_installment = $down_payment + ($monthly_payment * $num_months);
                                    ?>
                                    <div class="installment-option mb-3 p-3 border rounded">
                                        <div class="row align-items-center">
                                            <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                                <strong class="d-block"><?= htmlspecialchars($plan['plan_name'] ?? "{$num_months} Months") ?></strong>
                                                <small class="text-muted"><?= $num_months ?> months @ ₱<?= number_format($monthly_payment, 2) ?>/month</small>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="text-left text-sm-right">
                                                    <small class="text-muted d-block">Down: ₱<?= number_format($down_payment, 2) ?></small>
                                                    <small class="text-primary d-block"><strong>Total: ₱<?= number_format($total_installment, 2) ?></strong></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <small class="mx-2 text-muted d-block mb-1">Stock Availability</small>
                            <div class="pl-4">
                                <?php if($available > 10): ?>
                                    <span class="stock-status stock-available mb-2 d-inline-block">
                                        <i class="fa fa-check-circle"></i> In Stock
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted d-block">
                                            <strong>Available:</strong> <?= $available ?> units
                                        </small>
                                        <small class="text-muted d-block">
                                            <strong>Current Stock:</strong> <?= $current_stock ?> units
                                        </small>
                                        <small class="text-muted d-block">
                                            <strong>Reserved:</strong> <?= $reserved_orders ?> units
                                        </small>
                                    </div>
                                <?php elseif($available > 0): ?>
                                    <span class="stock-status stock-low mb-2 d-inline-block">
                                        <i class="fa fa-exclamation-triangle"></i> Low Stock
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted d-block">
                                            <strong>Available:</strong> <?= $available ?> units
                                        </small>
                                        <small class="text-muted d-block">
                                            <strong>Current Stock:</strong> <?= $current_stock ?> units
                                        </small>
                                        <small class="text-muted d-block">
                                            <strong>Reserved:</strong> <?= $reserved_orders ?> units
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <span class="stock-status stock-out mb-2 d-inline-block">
                                        <i class="fa fa-times-circle"></i> Out of Stock
                                    </span>
                                    <div class="mt-2">
                                        <small class="text-muted d-block">
                                            <strong>Current Stock:</strong> <?= $current_stock ?> units
                                        </small>
                                        <small class="text-muted d-block">
                                            <strong>Reserved:</strong> <?= $reserved_orders ?> units
                                        </small>
                                    </div>
                                    
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
                    
                    <!-- Available Colors Section -->
                    <?php if(!empty($all_colors) || (isset($available_colors) && trim($available_colors) !== '')): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="mx-2 text-muted d-block mb-1">Available Colors</small>
                            <div class="pl-4">
                                <?php if(!empty($all_colors)): ?>
                                    <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                                        <?php foreach($all_colors as $color_item): ?>
                                        <div class="color-badge p-2 border rounded text-center" style="min-width: 100px;">
                                            <?php if(!empty($color_item['image_path'])): ?>
                                            <img src="<?= validate_image($color_item['image_path']) ?>" alt="<?= htmlspecialchars($color_item['color']) ?>" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                                            <?php endif; ?>
                                            <div class="mt-1">
                                                <small class="text-muted"><?= htmlspecialchars($color_item['color']) ?></small>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php elseif(isset($available_colors) && trim($available_colors) !== ''): ?>
                                    <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                                        <?php 
                                        $color_list = explode(',', $available_colors);
                                        foreach($color_list as $color): 
                                            $color = trim($color);
                                            if($color !== ''):
                                        ?>
                                        <span class="badge badge-primary p-2" style="font-size: 0.9rem;"><?= htmlspecialchars($color) ?></span>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Product Description -->
                    <?php if(isset($description) && trim($description) !== ''): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <small class="mx-2 text-muted d-block mb-1">Product Description</small>
                            <div class="pl-4 specifications-content">
                                <?= html_entity_decode($description) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Full Product Specifications -->
                    <?php if($specifications): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="mb-3 mt-3"><i class="fa fa-cog"></i> Full Specifications</h5>
                            <div class="specifications-table-container">
                                <table class="table table-bordered table-striped mb-0">
                                    <tbody>
                                        <?php if(!empty($specifications['make'])): ?>
                                        <tr>
                                            <th style="width: 30%;">Make</th>
                                            <td><?= htmlspecialchars($specifications['make']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['model'])): ?>
                                        <tr>
                                            <th>Model</th>
                                            <td><?= htmlspecialchars($specifications['model']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['engine_type'])): ?>
                                        <tr>
                                            <th>Engine Type</th>
                                            <td><?= htmlspecialchars($specifications['engine_type']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['displacement'])): ?>
                                        <tr>
                                            <th>Displacement</th>
                                            <td><?= htmlspecialchars($specifications['displacement']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['maximum_power'])): ?>
                                        <tr>
                                            <th>Maximum Power</th>
                                            <td><?= htmlspecialchars($specifications['maximum_power']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['maximum_torque'])): ?>
                                        <tr>
                                            <th>Maximum Torque</th>
                                            <td><?= htmlspecialchars($specifications['maximum_torque']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['transmission']) || !empty($specifications['transmission_type'])): ?>
                                        <tr>
                                            <th>Transmission</th>
                                            <td><?= htmlspecialchars($specifications['transmission'] ?? $specifications['transmission_type'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['fuel_system'])): ?>
                                        <tr>
                                            <th>Fuel System</th>
                                            <td><?= htmlspecialchars($specifications['fuel_system']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['fuel_capacity']) || !empty($specifications['fuel_tank_capacity'])): ?>
                                        <tr>
                                            <th>Fuel Capacity</th>
                                            <td><?= htmlspecialchars($specifications['fuel_capacity'] ?? $specifications['fuel_tank_capacity'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['fuel_consumption'])): ?>
                                        <tr>
                                            <th>Fuel Consumption</th>
                                            <td><?= htmlspecialchars($specifications['fuel_consumption']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['compression_ratio'])): ?>
                                        <tr>
                                            <th>Compression Ratio</th>
                                            <td><?= htmlspecialchars($specifications['compression_ratio']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['bore_stroke'])): ?>
                                        <tr>
                                            <th>Bore x Stroke</th>
                                            <td><?= htmlspecialchars($specifications['bore_stroke']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['ignition_type']) || !empty($specifications['ignition_system'])): ?>
                                        <tr>
                                            <th>Ignition System</th>
                                            <td><?= htmlspecialchars($specifications['ignition_type'] ?? $specifications['ignition_system'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['starting_system'])): ?>
                                        <tr>
                                            <th>Starting System</th>
                                            <td><?= htmlspecialchars($specifications['starting_system']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['gear_shift_pattern'])): ?>
                                        <tr>
                                            <th>Gear Shift Pattern</th>
                                            <td><?= htmlspecialchars($specifications['gear_shift_pattern']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['brake_system_front']) || !empty($specifications['brake_type_front'])): ?>
                                        <tr>
                                            <th>Front Brake</th>
                                            <td><?= htmlspecialchars($specifications['brake_system_front'] ?? $specifications['brake_type_front'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['brake_system_rear']) || !empty($specifications['brake_type_rear'])): ?>
                                        <tr>
                                            <th>Rear Brake</th>
                                            <td><?= htmlspecialchars($specifications['brake_system_rear'] ?? $specifications['brake_type_rear'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['suspension_front'])): ?>
                                        <tr>
                                            <th>Front Suspension</th>
                                            <td><?= htmlspecialchars($specifications['suspension_front']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['suspension_rear'])): ?>
                                        <tr>
                                            <th>Rear Suspension</th>
                                            <td><?= htmlspecialchars($specifications['suspension_rear']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['front_tire']) || !empty($specifications['tire_size_front'])): ?>
                                        <tr>
                                            <th>Front Tire</th>
                                            <td><?= htmlspecialchars($specifications['front_tire'] ?? $specifications['tire_size_front'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['rear_tire']) || !empty($specifications['tire_size_rear'])): ?>
                                        <tr>
                                            <th>Rear Tire</th>
                                            <td><?= htmlspecialchars($specifications['rear_tire'] ?? $specifications['tire_size_rear'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['wheels_type']) || !empty($specifications['wheel_type'])): ?>
                                        <tr>
                                            <th>Wheel Type</th>
                                            <td><?= htmlspecialchars($specifications['wheels_type'] ?? $specifications['wheel_type'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['overall_dimensions']) || !empty($specifications['overall_dimensions_lwh'])): ?>
                                        <tr>
                                            <th>Overall Dimensions</th>
                                            <td><?= htmlspecialchars($specifications['overall_dimensions'] ?? $specifications['overall_dimensions_lwh'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['wheelbase'])): ?>
                                        <tr>
                                            <th>Wheelbase</th>
                                            <td><?= htmlspecialchars($specifications['wheelbase']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['seat_height'])): ?>
                                        <tr>
                                            <th>Seat Height</th>
                                            <td><?= htmlspecialchars($specifications['seat_height']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['ground_clearance']) || !empty($specifications['minimum_ground_clearance'])): ?>
                                        <tr>
                                            <th>Ground Clearance</th>
                                            <td><?= htmlspecialchars($specifications['ground_clearance'] ?? $specifications['minimum_ground_clearance'] ?? '') ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['curb_weight'])): ?>
                                        <tr>
                                            <th>Curb Weight</th>
                                            <td><?= htmlspecialchars($specifications['curb_weight']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['frame_type'])): ?>
                                        <tr>
                                            <th>Frame Type</th>
                                            <td><?= htmlspecialchars($specifications['frame_type']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['headlight'])): ?>
                                        <tr>
                                            <th>Headlight</th>
                                            <td><?= htmlspecialchars($specifications['headlight']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['taillight'])): ?>
                                        <tr>
                                            <th>Taillight</th>
                                            <td><?= htmlspecialchars($specifications['taillight']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['engine_oil_capacity'])): ?>
                                        <tr>
                                            <th>Engine Oil Capacity</th>
                                            <td><?= htmlspecialchars($specifications['engine_oil_capacity']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['battery_type'])): ?>
                                        <tr>
                                            <th>Battery Type</th>
                                            <td><?= htmlspecialchars($specifications['battery_type']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        
                                        <?php if(!empty($specifications['category'])): ?>
                                        <tr>
                                            <th>Category</th>
                                            <td><?= htmlspecialchars($specifications['category']) ?></td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
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
        
        // Only show quantity input for non-motorcycle products
        var quantityHtml = '';
        if (!isMotorcycle) {
            quantityHtml = `
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" class="form-control" value="1" min="1" max="<?= $available ?>" style="width: 100px; margin: 0 auto;">
                </div>
            `;
        }
        
        Swal.fire({
            title: 'Add to Cart',
            html: `
                <div class="text-center">
                    <h5>${productName}</h5>
                    <p class="text-muted">Price: ₱${parseFloat(productPrice || 0).toLocaleString()}</p>
                    <p class="text-muted">Available: <?= $available ?> units</p>
                    ${colorOptionsHtml}
                    ${quantityHtml}
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Add to Cart',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const color = document.getElementById('swal_color') ? document.getElementById('swal_color').value : '';
                let quantity = 1; // Default quantity for motorcycles
                
                // Only get quantity from input if it's not a motorcycle
                if (!isMotorcycle) {
                    quantity = document.getElementById('quantity').value;
                }
                
                if(availableColors.length > 0 && !color) {
                    Swal.showValidationMessage('Please choose a color');
                    return false;
                }
                
                if (!isMotorcycle && (quantity < 1 || quantity > <?= $available ?>)) {
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
                                <div class="form-group" id="quantity-section">
                                    <label for="quantity">Quantity:</label>
                                    <input type="number" id="quantity" class="form-control" value="1" min="1" max="<?= $available ?>" style="width: 100px; margin: 0 auto;">
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Add to Cart',
                        cancelButtonText: 'Cancel',
                        didOpen: () => {
                            // Check if this is a motorcycle product and hide quantity section
                            var category = '<?= isset($category) ? addslashes($category) : "" ?>'.toLowerCase();
                            var isMotorcycle = category.includes('motorcycle') || category.includes('bike');
                            
                            if (isMotorcycle) {
                                $('#quantity-section').hide();
                            }
                        },
                        preConfirm: () => {
                            // Check if this is a motorcycle product
                            var category = '<?= isset($category) ? addslashes($category) : "" ?>'.toLowerCase();
                            var isMotorcycle = category.includes('motorcycle') || category.includes('bike');
                            
                            let quantity = 1; // Default quantity for motorcycles
                            
                            // Only get quantity from input if it's not a motorcycle
                            if (!isMotorcycle) {
                                quantity = document.getElementById('quantity').value;
                            }
                            
                            <?php if(!empty($colors)): ?>
                            const color = document.getElementById('swal_color').value;
                            if(!color){
                                Swal.showValidationMessage('Please choose a color');
                                return false;
                            }
                            <?php endif; ?>
                            
                            if (!isMotorcycle && (quantity < 1 || quantity > <?= $available ?>)) {
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