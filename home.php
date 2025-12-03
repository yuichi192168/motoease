<!-- Hero Section -->
<header class="hero-section position-relative overflow-hidden" id="main-header">
   <div class="hero-overlay"></div>
   <div class="container h-100 d-flex align-items-center justify-content-center">
       <div class="text-center text-white w-100">
           <div class="hero-content">
               <h1 class="display-3 fw-bold mb-1 animate-fade-in">Star Honda Calamba</h1>
               <p class="lead mb-4 animate-slide-up">Your trusted partner for motorcycle sales and service</p>
               <div class="hero-buttons animate-slide-up">
                   <a class="btn btn-danger btn-lg me-3 px-4 py-3 rounded-pill shadow-lg" href="./?p=products">
                       <i class="fas fa-shopping-cart me-2"></i>Shop Now
                   </a>
                   <a class="btn btn-outline-light btn-lg px-4 py-3 rounded-pill" href="./?p=services">
                       <i class="fas fa-tools me-2"></i>Our Services
                   </a>
               </div>
           </div>
       </div>
   </div>
   <!-- <div class="hero-scroll-indicator">
       <div class="scroll-arrow">
           <i class="fas fa-chevron-down"></i>
       </div>
   </div> -->
</header>

<!-- Featured Motorcycles Section -->
<?php 
// Get most popular motorcycles - prioritize in demand and top selling
$featured_products_query = $conn->query("
    SELECT p.id as pid, p.*, b.name as brand, c.category,
           (SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.product_id = p.id) as total_sold,
           (SELECT COUNT(*) FROM cart_list cl WHERE cl.product_id = p.id) as in_cart_count,
           ((SELECT COALESCE(SUM(oi.quantity), 0) FROM order_items oi WHERE oi.product_id = p.id) + 
            (SELECT COUNT(*) FROM cart_list cl WHERE cl.product_id = p.id)) as demand_score
    FROM `product_list` p 
    INNER JOIN brand_list b ON p.brand_id = b.id 
    INNER JOIN `categories` c ON p.category_id = c.id 
    WHERE p.delete_flag = 0 AND p.status = 1
    ORDER BY demand_score DESC, total_sold DESC, in_cart_count DESC, p.id DESC
    LIMIT 8
");
if($featured_products_query->num_rows > 0):
    $featured_products = [];
    while($product = $featured_products_query->fetch_assoc()) {
        $stock_levels = get_product_stock_levels($conn, $product['id']);
        $product['available_stock'] = (int)($stock_levels['available_stock'] ?? 0);
        $featured_products[] = $product;
    }
?>
<section class="py-4 featured-motorcycles-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);" id="featured-motorcycles">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold text-danger mb-3">Most Popular Motorcycles</h2>
            <p class="lead text-muted mb-0">Top selling and in-demand models our customers love</p>
            <div class="title-underline mx-auto mt-3"></div>
        </div>
        
        <div class="row g-4">
            <?php foreach($featured_products as $row): 
                $available = $row['available_stock'];
                $is_top_selling = $row['total_sold'] > 0;
                $is_in_demand = $row['in_cart_count'] > 0 || $row['total_sold'] > 0;
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card rounded-0 shadow h-100 featured-product-card">
                    <div class="product-img-holder overflow-hidden position-relative">
                        <img src="<?= validate_image($row['image_path']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" class="img-top">
                        
                        <!-- Badges - Properly positioned to avoid overlap -->
                        <div class="product-badge-container product-badge-top-left">
                            <?php if($is_in_demand && $is_top_selling): ?>
                                <span class="badge badge-danger">
                                    <i class="fa fa-fire"></i> In Demand
                                </span>
                            <?php elseif($is_top_selling): ?>
                                <span class="badge badge-warning">
                                    <i class="fa fa-star"></i> Top Selling
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-badge-container product-badge-top-right">
                            <?php if($available > 10): ?>
                                <span class="badge badge-success">In Stock</span>
                            <?php elseif($available > 0): ?>
                                <span class="badge badge-warning">Low Stock</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Price Tag -->
                        <span class="position-absolute price-tag rounded-pill bg-success text-light px-3" style="bottom:6px; right:6px;">
                            <i class="fa fa-tags"></i> <b>₱<?= number_format($row['price'], 2) ?></b>
                        </span>
                    </div>
                    
                    <div class="card-body border-top d-flex flex-column">
                        <h4 class="card-title my-0 line-clamp-1"><b><?= htmlspecialchars($row['name']) ?></b></h4>
                        <small class="text-muted line-clamp-1"><?= htmlspecialchars($row['brand']) ?></small>
                        <small class="text-muted line-clamp-1"><?= htmlspecialchars($row['category']) ?></small>
                        <p class="m-0 line-clamp-2 flex-grow-1"><?= htmlspecialchars(strip_tags(html_entity_decode(substr($row['description'], 0, 100)))) ?>...</p>
                        
                                    <!-- Add to Cart Button -->
                                    <div class="mt-3">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <?php if($available > 0): ?>
                                                    <button class="btn btn-primary btn-sm w-100" onclick="if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' != 1){ Swal.fire({ title: 'Login Required', text: 'Please login first to add items to cart.', icon: 'warning', confirmButtonText: 'Login Now', showCancelButton: true, cancelButtonText: 'Cancel' }).then((result) => { if (result.isConfirmed) { location.href = './login.php'; } }); return false; } addToCart(<?= (int)$row['pid'] ?>);"><i class="fa fa-cart-plus"></i>
                                                        
                                                </button>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm w-100" onclick="showOutOfStockOptions(<?= (int)$row['pid'] ?>, '<?= htmlspecialchars($row['name']) ?>', '<?= htmlspecialchars($row['category']) ?>')">
                                                        <i class="fa fa-bell"></i> Notify
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-6">
                                                <a href="./?p=products/view_product&id=<?= (int)$row['pid'] ?>" class="btn btn-outline-primary btn-sm w-100" title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="./?p=products&sort=in_demand" class="btn btn-danger btn-lg px-5 rounded-pill">
                <i class="fas fa-motorcycle me-2"></i>View All Motorcycles
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Featured Services Section -->
<?php 
// Get most requested services - count service requests by service name match
$featured_services_query = $conn->query("
    SELECT s.*,
           (SELECT COUNT(*) FROM service_requests sr 
            WHERE sr.service_type LIKE CONCAT('%', s.service, '%') 
            OR sr.service_type = s.service) as request_count
    FROM `service_list` s
    WHERE s.status = 1 AND s.delete_flag = 0
    HAVING request_count > 0 OR s.service_amount IS NOT NULL
    ORDER BY request_count DESC, s.service_amount DESC, s.date_created DESC
    LIMIT 6
");
if($featured_services_query->num_rows > 0):
    $featured_services = [];
    while($service = $featured_services_query->fetch_assoc()) {
        $service['description'] = strip_tags(html_entity_decode(stripslashes($service['description'])));
        
        // Format estimated time
        $estimated_time = '';
        if(isset($service['estimated_hours']) && $service['estimated_hours'] > 0) {
            $hours = floor($service['estimated_hours']);
            $minutes = ($service['estimated_hours'] - $hours) * 60;
            if($hours > 0 && $minutes > 0) {
                $estimated_time = $hours . 'h ' . round($minutes) . 'm';
            } elseif($hours > 0) {
                $estimated_time = $hours . 'h';
            } else {
                $estimated_time = round($minutes) . 'm';
            }
        }
        $service['estimated_time'] = $estimated_time;
        $featured_services[] = $service;
    }
?>
<section class="py-5 featured-services-section" style="background: #ffffff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold text-danger mb-3">Most Requested Services</h2>
            <p class="lead text-muted mb-0">Popular services our customers trust us with</p>
            <div class="title-underline mx-auto mt-3"></div>
        </div>
        
        <div class="row g-4">
            <?php foreach($featured_services as $service): 
                $is_popular = $service['request_count'] > 0;
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card rounded-0 shadow h-100 featured-service-card" style="border-left: 4px solid #dc3545;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title mb-0">
                                <?= htmlspecialchars($service['service']) ?>
                            </h5>
                            <?php if($is_popular): ?>
                                <span class="badge badge-danger">
                                    <i class="fa fa-fire"></i> Popular
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if(!empty($service['service_type'])): ?>
                        <small class="text-muted d-block mb-2">
                            <i class="fa fa-tag"></i> <?= htmlspecialchars($service['service_type']) ?>
                        </small>
                        <?php endif; ?>
                        
                        <p class="card-text mb-3">
                            <?= htmlspecialchars(substr($service['description'], 0, 120)) ?><?= strlen($service['description']) > 120 ? '...' : '' ?>
                        </p>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <?php if(!empty($service['estimated_time'])): ?>
                            <small class="text-muted">
                                <i class="fa fa-clock"></i> Est. <?= $service['estimated_time'] ?>
                            </small>
                            <?php endif; ?>
                            
                            <?php if(!empty($service['service_amount'])): ?>
                            <span class="badge badge-success">
                                <i class="fa fa-tags"></i> ₱<?= number_format($service['service_amount'], 2) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn btn-danger btn-sm w-100 view_service" data-id="<?= (int)$service['id'] ?>">
                        <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="./?p=services" class="btn btn-danger btn-lg px-5 rounded-pill">
                <i class="fas fa-tools me-2"></i>View All Services
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Promo Images Section -->
<?php 
$promo_query = $conn->query("SELECT * FROM promo_images WHERE is_active = 1 ORDER BY display_order ASC, date_created DESC LIMIT 6");
if($promo_query->num_rows > 0):
$promos = [];
while($promo = $promo_query->fetch_assoc()) {
    $promos[] = $promo;
}
?>
<section class="py-5 promo-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold text-danger mb-3"><?php echo $_settings->info('promo_section_title') ?: 'Special Promotions' ?></h2>
            <p class="lead text-muted mb-0">Discover our exclusive offers and limited-time deals</p>
            <div class="title-underline mx-auto mt-3"></div>
        </div>
        
        <!-- Promo Images Display -->
        <div class="promo-images-container">
            <div class="row g-4">
                <?php foreach($promos as $promo): ?>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="promo-card h-100">
                        <div class="promo-image-container">
                            <img src="<?php echo validate_image($promo['image_path']) ?>" class="promo-image" alt="<?php echo htmlspecialchars($promo['title']) ?>">
                            <div class="promo-overlay">
                                <div class="promo-content">
                                    <h5 class="promo-title"><?php echo htmlspecialchars($promo['title']) ?></h5>
                                    <?php if(!empty($promo['description'])): ?>
                                    <p class="promo-description"><?php echo htmlspecialchars($promo['description']) ?></p>
                                    <?php endif; ?>
                                    <a href="https://www.facebook.com/share/p/1CcUsmdruW/" target="_blank" class="btn btn-light btn-sm rounded-pill">Learn More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Customer Purchase Images Infinite Carousel Section -->
<?php 
$customer_query = $conn->query("SELECT * FROM customer_purchase_images WHERE is_active = 1 ORDER BY display_order ASC, date_created DESC LIMIT 12");
if($customer_query->num_rows > 0):
$customers = [];
while($customer = $customer_query->fetch_assoc()) {
    $customers[] = $customer;
}
?>
<section class="py-5 customer-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold text-danger mb-3"><?php echo $_settings->info('customer_section_title') ?: 'Happy Customers' ?></h2>
            <p class="lead text-muted mb-0">See what our satisfied customers have to say</p>
            <div class="title-underline mx-auto mt-3"></div>
        </div>
        
        <!-- Infinite Carousel Container -->
        <div class="infinite-carousel-container">
            <div class="infinite-carousel-track" id="customerInfiniteCarousel">
                <!-- First set of customers -->
                <?php foreach($customers as $customer): ?>
                <div class="customer-card-wrapper">
                    <div class="customer-card">
                        <div class="customer-image-container">
                            <img src="<?php echo validate_image($customer['image_path']) ?>" class="customer-image" alt="<?php echo htmlspecialchars($customer['customer_name']) ?>">
                            <div class="customer-overlay">
                                <div class="customer-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="customer-content">
                            <h6 class="customer-name"><?php echo htmlspecialchars($customer['customer_name']) ?></h6>
                            <p class="customer-model"><?php echo htmlspecialchars($customer['motorcycle_model']) ?></p>
                            <?php if(!empty($customer['testimonial'])): ?>
                            <p class="customer-testimonial">"<?php echo htmlspecialchars(substr($customer['testimonial'], 0, 80)) ?><?php echo strlen($customer['testimonial']) > 80 ? '...' : '' ?>"</p>
                            <?php endif; ?>
                            <?php if(!empty($customer['purchase_date'])): ?>
                            <small class="customer-date"><?php echo date('M Y', strtotime($customer['purchase_date'])) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <!-- Duplicate set for infinite effect -->
                <?php foreach($customers as $customer): ?>
                <div class="customer-card-wrapper">
                    <div class="customer-card">
                        <div class="customer-image-container">
                            <img src="<?php echo validate_image($customer['image_path']) ?>" class="customer-image" alt="<?php echo htmlspecialchars($customer['customer_name']) ?>">
                            <div class="customer-overlay">
                                <div class="customer-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <div class="customer-content">
                            <h6 class="customer-name"><?php echo htmlspecialchars($customer['customer_name']) ?></h6>
                            <p class="customer-model"><?php echo htmlspecialchars($customer['motorcycle_model']) ?></p>
                            <?php if(!empty($customer['testimonial'])): ?>
                            <p class="customer-testimonial">"<?php echo htmlspecialchars(substr($customer['testimonial'], 0, 80)) ?><?php echo strlen($customer['testimonial']) > 80 ? '...' : '' ?>"</p>
                            <?php endif; ?>
                            <?php if(!empty($customer['purchase_date'])): ?>
                            <small class="customer-date"><?php echo date('M Y', strtotime($customer['purchase_date'])) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Carousel Controls -->
        <div class="carousel-controls">
            <button class="carousel-control-btn prev-btn" id="prevCustomer">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-control-btn next-btn" id="nextCustomer">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
  /* ============================================
       HOME PAGE CARD CONSISTENCY & RESPONSIVENESS
       ============================================ */
    
    /* Base card styling - consistent across all card types */
    .featured-product-card,
    .featured-service-card,
    .promo-card,
    .customer-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .featured-product-card:hover,
    .featured-service-card:hover,
    .promo-card:hover,
    .customer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    /* Consistent card body padding */
    .featured-product-card .card-body,
    .featured-service-card .card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    /* Consistent image container heights - matching products page */
    .featured-product-card .product-img-holder {
        height: 200px;
        background: #f8f9fa;
        position: relative;
        overflow: hidden;
    }
    
    .featured-product-card .product-img-holder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .featured-product-card:hover .product-img-holder img {
        transform: scale(1.05);
    }
    
    /* Consistent card titles */
    .featured-product-card .card-title,
    .featured-service-card .card-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        color: #212529;
    }
    
    /* Consistent card text */
    .featured-product-card .card-text,
    .featured-service-card .card-text {
        font-size: 0.875rem;
        color: #6c757d;
        line-height: 1.5;
        margin-bottom: 1rem;
        flex-grow: 1;
    }
    
    /* ============================================
       BADGE STYLING - RESPONSIVE & CONSISTENT
       ============================================ */
    
    /* Base badge styling - consistent sizing */
    .featured-product-card .badge,
    .featured-service-card .badge {
        font-size: 0.7rem;
        padding: 4px 8px;
        font-weight: 500;
        line-height: 1.2;
        border-radius: 4px;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .featured-product-card .badge i,
    .featured-service-card .badge i {
        font-size: 0.65rem;
    }
    
    /* Price badge - slightly larger but still proportional */
    .featured-product-card .badge.badge-success i.fa-tags {
        font-size: 0.75rem;
    }
    
    /* Price badge container - target by containing fa-tags icon */
    .featured-product-card .position-absolute:last-of-type .badge {
        font-size: 0.8rem;
        padding: 6px 10px;
    }
    
    /* Service price badge */
    .featured-service-card .badge.badge-success {
        font-size: 0.75rem;
        padding: 5px 9px;
    }
    
    /* Badge positioning - Prevent overlap with proper spacing */
    .product-badge-container {
        position: absolute;
        z-index: 2;
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
    }
    
    .product-badge-top-left {
        top: 8px;
        left: 8px;
        right: auto;
        bottom: auto;
    }
    
    .product-badge-top-right {
        top: 8px;
        left: auto;
        right: 8px;
        bottom: auto;
    }
    
    /* Ensure badges don't overlap - add min-width for top badges */
    .product-badge-top-left .badge,
    .product-badge-top-right .badge {
        max-width: calc(50% - 4px);
        min-width: fit-content;
    }
    
    /* ============================================
       BUTTON FIXES - CART ICON VISIBILITY
       ============================================ */
    
    /* Force all icons in buttons to be visible */
    .featured-product-card .btn-danger i,
    .featured-product-card .btn-outline-danger i,
    .featured-product-card .btn-outline-primary i,
    .featured-service-card .btn-danger i {
        display: inline-block !important;
        vertical-align: middle !important;
        opacity: 1 !important;
        visibility: visible !important;
        color: inherit !important;
    }
    
    /* Specific fix for cart icon */
    .featured-product-card .btn-danger .fa-cart-plus,
    .featured-product-card .btn-danger i.fa-cart-plus,
    .btn-danger i.fa-cart-plus {
        color: #ffffff !important;
    }
    
    /* Add to Cart Button Styles */
    .featured-product-card .btn-danger {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        border: none !important;
        color: #ffffff !important;
        transition: all 0.3s ease;
    }
    
    .featured-product-card .btn-danger:hover {
        background: linear-gradient(135deg, #c82333, #b21f2d) !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
    }
    
    .featured-product-card .btn-outline-danger {
        border: 2px solid #dc3545 !important;
        color: #dc3545 !important;
        transition: all 0.3s ease;
    }
    
    .featured-product-card .btn-outline-danger:hover {
        background: #dc3545 !important;
        color: white !important;
        transform: translateY(-2px);
    }
    
    .featured-product-card .btn-outline-primary {
        border: 2px solid #dc3545!important;
        color: #dc3545 !important;
        transition: all 0.3s ease;
    }
    
    .featured-product-card .btn-outline-primary:hover {
        background: #dc3545 !important;
        color: white !important;
        transform: translateY(-2px);
    }
    
    /* Stock Status Badges - Red Theme */
    .featured-product-card .badge-success {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        color: white !important;
    }
    
    .featured-product-card .badge-warning {
        background: linear-gradient(135deg, #fd7e14, #e8590c) !important;
        color: white !important;
    }
    
    .featured-product-card .badge-danger {
        background: linear-gradient(135deg, #000000, #343a40) !important;
        color: white !important;
    }
    
    /* Price Tag - Red Theme */
    .featured-product-card .price-tag {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        color: white !important;
        padding: 6px 12px !important;
        border-radius: 20px !important;
        font-size: 0.9rem !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
    }
    
    /* ============================================
       SKELETON LOADING ANIMATION
       ============================================ */
    
    .skeleton-img,
    .skeleton-title,
    .skeleton-subtitle,
    .skeleton-text,
    .skeleton-button,
    .skeleton-button-lg,
    .skeleton-badge {
        animation: skeleton-loading 1.5s infinite ease-in-out;
    }
    
    @keyframes skeleton-loading {
        0% {
            opacity: 0.6;
        }
        50% {
            opacity: 1;
        }
        100% {
            opacity: 0.6;
        }
    }
    
    /* ============================================
       RESPONSIVE DESIGN
       ============================================ */
    
    /* Desktop (992px and up) */
    @media (min-width: 992px) {
        .featured-product-card .product-img-holder {
            height: 200px;
        }
    }
    
    /* Tablet (768px - 991px) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .featured-product-card .product-img-holder {
            height: 180px;
        }
        
        .featured-product-card .card-body {
            padding: 1rem;
        }
        
        .featured-product-card .badge {
            font-size: 0.65rem;
            padding: 3px 7px;
        }
    }
    
    /* Mobile (below 768px) */
    @media (max-width: 767.98px) {
        .featured-product-card .product-img-holder {
            height: 150px;
        }
        
        .featured-product-card .card-body {
            padding: 0.875rem;
        }
        
        .featured-product-card .badge {
            font-size: 0.6rem;
            padding: 3px 6px;
        }
        
        .featured-product-card .price-tag {
            font-size: 0.75rem !important;
            padding: 5px 10px !important;
        }
        
        /* Button text smaller on mobile */
        .featured-product-card .btn-danger,
        .featured-product-card .btn-outline-danger,
        .featured-product-card .btn-outline-primary {
            font-size: 0.75rem !important;
            padding: 0.375rem 0.5rem !important;
        }
        
        .featured-product-card .btn-danger i,
        .featured-product-card .btn-outline-danger i,
        .featured-product-card .btn-outline-primary i {
            margin-right: 2px !important;
            font-size: 0.8rem !important;
        }
    }
    
    /* Small mobile (below 576px) */
    @media (max-width: 575.98px) {
        .featured-product-card .product-img-holder {
            height: 140px;
        }
        
        .featured-product-card .card-body {
            padding: 0.75rem;
        }
        
        .featured-product-card .badge {
            font-size: 0.55rem;
            padding: 2px 5px;
        }
        
        .featured-product-card .price-tag {
            font-size: 0.7rem !important;
            padding: 4px 8px !important;
        }
    }
    
    /* ============================================
       SPACING CONSISTENCY
       ============================================ */
    
    .title-underline {
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #dc3545, #ff6b6b);
        border-radius: 2px;
    }
    
    #featured-motorcycles {
        scroll-margin-top: 80px;
    }
    
    .featured-motorcycles-section {
        padding-top: 2rem;
        padding-bottom: 3rem;
    }
    
    @media (max-width: 767.98px) {
        #featured-motorcycles {
            scroll-margin-top: 60px;
        }
        
        .featured-motorcycles-section {
            padding-top: 1.5rem;
            padding-bottom: 2rem;
        }
    }
</style>

<script>
// Out of Stock Options Function - copied from products page
function showOutOfStockOptions(productId, productName, category) {
    if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" != 1) {
        Swal.fire({
            title: 'Login Required',
            text: 'Please login to set notifications for out-of-stock products.',
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
    
    // Show loading
    Swal.fire({
        title: 'Loading Alternatives...',
        text: 'Finding similar products for you',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Get alternative recommendations
    $.ajax({
        url: '<?= base_url ?>classes/Master.php?f=get_alternative_products',
        method: 'POST',
        data: {
            product_id: productId,
            category: category
        },
        dataType: 'json',
        timeout: 10000,
        success: function(resp) {
            Swal.close();
            if(resp.status === 'success' && resp.alternatives) {
                showOutOfStockModal(productId, productName, resp.alternatives);
            } else {
                showOutOfStockModal(productId, productName, []);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching alternatives:', error);
            Swal.close();
            showOutOfStockModal(productId, productName, []);
        }
    });
}

function showOutOfStockModal(productId, productName, alternatives) {
    var alternativesHtml = '';
    if(alternatives && alternatives.length > 0) {
        alternativesHtml = '<div class="text-left mt-3"><h6>Similar Products Available:</h6><div class="row">';
        alternatives.forEach(function(alt) {
            alternativesHtml += '<div class="col-6 mb-2">';
            alternativesHtml += '<div class="card border">';
            alternativesHtml += '<img src="' + alt.image_path + '" class="card-img-top" style="height: 80px; object-fit: cover;">';
            alternativesHtml += '<div class="card-body p-2">';
            alternativesHtml += '<h6 class="card-title mb-1" style="font-size: 0.8rem;">' + alt.name + '</h6>';
            alternativesHtml += '<p class="card-text mb-1" style="font-size: 0.7rem;">₱' + parseFloat(alt.price).toLocaleString() + '</p>';
            alternativesHtml += '<a href="./?p=products/view_product&id=' + alt.id + '" class="btn btn-sm btn-primary" style="font-size: 0.7rem;">View</a>';
            alternativesHtml += '</div></div></div>';
        });
        alternativesHtml += '</div></div>';
    } else {
        alternativesHtml = '<div class="text-center mt-3"><p class="text-muted">No similar products found at the moment.</p></div>';
    }
    
    requestAnimationFrame(function() {
        Swal.fire({
            title: 'Product Out of Stock',
            html: '<div class="text-center">' +
                  '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>' +
                  '<h5>' + productName + '</h5>' +
                  '<p class="text-muted">This product is currently out of stock.</p>' +
                  alternativesHtml +
                  '</div>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Notify Me When Available',
            cancelButtonText: 'Close',
            confirmButtonColor: '#dc3545',
            width: '600px',
            allowOutsideClick: true,
            allowEscapeKey: true,
            focusConfirm: false,
            focusCancel: false
        }).then((result) => {
            if (result.isConfirmed) {
                setProductNotification(productId, productName);
            }
        });
    });
}

function setProductNotification(productId, productName) {
    Swal.fire({
        title: 'Setting Notification...',
        text: 'Please wait',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '<?= base_url ?>classes/Master.php?f=set_product_notification',
        method: 'POST',
        data: {
            product_id: productId
        },
        dataType: 'json',
        timeout: 5000,
        success: function(resp) {
            Swal.close();
            requestAnimationFrame(function() {
                if(resp.status === 'success') {
                    Swal.fire({
                        title: 'Notification Set!',
                        text: 'You will be notified when ' + productName + ' becomes available.',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    alert_toast(resp.msg || 'Failed to set notification', 'error');
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('Notification error:', error);
            Swal.close();
            alert_toast('An error occurred while setting notification', 'error');
        }
    });
}
</script>

<script>
    $(function(){
        $('#search').on('input',function(){
            var _search = $(this).val().toLowerCase().trim()
            $('#service_list .item').each(function(){
                var _text = $(this).text().toLowerCase().trim()
                    _text = _text.replace(/\s+/g,' ')
                    console.log(_text)
                if((_text).includes(_search) == true){
                    $(this).toggle(true)
                }else{
                    $(this).toggle(false)
                }
            })
            if( $('#service_list .item:visible').length > 0){
                $('#noResult').hide('slow')
            }else{
                $('#noResult').show('slow')
            }
        })
        $('#service_list .item').hover(function(){
            $(this).find('.callout').addClass('shadow')
        })
        $('#service_list .view_service').click(function(){
            uni_modal("Service Details","view_service.php?id="+$(this).attr('data-id'),'mid-large')
        })
        
        // Handle featured services view button
        $('.featured-service-card .view_service').click(function(){
            uni_modal("Service Details","view_service.php?id="+$(this).attr('data-id'),'mid-large')
        })

    })
    
    // Simplified addToCart function for home page
    function addToCart(productId) {
        // Check if user is logged in
        var isLoggedIn = <?= ($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2) ? 'true' : 'false' ?>;
        
        if(!isLoggedIn) {
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
        
        // Get product details and add to cart
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_product_details",
            method: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(resp) {
                if(resp.status === 'success' && resp.data) {
                    var product = resp.data;
                    
                    // If product has colors, redirect to product page for selection
                    if(product.colors && product.colors.length > 0) {
                        location.href = './?p=products/view_product&id=' + productId;
                        return;
                    }
                    
                    // Otherwise, add directly to cart
                    $.ajax({
                        url: _base_url_ + "classes/Master.php?f=save_to_cart",
                        method: 'POST',
                        data: {
                            product_id: productId,
                            quantity: 1,
                            color: ''
                        },
                        dataType: 'json',
                        success: function(cartResp) {
                            if(cartResp.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: cartResp.msg || 'Product added to cart',
                                    icon: 'success',
                                    confirmButtonText: 'Continue Shopping',
                                    showCancelButton: true,
                                    cancelButtonText: 'View Cart'
                                }).then((result) => {
                                    if (!result.isConfirmed) {
                                        location.href = './?p=cart';
                                    }
                                });
                            } else {
                                alert_toast(cartResp.msg || 'Failed to add product to cart', 'error');
                            }
                        },
                        error: function() {
                            alert_toast('An error occurred', 'error');
                        }
                    });
                } else {
                    // Fallback: redirect to product page
                    location.href = './?p=products/view_product&id=' + productId;
                }
            },
            error: function() {
                // Fallback: redirect to product page
                location.href = './?p=products/view_product&id=' + productId;
            }
        });
    }
    $(document).scroll(function() { 
        $('#topNavBar').removeClass('bg-transparent navbar-dark bg-primary')
        if($(window).scrollTop() === 0) {
           $('#topNavBar').addClass('navbar-dark bg-transparent')
        }else{
           $('#topNavBar').addClass('navbar-dark bg-primary')
        }
    });
    $(function(){
        $(document).trigger('scroll')
    })
    
    // Infinite Customer Carousel
    $(document).ready(function() {
        const carouselTrack = document.getElementById('customerInfiniteCarousel');
        const prevBtn = document.getElementById('prevCustomer');
        const nextBtn = document.getElementById('nextCustomer');
        
        if (carouselTrack) {
            let isAnimating = false;
            let currentPosition = 0;
            const cardWidth = 300; // Width of each customer card
            const visibleCards = 4; // Number of visible cards
            const totalCards = carouselTrack.children.length / 2; // Half because we duplicate
            const maxPosition = -(totalCards * cardWidth);
            
            // Auto-scroll function (right to left)
            function autoScroll() {
                if (!isAnimating) {
                    currentPosition += 1; // Move right to left
                    if (currentPosition >= 0) {
                        currentPosition = maxPosition;
                    }
                    carouselTrack.style.transform = `translateX(${currentPosition}px)`;
                }
            }
            
            // Start auto-scroll
            let autoScrollInterval = setInterval(autoScroll, 50);
            
            // Pause on hover
            carouselTrack.addEventListener('mouseenter', function() {
                clearInterval(autoScrollInterval);
            });
            
            // Resume on mouse leave
            carouselTrack.addEventListener('mouseleave', function() {
                autoScrollInterval = setInterval(autoScroll, 50);
            });
            
            // Manual controls
            if (prevBtn && nextBtn) {
                prevBtn.addEventListener('click', function() {
                    if (!isAnimating) {
                        isAnimating = true;
                        currentPosition += cardWidth;
                        if (currentPosition > 0) {
                            currentPosition = maxPosition;
                        }
                        carouselTrack.style.transition = 'transform 0.5s ease-in-out';
                        carouselTrack.style.transform = `translateX(${currentPosition}px)`;
                        
                        setTimeout(() => {
                            isAnimating = false;
                        }, 500);
                    }
                });
                
                nextBtn.addEventListener('click', function() {
                    if (!isAnimating) {
                        isAnimating = true;
                        currentPosition -= cardWidth;
                        if (currentPosition <= maxPosition) {
                            currentPosition = 0;
                        }
                        carouselTrack.style.transition = 'transform 0.5s ease-in-out';
                        carouselTrack.style.transform = `translateX(${currentPosition}px)`;
                        
                        setTimeout(() => {
                            isAnimating = false;
                        }, 500);
                    }
                });
            }
        }
        
        // Smooth scroll for hero section - scroll to featured motorcycles
        $('.hero-scroll-indicator').click(function() {
            $('html, body').animate({
                scrollTop: $('#featured-motorcycles').offset().top - 80
            }, 1000);
        });
        
        // Animate elements on scroll
        function animateOnScroll() {
            $('.animate-fade-in, .animate-slide-up').each(function() {
                const elementTop = $(this).offset().top;
                const elementBottom = elementTop + $(this).outerHeight();
                const viewportTop = $(window).scrollTop();
                const viewportBottom = viewportTop + $(window).height();
                
                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('animated');
                }
            });
        }
        
        $(window).on('scroll', animateOnScroll);
        animateOnScroll(); // Run on page load
        
        // Ensure featured motorcycles section is visible on page load
        $(document).ready(function() {
            // Check if featured motorcycles section is below viewport
            var featuredSection = $('#featured-motorcycles');
            if (featuredSection.length) {
                var windowHeight = $(window).height();
                var heroHeight = $('.hero-section').outerHeight() || windowHeight * 0.5;
                var featuredTop = featuredSection.offset().top;
                
                // If featured section is below viewport, scroll slightly to show it
                if (featuredTop > windowHeight - 100) {
                    // Only scroll if it's significantly below (more than 100px)
                    // Otherwise, it should already be visible with 50vh hero
                }
            }
        });
    });
</script>