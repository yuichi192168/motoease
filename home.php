 <!-- Header-->
 <header class="bg-dark py-5" id="main-header">
    <div class="container h-100 d-flex align-items-end justify-content-center w-100">
        <div class="text-center text-white w-100">
            <h1 class="display-4 fw-bolder mx-5"><?php echo $_settings->info('name') ?></h1>
            <div class="col-auto mt-2">
                <a class="btn btn-primary btn-lg rounded-0" href="./?p=products">Shop Now</a>
            </div>
        </div>
    </div>
</header>

<!-- Promo Images Section -->
<?php 
$promo_query = $conn->query("SELECT * FROM promo_images WHERE is_active = 1 ORDER BY display_order ASC, date_created DESC LIMIT 5");
if($promo_query->num_rows > 0):
?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary"><?php echo $_settings->info('promo_section_title') ?: 'Special Promotions' ?></h2>
            <p class="lead text-muted">Check out our latest offers and promotions</p>
        </div>
        <div class="row">
            <?php while($promo = $promo_query->fetch_assoc()): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-img-top overflow-hidden" style="height: 250px;">
                        <img src="<?php echo validate_image($promo['image_path']) ?>" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($promo['title']) ?>">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?php echo htmlspecialchars($promo['title']) ?></h5>
                        <?php if(!empty($promo['description'])): ?>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($promo['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<!-- Section-->
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row row-cols-sm-1 row-cols-md-2 row-cols-xl-4">
            <?php 
                $products = $conn->query("SELECT p.*,b.name as brand, c.category FROM `product_list` p inner join brand_list b on p.brand_id = b.id inner join `categories` c on p.category_id = c.id where p.delete_flag = 0 and p.status = 1 order by RAND() limit 4");
                while($row= $products->fetch_assoc()):
            ?>
                <a class="col px-1 py-2 text-decoration-none text-dark product-item" href ="./?p=products/view_product&id=<?= $row['id'] ?>">
                    <div class="card rounded-0 shadow">
                        <div class="product-img-holder overflow-hidden position-relative">
                            <img src="<?= validate_image($row['image_path']) ?>" alt="Product Image" class="img-top"/>
                            <span class="position-absolute price-tag rounded-pill bg-success elevation-1 text-light px-3">
                                <i class="fa fa-tags"></i> <b><?= number_format($row['price'],2) ?></b>
                            </span>
                        </div>
                        <div class="card-body border-top">
                            <h4 class="card-title my-0"><b><?= $row['name'] ?></b></h4><br>
                            <small class="text-muted"><?= $row['brand'] ?></small><br>
                            <small class="text-muted"><?= $row['category'] ?></small>
                            <p class="m-0 truncate-5"><?= strip_tags(html_entity_decode($row['description'])) ?></p>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Customer Purchase Images Section -->
<?php 
$customer_query = $conn->query("SELECT * FROM customer_purchase_images WHERE is_active = 1 ORDER BY display_order ASC, date_created DESC LIMIT 8");
if($customer_query->num_rows > 0):
?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary"><?php echo $_settings->info('customer_section_title') ?: 'Happy Customers' ?></h2>
            <p class="lead text-muted">See what our satisfied customers have to say</p>
        </div>
        <div class="row">
            <?php while($customer = $customer_query->fetch_assoc()): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-img-top overflow-hidden" style="height: 200px;">
                        <img src="<?php echo validate_image($customer['image_path']) ?>" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="<?php echo htmlspecialchars($customer['customer_name']) ?>">
                    </div>
                    <div class="card-body text-center">
                        <h6 class="card-title text-primary mb-1"><?php echo htmlspecialchars($customer['customer_name']) ?></h6>
                        <small class="text-muted d-block mb-2"><?php echo htmlspecialchars($customer['motorcycle_model']) ?></small>
                        <?php if(!empty($customer['testimonial'])): ?>
                        <p class="card-text small text-muted">"<?php echo htmlspecialchars(substr($customer['testimonial'], 0, 100)) ?><?php echo strlen($customer['testimonial']) > 100 ? '...' : '' ?>"</p>
                        <?php endif; ?>
                        <?php if(!empty($customer['purchase_date'])): ?>
                        <small class="text-muted"><?php echo date('M Y', strtotime($customer['purchase_date'])) ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php endif; ?>

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
</script>