<?php 
require_once('config.php');

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category_filter']) ? explode(",",$_GET['category_filter']) : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
<body>
<?php require_once('inc/topBarNav.php') ?>
 <!-- Header-->
 <header class="bg-dark py-5" id="main-header">
    <div class="container h-100 d-flex align-items-end justify-content-center w-100">
        <div class="text-center text-white w-100">
            <h1 class="display-4 fw-bolder"><?php echo $_settings->info('name') ?></h1>
            <p class="lead fw-normal text-white-50 mb-0">We will take care of your vehicle</p>
            <div class="col-auto mt-2">
                <?php if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2): ?>
                    <a href="./?p=send_request" class="btn btn-primary btn-lg rounded-0" id="send_request">Send Service Request</a>
                <?php else: ?>
                    <button class="btn btn-primary btn-lg rounded-0" id="send_request" type="button" onclick="if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' != 1){ Swal.fire({ title: 'Login Required', text: 'Please login first to send service request.', icon: 'warning', confirmButtonText: 'Login Now', showCancelButton: true, cancelButtonText: 'Cancel' }).then((result) => { if (result.isConfirmed) { location.href = './login.php'; } }); return false; } uni_modal('Fill the Service Request Form','send_request.php','large');">Send Service Request</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
<!-- Section-->
<style>
/* Service Type Filter Styles */
.filter-container {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 20px;
}

.filter-header {
    padding: 15px 20px;
    background: #e9ecef;
    border-radius: 8px 8px 0 0;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.filter-header:hover {
    background: #dee2e6;
}

.filter-title {
    margin: 0;
    color: #495057;
    font-weight: 600;
}

.filter-toggle {
    transition: transform 0.3s ease;
    font-size: 14px;
}

.filter-header[aria-expanded="true"] .filter-toggle {
    transform: rotate(180deg);
}

.filter-content {
    padding: 20px;
}

.horizontal-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

.category-filter-item {
    flex-shrink: 0;
}

.category-filter-item .custom-control {
    margin-bottom: 0;
}

.category-filter-item .custom-control-label {
    font-size: 14px;
    padding-left: 25px;
    cursor: pointer;
    white-space: nowrap;
}

.category-filter-item .custom-control-input:checked ~ .custom-control-label {
    color: #007bff;
    font-weight: 500;
}

/* Skeleton Loading Animation */
@keyframes skeleton-loading {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .horizontal-categories {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .filter-header {
        padding: 12px 15px;
    }
    
    .filter-content {
        padding: 15px;
    }
    
    /* Mobile skeleton improvements */
    .skeleton-line {
        height: 16px !important;
        margin-bottom: 6px !important;
    }
    
    .skeleton-line:first-child {
        height: 18px !important;
    }
}
</style>
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Our Services</h3>
                <hr class="bg-primary opacity-100">
                
                <!-- Search and Filter Section -->
                <div class="row mb-4">
                    <!-- Search Bar -->
                    <div class="col-12 mb-3">
                        <div class="search-container">
                            <form action="" id="search_services">
                                <div class="input-group">
                                    <input type="search" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search Services...">
                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Service Type Filter -->
                    <!-- <div class="col-12">
                        <div class="filter-container">
                            <div class="filter-header" data-toggle="collapse" data-target="#serviceTypeFilter" aria-expanded="false" aria-controls="serviceTypeFilter">
                                <h6 class="filter-title mb-0">
                                    <i class="fa fa-filter"></i> Filter by Service Type
                                    <i class="fa fa-chevron-down filter-toggle"></i>
                                </h6>
                            </div>
                            <div class="collapse" id="serviceTypeFilter">
                                <div class="filter-content">
                                    <div class="horizontal-categories">
                                        <div class="category-filter-item">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="type_all" value="all" <?= !is_array($category_filter) && $category_filter =='all' ? 'checked' : '' ?>>
                                                <label for="type_all" class="custom-control-label">All</label>
                                            </div>
                                        </div>
                                        <?php 
                                            // Get service types from service_list table
                                            $types = $conn->query("SELECT DISTINCT service_type FROM `service_list` WHERE status = 1 AND delete_flag = 0 AND service_type IS NOT NULL AND service_type != '' ORDER BY service_type ASC");
                                            while($row = $types->fetch_assoc()):
                                        ?>
                                            <div class="category-filter-item">
                                                <div class="custom-control custom-checkbox">
                                                    <input class="custom-control-input type_filter" type="checkbox" id="type_<?= md5($row['service_type']) ?>" value="<?= htmlspecialchars($row['service_type']) ?>" <?= ((is_array($category_filter) && in_array($row['service_type'],$category_filter)) || (!is_array($category_filter) && $category_filter =='all')) ? 'checked' : '' ?>>
                                                    <label for="type_<?= md5($row['service_type']) ?>" class="custom-control-label"><?= htmlspecialchars($row['service_type']) ?></label>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                   
                </div>
                
                <!-- Loading Skeleton -->
                <div id="services-skeleton" class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-xl-2" style="display: none;">
                    <?php for($i = 0; $i < 6; $i++): ?>
                    <div class="col item mb-3">
                        <div class="callout callout-primary border-primary rounded-0">
                            <div class="skeleton-line" style="height: 20px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; margin-bottom: 10px; border-radius: 4px;"></div>
                            <div class="skeleton-line" style="height: 15px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; margin-bottom: 8px; border-radius: 4px; width: 85%;"></div>
                            <div class="skeleton-line" style="height: 15px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; margin-bottom: 8px; border-radius: 4px; width: 70%;"></div>
                            <div class="skeleton-line" style="height: 15px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: skeleton-loading 1.5s infinite; border-radius: 4px; width: 60%;"></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <!-- Services Container -->
                <div id="services-container" class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-xl-2" style="min-height: 200px;">
                    <?php 
                    // Build WHERE clause for filtering
                    $where = "WHERE status = 1 AND delete_flag = 0";
                    
                    // Add service type filter
                    if(is_array($category_filter) && !in_array('all', $category_filter)){
                        $type_conditions = [];
                        foreach($category_filter as $type){
                            $type_conditions[] = "service_type = '" . $conn->real_escape_string($type) . "'";
                        }
                        $where .= " AND (" . implode(' OR ', $type_conditions) . ")";
                    }
                    
                    // Add search filter
                    if(!empty($search)){
                        $search_term = $conn->real_escape_string($search);
                        $where .= " AND (service LIKE '%{$search_term}%' OR description LIKE '%{$search_term}%' OR service_type LIKE '%{$search_term}%')";
                    }
                    
                    $services = $conn->query("SELECT * FROM `service_list` {$where} ORDER BY `service`");
                    $service_count = 0;
                    while($row= $services->fetch_assoc()):
                        $service_count++;
                        $row['description'] = strip_tags(html_entity_decode(stripslashes($row['description'])));
                        
                        // Format estimated time display
                        $estimated_time = '';
                        if(isset($row['estimated_hours']) && $row['estimated_hours'] > 0) {
                            $hours = floor($row['estimated_hours']);
                            $minutes = ($row['estimated_hours'] - $hours) * 60;
                            
                            if($hours > 0 && $minutes > 0) {
                                $estimated_time = $hours . 'h ' . round($minutes) . 'm';
                            } elseif($hours > 0) {
                                $estimated_time = $hours . 'h';
                            } else {
                                $estimated_time = round($minutes) . 'm';
                            }
                        } elseif(isset($row['min_minutes']) && isset($row['max_minutes']) && $row['min_minutes'] > 0) {
                            // Use min/max minutes if available
                            $min_hours = floor($row['min_minutes'] / 60);
                            $min_mins = $row['min_minutes'] % 60;
                            $max_hours = floor($row['max_minutes'] / 60);
                            $max_mins = $row['max_minutes'] % 60;
                            
                            $min_time = '';
                            $max_time = '';
                            
                            if($min_hours > 0 && $min_mins > 0) {
                                $min_time = $min_hours . 'h ' . $min_mins . 'm';
                            } elseif($min_hours > 0) {
                                $min_time = $min_hours . 'h';
                            } else {
                                $min_time = $min_mins . 'm';
                            }
                            
                            if($max_hours > 0 && $max_mins > 0) {
                                $max_time = $max_hours . 'h ' . $max_mins . 'm';
                            } elseif($max_hours > 0) {
                                $max_time = $max_hours . 'h';
                            } else {
                                $max_time = $max_mins . 'm';
                            }
                            
                            $estimated_time = $min_time . ' - ' . $max_time;
                        }
                    ?>
                    <a class="col item text-decoration-none text-dark view_service" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
                        <div class="callout callout-primary border-primary rounded-0">
                            <dl>
                                <dt><?php echo $row['service'] ?></dt>
                                <dd class="truncate-3 text-muted lh-1"><small><?php echo $row['description'] ?></small></dd>
                                <?php if(!empty($estimated_time)): ?>
                                <dd class="mt-2">
                                    <span class="badge badge-info">
                                        <i class="fa fa-clock-o"></i> Est. Time: <?php echo $estimated_time ?>
                                    </span>
                                </dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </a>
                    <?php endwhile; ?>
                </div>
                <div id="noResult" style="display:none" class="text-center"><b>No Result</b></div>
                <?php if($service_count == 0): ?>
                <div class="alert alert-warning text-center">
                    <h5>No Services Available</h5>
                    <p>There are currently no services available. Please check back later.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<style>
    #services-container .item {
        display: block !important;
        visibility: visible !important;
    }
    .callout {
        height: auto !important;
        min-height: 120px;
    }
    
    /* Search and Filter Styling */
    .search-container {
        margin-bottom: 1rem;
    }
    
    .categories-filter-container {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .filter-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 10px;
    }
    
    .horizontal-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .category-filter-item {
        margin-right: 15px;
        margin-bottom: 5px;
    }
    
    .custom-control-label {
        font-size: 0.9rem;
        color: #495057;
        cursor: pointer;
    }
    
    .custom-control-input:checked ~ .custom-control-label {
        color: #007bff;
        font-weight: 500;
    }
    
    /* Skeleton Loading Animation */
    .skeleton-line {
        animation: skeleton-loading 1.5s ease-in-out infinite;
    }
    
    @keyframes skeleton-loading {
        0% {
            background-color: #e0e0e0;
        }
        50% {
            background-color: #f0f0f0;
        }
        100% {
            background-color: #e0e0e0;
        }
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .horizontal-categories {
            flex-direction: column;
        }
        
        .category-filter-item {
            margin-right: 0;
        }
    }
</style>
<script>
    $(function(){
        console.log('Services page loaded');
        console.log('Send request button exists:', $('#send_request').length > 0);
        console.log('Service items count:', $('.view_service').length);
        console.log('Total service items:', $('.item').length);
        console.log('Visible service items:', $('.item:visible').length);
        
        // Search form submission
        $('#search_services').submit(function(e){
            e.preventDefault();
            showSkeleton();
            var search = $(this).serialize();
            setTimeout(function() {
                location.href="./?p=services"+(search != '' ? "&"+search : "")+"<?= isset($_GET['category_filter']) ? "&category_filter=".$_GET['category_filter'] : "" ?>";
            }, 300);
        });
        
        // Service type filter change
        $('.type_filter').change(function(){
            showSkeleton();
            var type_ids = [];
            if($('.type_filter').length == $('.type_filter:checked').length){
                $('#type_all').prop("checked",true);
            }else{
                $('#type_all').prop("checked",false);
                $('.type_filter:checked').each(function(){
                    type_ids.push($(this).val());
                });
                type_ids = type_ids.join(",");
            }
            
            setTimeout(function() {
                location.href="./?p=services"+(type_ids.length > 0 ? "&category_filter="+type_ids : "")+"<?= isset($_GET['search']) ? "&search=".$_GET['search'] : "" ?>";
            }, 300);
        });
        
        // "All" service type filter
        $('#type_all').change(function(){
            if($(this).is(':checked')){
                $('.type_filter').prop('checked', false);
                showSkeleton();
                setTimeout(function() {
                    location.href="./?p=services<?= isset($_GET['search']) ? "&search=".$_GET['search'] : "" ?>";
                }, 300);
            }
        });
        
        // Show skeleton loading
        function showSkeleton(){
            $('#services-container').hide();
            $('#services-skeleton').show();
        }
        
        // Hide skeleton and show results
        function hideSkeleton(){
            $('#services-skeleton').hide();
            $('#services-container').show();
        }
        
        // Hide skeleton on page load
        $(document).ready(function(){
            hideSkeleton();
        });
        
        $('#services-container .item').hover(function(){
            $(this).find('.callout').addClass('shadow')
        })
        $('#services-container .view_service').click(function(){
            console.log('Service clicked, ID:', $(this).attr('data-id'));
            uni_modal("Service Details","view_service.php?id="+$(this).attr('data-id'),'mid-large')
        })
        
        // Handle collapsible filter toggle
        $('.filter-header').on('click', function(){
            var target = $(this).data('target');
            var isExpanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !isExpanded);
        });
        
        // URL parameter utility function
        $.urlParam = function(name){
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results == null ? null : results[1] || '';
        }
        
        // Initialize filter state based on URL parameters
        if($.urlParam('category_filter') && $.urlParam('category_filter') !== 'all'){
            $('#serviceTypeFilter').addClass('show');
            $('.filter-header').attr('aria-expanded', 'true');
        }

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

<!-- Modal Structure -->
<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog rounded-0 modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<?php require_once('inc/footer.php') ?>
</body>
</html>