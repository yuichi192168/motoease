<?php 
require_once('config.php');
if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<div class='alert alert-danger'>Service ID is required.</div>";
    exit;
}
$qry = $conn->query("SELECT * FROM `service_list` where id = '{$_GET['id']}'");
if($qry->num_rows == 0){
    echo "<div class='alert alert-danger'>Service not found.</div>";
    exit;
}
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
    </dl>
    <hr>
    <div class="card card-outline rounded-0 card-secondary">
        <div class="card-header py-2">
            <strong>Customer Reviews</strong>
        </div>
        <div class="card-body">
            <div id="svc_reviews_summary" class="mb-2 text-muted"></div>
            <div id="svc_reviews_list" class="mb-3"></div>
            <div class="review-form">
                <div class="form-group mb-2">
                    <label class="mb-1">Rating</label>
                    <div id="svc_rating_stars">
                        <span class="text-warning h5 mx-1 star" data-val="1">★</span>
                        <span class="text-warning h5 mx-1 star" data-val="2">★</span>
                        <span class="text-warning h5 mx-1 star" data-val="3">★</span>
                        <span class="text-warning h5 mx-1 star" data-val="4">★</span>
                        <span class="text-warning h5 mx-1 star" data-val="5">★</span>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label class="mb-1">Comment</label>
                    <textarea id="svc_review_comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                </div>
                <button id="svc_submit_review" class="btn btn-sm btn-primary">Submit Review</button>
            </div>
        </div>
    </div>
    <div class="w-100 d-flex justify-content-end mx-2">
        <div class="col-auto">
            <button class="btn btn-dark btn-sm rounded-0" type="button" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
<script>
$(function(){
    loadSvcReviews();
    let svcSelected = 0;
    $('#svc_rating_stars .star').on('mouseenter', function(){
        const v = parseInt($(this).data('val')); svcHighlight(v);
    }).on('mouseleave', function(){ svcHighlight(svcSelected); })
    .on('click', function(){ svcSelected = parseInt($(this).data('val')); svcHighlight(svcSelected); });
    function svcHighlight(v){
        $('#svc_rating_stars .star').each(function(){ const s = parseInt($(this).data('val')); $(this).css('opacity', s <= v ? 1 : 0.3); });
    }
    svcHighlight(0);

    $('#svc_submit_review').click(function(){
        if("<?= $_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2 ?>" != 1){
            alert_toast('Please login to submit a review.','warning');
            return;
        }
        if(svcSelected < 1){ alert_toast('Please select a rating.','warning'); return; }
        const comment = $('#svc_review_comment').val();
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=save_review",
            method:'POST',
            data:{ target_type:'service', target_id:'<?= isset($id)?$id:"" ?>', rating: svcSelected, comment: comment },
            dataType:'json',
            error:err=>{ console.error(err); alert_toast('An error occurred','error'); end_loader(); },
            success:function(resp){
                if(resp.status=='success'){
                    $('#svc_review_comment').val(''); svcSelected = 0; svcHighlight(0); loadSvcReviews(); alert_toast(resp.msg,'success');
                }else{ alert_toast(resp.msg||'An error occurred','error'); }
                end_loader();
            }
        });
    });
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
                        html += '<div class="mb-2">'+
                                '<div><strong>'+name+'</strong> <span class="text-warning">'+stars+'</span></div>'+
                                (r.comment?'<div class="text-muted">'+$('<div>').text(r.comment).html()+'</div>':'')+
                                '<div class="text-xs text-muted">'+ r.date_created +'</div>'+
                                '</div>';
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