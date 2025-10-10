<?php
require_once('../config.php');
require_once('../inc/sess_auth.php');
?>
<style>
    .recommendation-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fff;
    }
    .recommendation-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
<div class="content py-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Product Recommendations Management</h3>
                        <div class="card-tools">
                            <button class="btn btn-primary btn-sm" onclick="addRecommendation()">
                                <i class="fa fa-plus"></i> Add Recommendation
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Current Recommendations</h5>
                                <div id="recommendations_list">
                                    <!-- Recommendations will be loaded here -->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Add New Recommendation</h5>
                                <form id="recommendation_form">
                                    <input type="hidden" name="id">
                                    <div class="form-group">
                                        <label>Product</label>
                                        <select name="product_id" class="form-control" required>
                                            <option value="">Select Product</option>
                                            <?php 
                                            $products = $conn->query("SELECT id, name FROM product_list WHERE delete_flag = 0 AND status = 1 ORDER BY name");
                                            while($row = $products->fetch_assoc()):
                                            ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Recommended Product</label>
                                        <select name="recommended_product_id" class="form-control" required>
                                            <option value="">Select Recommended Product</option>
                                            <?php 
                                            $products = $conn->query("SELECT id, name FROM product_list WHERE delete_flag = 0 AND status = 1 ORDER BY name");
                                            while($row = $products->fetch_assoc()):
                                            ?>
                                            <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Recommendation Type</label>
                                        <select name="recommendation_type" class="form-control" required>
                                            <option value="alternative">Alternative</option>
                                            <option value="similar">Similar</option>
                                            <option value="upgrade">Upgrade</option>
                                            <option value="cross_sell">Cross Sell</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Priority (1 = highest)</label>
                                        <input type="number" name="priority" class="form-control" value="1" min="1" max="10" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Save Recommendation</button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    loadRecommendations();
    
    $('#recommendation_form').submit(function(e){
        e.preventDefault();
        
        var formData = new FormData($(this)[0]);
        
        start_loader();
        $.ajax({
            url: '../classes/Master.php?f=save_product_recommendation',
            method: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            error: err => {
                console.log(err);
                alert_toast("An error occurred", 'error');
                end_loader();
            },
            success: function(resp){
                if(resp.status == 'success'){
                    alert_toast(resp.msg, 'success');
                    resetForm();
                    loadRecommendations();
                } else {
                    alert_toast(resp.msg, 'error');
                }
                end_loader();
            }
        });
    });
});

function loadRecommendations(){
    $.ajax({
        url: '../classes/Master.php?f=get_all_recommendations',
        method: 'POST',
        dataType: 'json',
        success: function(resp){
            if(resp.status == 'success'){
                var html = '';
                resp.recommendations.forEach(function(rec){
                    html += '<div class="recommendation-card">';
                    html += '<div class="d-flex justify-content-between align-items-start">';
                    html += '<div>';
                    html += '<h6>' + rec.product_name + '</h6>';
                    html += '<p class="mb-1"><strong>→</strong> ' + rec.recommended_product_name + '</p>';
                    html += '<small class="text-muted">Type: ' + rec.recommendation_type + ' | Priority: ' + rec.priority + '</small>';
                    html += '</div>';
                    html += '<div class="btn-group">';
                    html += '<button class="btn btn-sm btn-primary" onclick="editRecommendation(' + rec.id + ')">Edit</button>';
                    html += '<button class="btn btn-sm btn-danger" onclick="deleteRecommendation(' + rec.id + ')">Delete</button>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
                $('#recommendations_list').html(html);
            }
        }
    });
}

function addRecommendation(){
    resetForm();
}

function editRecommendation(id){
    $.ajax({
        url: '../classes/Master.php?f=get_recommendation',
        method: 'POST',
        data: {id: id},
        dataType: 'json',
        success: function(resp){
            if(resp.status == 'success'){
                var rec = resp.recommendation;
                $('input[name="id"]').val(rec.id);
                $('select[name="product_id"]').val(rec.product_id);
                $('select[name="recommended_product_id"]').val(rec.recommended_product_id);
                $('select[name="recommendation_type"]').val(rec.recommendation_type);
                $('input[name="priority"]').val(rec.priority);
            }
        }
    });
}

function deleteRecommendation(id){
    if(confirm('Are you sure you want to delete this recommendation?')){
        $.ajax({
            url: '../classes/Master.php?f=delete_product_recommendation',
            method: 'POST',
            data: {id: id},
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    alert_toast(resp.msg, 'success');
                    loadRecommendations();
                } else {
                    alert_toast(resp.msg, 'error');
                }
            }
        });
    }
}

function resetForm(){
    $('#recommendation_form')[0].reset();
    $('input[name="id"]').val('');
}
</script>

