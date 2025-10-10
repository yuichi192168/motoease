<?php 
require_once('config.php');
?>
<style>
.service-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.service-card .card-body {
    padding: 1.5rem;
}

.service-icon {
    text-align: center;
}

.service-card .btn {
    border-radius: 25px;
    font-weight: 500;
}

.service-card .btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
}

.service-card .btn-outline-primary {
    border: 2px solid #007bff;
    color: #007bff;
}

.service-card .btn-outline-primary:hover {
    background: #007bff;
    color: white;
}

.badge {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

#search {
    border-radius: 25px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1.5rem;
}

#search:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
}

.input-group .btn {
    border-radius: 0 25px 25px 0;
    border: 2px solid #007bff;
    border-left: none;
}
</style>

<!-- Header-->
<header class="bg-dark py-5" id="main-header">
    <div class="container h-100 d-flex align-items-end justify-content-center w-100">
        <div class="text-center text-white w-100">
            <h1 class="display-4 fw-bolder">All Services</h1>
            <p class="lead fw-normal text-white-50 mb-0">Complete list of our motorcycle services</p>
        </div>
    </div>
</header>

<!-- Section-->
<section class="py-5">
    <div class="container-fluid px-4 px-lg-5 mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">All Available Services</h2>
                <p class="text-center text-muted mb-4">Browse through our complete range of motorcycle maintenance and repair services</p>
                <hr class="bg-primary opacity-100 mb-4">
                
                <div class="row mb-4">
                    <div class="col-md-6 mx-auto">
                        <div class="input-group">
                            <input type="search" id="search" class="form-control form-control-lg" placeholder="Search services..." aria-label="Search services">
                            <button class="btn btn-primary" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4" id="service_list">
                    <?php 
                    // Display all services
                    $services = $conn->query("SELECT * FROM `service_list` where status = 1 and delete_flag = 0 order by `service`");
                    while($row= $services->fetch_assoc()):
                        $row['description'] = strip_tags(html_entity_decode(stripslashes($row['description'])));
                    ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 service-card item shadow-sm border-0" data-id="<?php echo $row['id'] ?>">
                            <div class="card-body d-flex flex-column">
                                <div class="service-icon mb-3">
                                    <i class="fa fa-tools text-primary fa-2x"></i>
                                </div>
                                <h5 class="card-title text-dark mb-3"><?php echo $row['service'] ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?php echo substr($row['description'], 0, 120) . (strlen($row['description']) > 120 ? '...' : ''); ?></p>
                                
                                <div class="mt-auto">
                                    <?php if(isset($row['estimated_hours']) && $row['estimated_hours'] > 0): ?>
                                    <div class="mb-3">
                                        <span class="badge bg-info text-white">
                                            <i class="fa fa-clock me-1"></i> Est. <?php echo round($row['estimated_hours'] * 60); ?> min
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm view_service" data-id="<?php echo $row['id'] ?>">
                                            <i class="fa fa-eye me-1"></i> View Details
                                        </button>
                                        <button class="btn btn-primary btn-sm book_service" data-id="<?php echo $row['id'] ?>">
                                            <i class="fa fa-calendar me-1"></i> Book This Service
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                
                <div id="noResult" style="display:none" class="text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No services found</h4>
                    <p class="text-muted">Try adjusting your search terms</p>
                </div>
                
                <div class="text-center mt-5">
                    <a href="./?p=services" class="btn btn-outline-primary btn-lg">
                        <i class="fa fa-arrow-left me-2"></i>Back to Main Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(function(){
        // Search functionality
        $('#search').on('input',function(){
            var _search = $(this).val().toLowerCase().trim();
            $('#service_list .item').each(function(){
                var _text = $(this).text().toLowerCase().trim()
                _text = _text.replace(/\s+/g,' ')
                
                if((_text).includes(_search) == true){
                    $(this).closest('.col-lg-4').show()
                }else{
                    $(this).closest('.col-lg-4').hide()
                }
            })
            
            if( $('#service_list .col-lg-4:visible').length > 0){
                $('#noResult').hide('slow')
            }else{
                $('#noResult').show('slow')
            }
        })
        
        // Service card hover effects
        $('.service-card').hover(
            function(){
                $(this).addClass('shadow-lg');
            },
            function(){
                $(this).removeClass('shadow-lg');
            }
        )
        
        // View service details
        $('.view_service').click(function(){
            var serviceId = $(this).attr('data-id');
            uni_modal("Service Details","view_service.php?id="+serviceId,'mid-large')
        })
        
        // Book specific service
        $('.book_service').click(function(){
            var serviceId = $(this).attr('data-id');
            var isLoggedIn = <?= ($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2) ? 'true' : 'false' ?>;
            if(isLoggedIn){
                uni_modal("Book Appointment","send_request.php?service_id="+serviceId,'mid-large');
            } else {
                alert_toast("Please Login First.","warning");
            }
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
