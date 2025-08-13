<?php
require_once('./../../config.php');

$id = isset($_GET['id']) ? $_GET['id'] : '';
$row = null;
if($id){
    $qry = $conn->query("SELECT * from `brand_list` where id = '{$id}' ");
    if($qry->num_rows > 0){
        $row = $qry->fetch_assoc();
    }
}

// Fetch predefined logos from database
$logos_qry = $conn->query("SELECT * FROM `brand_list` WHERE delete_flag = 0 ORDER BY name ASC");
$available_logos = [];
while($logo = $logos_qry->fetch_assoc()){
    $available_logos[$logo['image_path']] = $logo['name'];
}
?>
<style>
#uni_modal img#cimg{
    height: 5em;
    width: 5em;
    object-fit: scale-down;
    object-position: center center;
}
.img-logo{
    width:3em;
    height:3em;
    object-fit: scale-down;
    object-position: center center;
    cursor: pointer;
    border: 2px solid transparent;
}
input[type="radio"] { display:none; }
input[type="radio"]:checked + img { border-color: #007bff; }
</style>

<div class="container-fluid">
    <form action="" id="brand-form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= isset($row['id']) ? $row['id'] : '' ?>">

        <div class="form-group">
            <label for="name" class="control-label">Brand Name</label>
            <input name="name" id="name" class="form-control form-control-sm" value="<?= isset($row['name']) ? $row['name'] : ''; ?>" required/>
        </div>

       <div class="form-group">
    <label>Predefined Logos</label>
    <div class="d-flex flex-wrap">
        <?php foreach($available_logos as $file => $brand_name): ?>
        <div class="m-2 text-center">
            <img src="<?= validate_image($file) ?>" alt="<?= $brand_name ?>" class="img-logo img-thumbnail">
            <div><?= $brand_name ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>


        <div class="form-group">
            <label>Or Upload New Logo</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
                <label class="custom-file-label" for="customFile">Choose file</label>
            </div>
        </div>

        <div class="form-group d-flex justify-content-center">
            <img src="<?= validate_image(isset($row['image_path']) ? $row['image_path'] : "") ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
        </div>
    </form>
</div>

<script>
window.displayImg = function(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
            _this.siblings('.custom-file-label').html(input.files[0].name);
            $('input[name="predefined_logo"]').prop('checked', false); // deselect predefined
        }
        reader.readAsDataURL(input.files[0]);
    }else{
        $('#cimg').attr('src', "<?= validate_image(isset($row['image_path']) ? $row['image_path'] : "") ?>");
        _this.siblings('.custom-file-label').html("Choose file");
    }
}

$(document).ready(function(){
    $('#brand-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        start_loader();

        // Decide which logo to save: uploaded file or predefined
        var formData = new FormData($(this)[0]);
        var selectedPredefined = $('input[name="predefined_logo"]:checked').val();
        if(selectedPredefined && !$('#customFile').val()){
            formData.append('selected_logo', selectedPredefined);
        }

        $.ajax({
            url: _base_url_+"classes/Master.php?f=save_brand",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            error: err=>{
                console.log(err);
                alert_toast("An error occured",'error');
                end_loader();
            },
            success: function(resp){
                if(typeof resp =='object' && resp.status == 'success'){
                    location.href = "./?page=maintenance/brands";
                }else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg)
                    _this.prepend(el)
                    el.show('slow')
                    $("html, body").animate({ scrollTop: _this.closest('.card').offset().top }, "fast");
                    end_loader()
                }else{
                    alert_toast("An error occured",'error');
                    end_loader();
                    console.log(resp)
                }
            }
        })
    })
});
</script>
