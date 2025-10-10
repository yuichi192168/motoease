<!-- Hero Section -->
<header class="hero-section position-relative overflow-hidden" id="main-header">
   <div class="hero-overlay"></div>
   <div class="container h-100 d-flex align-items-center justify-content-center">
       <div class="text-center text-white w-100">
           <div class="hero-content">
               <h1 class="display-3 fw-bold mb-4 animate-fade-in"><?php echo $_settings->info('name') ?></h1>
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
   <div class="hero-scroll-indicator">
       <div class="scroll-arrow">
           <i class="fas fa-chevron-down"></i>
       </div>
   </div>
</header>

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
        
        <!-- Promo Carousel -->
        <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-indicators promo-indicators">
                <?php for($i = 0; $i < ceil(count($promos) / 3); $i++): ?>
                <button type="button" data-bs-target="#promoCarousel" data-bs-slide-to="<?= $i ?>" <?= $i === 0 ? 'class="active" aria-current="true"' : '' ?> aria-label="Promo Slide <?= $i + 1 ?>"></button>
                <?php endfor; ?>
            </div>
            
            <div class="carousel-inner">
                <?php 
                $slide_count = 0;
                for($i = 0; $i < count($promos); $i += 3): 
                    $is_active = $slide_count === 0 ? 'active' : '';
                ?>
                <div class="carousel-item <?= $is_active ?>">
                    <div class="row g-4">
                        <?php 
                        $end_index = min($i + 3, count($promos));
                        for($j = $i; $j < $end_index; $j++): 
                            $promo = $promos[$j];
                        ?>
                        <div class="col-lg-4 col-md-6">
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
                        <?php endfor; ?>
                    </div>
                </div>
                <?php 
                $slide_count++;
                endfor; 
                ?>
            </div>
            
            <!-- Carousel Controls -->
            <button class="carousel-control-prev promo-control" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next promo-control" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
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

<script>
    $(function(){
        $('#search').on('input',function(){
            var _search = $(this).val().toLowerCase().trim();
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
        $('#send_request').click(function(){
            uni_modal("Fill the Service Request Form","send_request.php",'large')
        })

    })
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
            
            // Auto-scroll function
            function autoScroll() {
                if (!isAnimating) {
                    currentPosition -= 1;
                    if (currentPosition <= maxPosition) {
                        currentPosition = 0;
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
        
        // Promo Carousel functionality
        const promoCarousel = document.getElementById('promoCarousel');
        if (promoCarousel) {
            promoCarousel.addEventListener('mouseenter', function() {
                const bsCarousel = bootstrap.Carousel.getInstance(promoCarousel);
                if (bsCarousel) {
                    bsCarousel.pause();
                }
            });
            
            promoCarousel.addEventListener('mouseleave', function() {
                const bsCarousel = bootstrap.Carousel.getInstance(promoCarousel);
                if (bsCarousel) {
                    bsCarousel.cycle();
                }
            });
        }
        
        // Smooth scroll for hero section
        $('.hero-scroll-indicator').click(function() {
            $('html, body').animate({
                scrollTop: $('.promo-section').offset().top
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
    });
</script>