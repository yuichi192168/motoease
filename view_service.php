<?php 
require_once('./config.php');
$qry = $conn->query("SELECT * FROM `service_list` where id = '{$_GET['id']}'");
foreach($qry->fetch_array() as $k => $v){
    $$k = $v;
}
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container-fluid">
    <dl>
        <dt><?php echo $service ?></dt>
        <dd><?php echo html_entity_decode(stripslashes($description)) ?></dd>
        <?php 
        // Format estimated time display
        $estimated_time = '';
        if(isset($estimated_hours) && $estimated_hours > 0) {
            $hours = floor($estimated_hours);
            $minutes = ($estimated_hours - $hours) * 60;
            
            if($hours > 0 && $minutes > 0) {
                $estimated_time = $hours . 'h ' . round($minutes) . 'm';
            } elseif($hours > 0) {
                $estimated_time = $hours . 'h';
            } else {
                $estimated_time = round($minutes) . 'm';
            }
        } elseif(isset($min_minutes) && isset($max_minutes) && $min_minutes > 0) {
            // Use min/max minutes if available
            $min_hours = floor($min_minutes / 60);
            $min_mins = $min_minutes % 60;
            $max_hours = floor($max_minutes / 60);
            $max_mins = $max_minutes % 60;
            
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
        
        if(!empty($estimated_time)): ?>
        <dd class="mt-2">
            <span class="badge badge-info">
                <i class="fa fa-clock-o"></i> Estimated Time: <?php echo $estimated_time ?>
            </span>
        </dd>
        <?php endif; ?>
    </dl>
    <hr>
    <style>
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
    </style>
    <div class="card card-outline rounded-0 card-secondary">
        <div class="card-header py-2">
            <strong>Customer Reviews</strong>
        </div>
        <div class="card-body">
            <div id="svc_reviews_summary" class="mb-2 text-muted"></div>
            <div id="svc_reviews_list" class="mb-3"></div>
            <div class="review-form">
                <div class="form-group mb-2">
                    <label class="mb-1">Rating <span class="text-danger">*</span></label>
                    <div id="svc_rating_stars" class="rating-stars-container">
                        <span class="star" data-val="1" title="1 star - Poor">★</span>
                        <span class="star" data-val="2" title="2 stars - Fair">★</span>
                        <span class="star" data-val="3" title="3 stars - Good">★</span>
                        <span class="star" data-val="4" title="4 stars - Very Good">★</span>
                        <span class="star" data-val="5" title="5 stars - Excellent">★</span>
                    </div>
                    <small class="text-muted">Click on a star to rate this service</small>
                    <div class="rating-error" id="svc_rating_error">Please select a rating before submitting your review.</div>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1">Comment</label>
                    <textarea id="svc_review_comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                </div>
                <button onclick="if('<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>' != 1){ Swal.fire({ title: 'Login Required', text: 'Please login first to submit a review.', icon: 'warning', confirmButtonText: 'Login Now', showCancelButton: true, cancelButtonText: 'Cancel' }).then((result) => { if (result.isConfirmed) { location.href = './login.php'; } }); return false; } submitSvcReviewForm();" class="btn btn-sm btn-primary">Submit Review</button>
            </div>
        </div>
    </div>
    <div class="w-100 d-flex justify-content-end mx-2">
        <div class="col-auto">
            <button class="btn btn-dark btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
$(function(){
    loadSvcReviews();
    let svcSelected = 0;
    let isHovering = false;
    
    // Enhanced star rating functionality
    $('#svc_rating_stars .star').on('mouseenter', function(){
        isHovering = true;
        const v = parseInt($(this).data('val'));
        svcHighlight(v, true);
    }).on('mouseleave', function(){
        isHovering = false;
        svcHighlight(svcSelected, false);
    }).on('click', function(){
        svcSelected = parseInt($(this).data('val'));
        svcHighlight(svcSelected, false);
        hideSvcRatingError();
    });
    
    function svcHighlight(v, isHover = false){
        $('#svc_rating_stars .star').each(function(){
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
    
    function showSvcRatingError() {
        $('#svc_rating_error').show();
    }
    
    function hideSvcRatingError() {
        $('#svc_rating_error').hide();
    }
    
    // Initialize with no rating selected
    svcHighlight(0, false);

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

    // Function to handle service review submission (called from onclick)
    function submitSvcReviewForm(){
        // Clear previous validation errors
        hideSvcRatingError();
        $('#svc_review_comment').removeClass('is-invalid');
        
        // Validate rating selection
        if(svcSelected < 1){
            showSvcRatingError();
            return;
        }
        
        // Validate comment (optional but if provided, should not be empty)
        const comment = $('#svc_review_comment').val().trim();
        if(comment.length > 0 && comment.length < 10){
            $('#svc_review_comment').addClass('is-invalid');
            alert_toast('Please provide a more detailed comment (at least 10 characters) or leave it empty.', 'warning');
            return;
        }
        
        // Submit the review
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=save_review",
            method:'POST',
            data:{ target_type:'service', target_id:'<?= isset($id)?$id:"" ?>', rating: svcSelected, comment: comment },
            dataType:'json',
            error:err=>{ console.error(err); alert_toast('An error occurred','error'); end_loader(); },
            success:function(resp){
                if(resp.status=='success'){
                    $('#svc_review_comment').val(''); 
                    svcSelected = 0; 
                    svcHighlight(0, false); 
                    loadSvcReviews(); 
                    alert_toast(resp.msg,'success');
                }else{ 
                    alert_toast(resp.msg||'An error occurred','error'); 
                }
                end_loader();
            }
        });
    }
});

function loadSvcReviews(){
    $.ajax({
        url:_base_url_+"classes/Master.php?f=get_reviews",
        method:'POST',
        data:{ target_type:'service', target_id:'<?= isset($id)?$id:"" ?>', limit:10, offset:0 },
        dataType:'json',
        success:function(resp){
            if(resp.status=='success'){
                let s = resp.count + ' review' + (resp.count==1?'':'s');
                if(resp.count>0){ s += ' • Avg ' + (resp.avg_rating||0) + '/5'; }
                $('#svc_reviews_summary').text(s);
                if(resp.reviews.length){
                    var html='';
                    $.each(resp.reviews, function(_, r){
                        const name = r.reviewer_name ? r.reviewer_name : 'Customer';
                        const stars = '★★★★★'.slice(0, r.rating) + '☆☆☆☆☆'.slice(0, 5 - r.rating);
                        const ratingText = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][r.rating] || '';
                        const formattedDate = new Date(r.date_created).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });
                        
                        html += '<div class="mb-3 p-3 border rounded shadow-sm" style="background-color: #f8f9fa; border-left: 4px solid #28a745;">';
                        html += '<div class="d-flex justify-content-between align-items-start mb-2">';
                        html += '<div>';
                        html += '<strong class="text-success">'+name+'</strong>';
                        html += '<div class="mt-1">';
                        html += '<span class="text-warning h5" style="font-size: 1.2rem;">'+stars+'</span>';
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
                    });
                    $('#svc_reviews_list').html(html);
                }else{
                    $('#svc_reviews_list').html('<p class="text-muted">No reviews yet. Be the first to review this service.</p>');
                }
            }
        }
    });
}
</script>