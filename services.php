 <!-- Header-->
 <header class="bg-dark py-5" id="main-header">
    <div class="container h-100 d-flex align-items-end justify-content-center w-100">
        <div class="text-center text-white w-100">
            <h1 class="display-4 fw-bolder"><?php echo $_settings->info('name') ?></h1>
            <p class="lead fw-normal text-white-50 mb-0">We will take care of your vehicle</p>
            <div class="col-auto mt-2">
                <button class="btn btn-primary btn-lg rounded-0" id="book_appointment" type="button">Book Appointment</button>
            </div>
        </div>
    </div>
</header>
<!-- Section-->
<section class="py-5">
    <div class="container-fluid px-4 px-lg-5 mt-5">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">Our Services</h2>
                <p class="text-center text-muted mb-4">Professional motorcycle maintenance and repair services</p>
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
                    // Display only popular/main services (limit to 12 services)
                    $services = $conn->query("SELECT * FROM `service_list` where status = 1 and delete_flag = 0 order by `service` LIMIT 12");
                    while($row= $services->fetch_assoc()):
                        $row['description'] = strip_tags(html_entity_decode(stripslashes($row['description'])));
                    ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="card h-100 service-card item shadow-sm border-0" data-id="<?php echo $row['id'] ?>">
                            <div class="card-body d-flex flex-column">
                                <div class="service-icon mb-3">
                                    <i class="fa fa-tools fa-2x"></i>
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
                    <button class="btn btn-outline-primary btn-lg" id="view_all_services">
                        <i class="fa fa-list me-2"></i>View All Services
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
/* Red and Black Theme for Services */
.service-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border: 2px solid #333;
    color: white;
}

.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(220, 53, 69, 0.4) !important;
    background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
    border-color: #dc3545;
}

.service-card .card-body {
    padding: 2rem;
}

.service-icon {
    text-align: center;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border-radius: 50%;
    width: 80px;
    height: 80px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
}

.service-icon i {
    color: white;
    font-size: 2rem;
}

.service-card .card-title {
    color: #dc3545 !important;
    font-weight: 700;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.service-card .card-text {
    color: #e9ecef !important;
}

.service-card .btn {
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.service-card .btn-primary {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.service-card .btn-primary:hover {
    background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

.service-card .btn-outline-primary {
    border: 2px solid #dc3545;
    color: #dc3545;
    background: transparent;
}

.service-card .btn-outline-primary:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.badge {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
    border: none;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

#search {
    border-radius: 25px;
    border: 2px solid #dc3545;
    padding: 0.75rem 1.5rem;
    background: #1a1a1a;
    color: white;
    transition: all 0.3s ease;
}

#search:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    background: #2d2d2d;
}

#search::placeholder {
    color: #6c757d;
}

.input-group .btn {
    border-radius: 0 25px 25px 0;
    border: 2px solid #dc3545;
    border-left: none;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
    font-weight: 600;
}

.input-group .btn:hover {
    background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
    transform: translateY(-1px);
}

/* Header styling */
#main-header {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%) !important;
    border-bottom: 3px solid #dc3545;
}

#main-header .btn-primary {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

#main-header .btn-primary:hover {
    background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
}

/* Section styling */
section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

h2 {
    color: #dc3545;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
}

hr.bg-primary {
    background: linear-gradient(90deg, #dc3545 0%, #c82333 100%) !important;
    height: 3px;
    border-radius: 2px;
}

/* View all services button */
#view_all_services {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    border: none;
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

#view_all_services:hover {
    background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

/* No results styling */
#noResult {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    color: white;
    border-radius: 15px;
    border: 2px solid #dc3545;
}

#noResult i {
    color: #dc3545;
}

#noResult h4, #noResult p {
    color: #e9ecef;
}
</style>

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
        
        // Book appointment button (general)
        $('#book_appointment').click(function(){
            var isLoggedIn = <?= ($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2) ? 'true' : 'false' ?>;
            if(isLoggedIn){
                uni_modal("Book Appointment","send_request.php",'mid-large');
            } else {
                alert_toast("Please Login First.","warning");
            }
        })
        
        // View all services
        $('#view_all_services').click(function(){
            location.href = './?p=all_services';
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