<?php require_once('config.php'); ?>
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
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">Our Services</h3>
                <hr class="bg-primary opacity-100">
                <div class="form-group">
                <div class="input-group mb-3">
                    <input type="search" id="search" class="form-control" placeholder="Search Service Here" aria-label="Search Service Here" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <span class="input-group-text bg-primary" id="basic-addon2"><i class="fa fa-search"></i></span>
                    </div>
                </div>
                </div>
                <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-xl-2" id="service_list" style="min-height: 200px;">
                    <?php 
                    $services = $conn->query("SELECT * FROM `service_list` where status = 1 and delete_flag = 0 order by `service`");
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
    #service_list .item {
        display: block !important;
        visibility: visible !important;
    }
    .callout {
        height: auto !important;
        min-height: 120px;
    }
</style>
<script>
    $(function(){
        console.log('Services page loaded');
        console.log('Send request button exists:', $('#send_request').length > 0);
        console.log('Service items count:', $('.view_service').length);
        console.log('Total service items:', $('.item').length);
        console.log('Visible service items:', $('.item:visible').length);
        
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
            console.log('Service clicked, ID:', $(this).attr('data-id'));
            uni_modal("Service Details","view_service.php?id="+$(this).attr('data-id'),'mid-large')
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