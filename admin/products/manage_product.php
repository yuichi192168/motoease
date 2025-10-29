<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
// Default brand to Honda
$honda_id = null;
$bq = $conn->query("SELECT id FROM brand_list WHERE name='Honda' ORDER BY id ASC LIMIT 1");
if($bq && $bq->num_rows>0){ $honda_id = $bq->fetch_array()[0]; }
?>
<div class="card card-outline card-info rounded-0">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Create New " ?> Product</h3>
	</div>
	<div class="card-body">
        <form action="" id="product-form">
            <input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
            <input type="hidden" name="brand_id" value="<?php echo isset($brand_id) ? $brand_id : ($honda_id ?? ''); ?>">
            
            <div class="form-group">
				<label for="category_id" class="control-label">Category</label>
                <select name="category_id" id="category_id" class="custom-select select2">
                    <option value="" <?= !isset($category_id) ? "selected" : "" ?> disabled></option>
                    <?php 
                    $categories = $conn->query("SELECT * FROM categories where delete_flag = 0 ".(isset($category_id) ? " or id = '{$category_id}'" : "")." order by `category` asc ");
                    while($row= $categories->fetch_assoc()):
                    ?>
                    <option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? "selected" : "" ?>><?= $row['category'] ?> <?= $row['delete_flag'] == 1 ? "<small>Deleted</small>" : "" ?></option>
                    <?php endwhile; ?>
                </select>
			</div>

			<div class="form-group">
				<label for="name" class="control-label">Name</label>
                <input name="name" id="name" type="text" class="form-control rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required>
			</div>

            <!-- Dynamic compatibility field based on category -->
            <div class="form-group" id="compatibility-field">
                <label for="models" class="control-label" id="compatibility-label">Compatible for: <small>(model)</small></label>
                <select name="models" id="models" class="custom-select select2" required>
                    <option value="">-- Select --</option>
                </select>
            </div>

			<div class="form-group">
				<label for="available_colors" class="control-label">Available Colors <small>(comma-separated, e.g., Red, Blue, Black)</small></label>
				<input name="available_colors" id="available_colors" type="text" class="form-control rounded-0" value="<?php echo isset($available_colors) ? htmlspecialchars($available_colors) : ''; ?>">
			</div>

			<div class="form-group">
				<label class="control-label d-block">Color Images (optional)</label>
				<small class="text-muted d-block mb-1">Upload one image per color name listed above. Leave blank to use main product image.</small>
				<div id="colorImagesContainer"></div>
			</div>

            <!-- Template selection removed to enforce specific descriptions per variation -->

            <div class="form-group">
                <label for="description" class="control-label">Specifications</label>
                <textarea name="description" id="description" type="text" class="form-control rounded-0 summernote" required><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>

            <!-- Multi-compatibility for oil products -->
            <div class="form-group">
                <label for="compatible_models" class="control-label">Compatible Motorcycle Models (for Oils)</label>
                <select name="compatible_models[]" id="compatible_models" class="custom-select select2" multiple>
                    <?php 
                    $models_list = [
                        "ADV 160","Airblade 150","Airblade 160","Beat","Click 125i","Click 125i SE","Click 160","CRF150L","DIO","Giorno+","PCX 150","PCX 160 ABS","PCX 160 CBS","RS 125","Supra GTR 150","TMX 125 ALPHA","TMX SUPREMO","Wave RSX(DISC)","Wave RSX(DRUM)","Winner X (Premium)","Winner X (Racing)","XR 150i","XRM 125 Dual Sport Fi","CB150x"
                    ];
                    $selected_compat = [];
                    if(isset($id)){
                        $compat_rs = $conn->query("SELECT model_name FROM product_compatibility WHERE product_id = '{$id}' ORDER BY model_name ASC");
                        if($compat_rs){
                            while($cr = $compat_rs->fetch_assoc()) $selected_compat[] = $cr['model_name'];
                        }
                    }
                    foreach($models_list as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>" <?= in_array($m, $selected_compat) ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Leave empty if not applicable. Use for oils usable across multiple models.</small>
            </div>

			<div class="form-group">
				<label for="price" class="control-label">Price</label>
				<input name="price" id="price" type="text" class="form-control form-control-sm text-right" value="<?php echo isset($price) ? $price : ''; ?>" placeholder="000000.00" required>
			</div>

            <!-- ABC Classification Fields - Auto-assigned -->
            <div class="form-group">
				<label for="abc_category" class="control-label">ABC Category</label>
                <input type="text" name="abc_category" id="abc_category" class="form-control" value="<?php echo isset($abc_category) ? $abc_category : 'C'; ?>" readonly>
                <small class="text-muted">ABC classification is automatically assigned based on product value and demand</small>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="min_stock" class="control-label">Minimum Stock</label>
                        <input name="min_stock" id="min_stock" type="number" min="0" class="form-control form-control-sm text-right" value="<?php echo isset($min_stock) ? $min_stock : '0'; ?>">
                        <small class="text-muted">Minimum stock level</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="max_stock" class="control-label">Maximum Stock</label>
                        <input name="max_stock" id="max_stock" type="number" min="0" class="form-control form-control-sm text-right" value="<?php echo isset($max_stock) ? $max_stock : '0'; ?>">
                        <small class="text-muted">Maximum stock level</small>
                    </div>
                </div>
            </div>

            <div class="form-group">
				<label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="custom-select selevt">
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-md-6">
						<label for="" class="control-label">Product Image</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
							<label class="custom-file-label" for="customFile">Choose file</label>
						</div>
					</div>
					<div class="col-md-6">
						<div class="d-flex justify-content-center">
							<img src="<?php echo validate_image(isset($image_path) ? $image_path : "") ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="product-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=products">Cancel</a>
	</div>
</div>

<script>
window.displayImg = function(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
            _this.siblings('.custom-file-label').html(input.files[0].name)
        }
        reader.readAsDataURL(input.files[0]);
    }else{
        $('#cimg').attr('src', "<?php echo validate_image(isset($image_path) ? $image_path : "") ?>");
        _this.siblings('.custom-file-label').html("Choose file")
    }
}

$(document).ready(function(){
    $('.select2').select2({
        width:'100%',
        placeholder:"Please Select Here"
    });

    // Dynamic compatibility field based on category
    function updateCompatibilityField() {
        var categoryId = $('#category_id').val();
        var categoryText = $('#category_id option:selected').text().toLowerCase();
        var $field = $('#compatibility-field');
        var $label = $('#compatibility-label');
        var $select = $('#models');
        
        // Clear existing options
        $select.empty().append('<option value="">-- Select --</option>');
        
        if (categoryText.includes('motorcycle')) {
            // For motorcycles, show compatible oils
            $label.html('Compatible Oils: <small>(select applicable oils)</small>');
            $select.attr('name', 'compatible_oils[]').attr('multiple', true);
            
            var oils = [
                "Honda 4T 10W-30", "Honda 4T 10W-40", "Honda 4T 20W-50", 
                "Honda 4T 5W-30", "Honda 4T 15W-40", "Honda 4T 5W-40",
                "Honda 2T Oil", "Honda Gear Oil", "Honda Brake Fluid",
                "Honda Coolant", "Honda Chain Lube", "Honda Carb Cleaner"
            ];
            
            oils.forEach(function(oil) {
                $select.append('<option value="' + oil + '">' + oil + '</option>');
            });
        } else if (categoryText.includes('oil')) {
            // For oils, show compatible motorcycles
            $label.html('Compatible Motorcycles: <small>(select applicable models)</small>');
            $select.attr('name', 'compatible_motorcycles[]').attr('multiple', true);
            
            var motorcycles = [
                "ADV 160", "Airblade 150", "Airblade 160", "Beat", "Click 125i", 
                "Click 125i SE", "Click 160", "CRF150L", "DIO", "Giorno+", 
                "PCX 150", "PCX 160 ABS", "PCX 160 CBS", "RS 125", "Supra GTR 150", 
                "TMX 125 ALPHA", "TMX SUPREMO", "Wave RSX(DISC)", "Wave RSX(DRUM)", 
                "Winner X (Premium)", "Winner X (Racing)", "XR 150i", "XRM 125 Dual Sport Fi", "CB150x"
            ];
            
            motorcycles.forEach(function(motorcycle) {
                $select.append('<option value="' + motorcycle + '">' + motorcycle + '</option>');
            });
        } else {
            // For other categories, show regular model compatibility
            $label.html('Compatible for: <small>(model)</small>');
            $select.attr('name', 'models').removeAttr('multiple');
            
            var models = [
                "ADV 160", "Airblade 150", "Airblade 160", "Beat", "Click 125i", 
                "Click 125i SE", "Click 160", "CRF150L", "DIO", "Giorno+", 
                "PCX 150", "PCX 160 ABS", "PCX 160 CBS", "RS 125", "Supra GTR 150", 
                "TMX 125 ALPHA", "TMX SUPREMO", "Wave RSX(DISC)", "Wave RSX(DRUM)", 
                "Winner X (Premium)", "Winner X (Racing)", "XR 150i", "XRM 125 Dual Sport Fi", "CB150x"
            ];
            
            models.forEach(function(model) {
                $select.append('<option value="' + model + '">' + model + '</option>');
            });
        }
        
        // Reinitialize select2
        $select.select2({
            width: '100%',
            placeholder: "Please Select Here"
        });
    }
    
    // Auto-assign ABC category based on price
    function autoAssignABCCategory() {
        var price = parseFloat($('#price').val().replace(/,/g, '')) || 0;
        var category = 'C'; // Default
        
        if (price >= 50000) {
            category = 'A'; // High value
        } else if (price >= 10000) {
            category = 'B'; // Medium value
        }
        
        $('#abc_category').val(category);
    }
    
    // Event handlers
    $('#category_id').on('change', updateCompatibilityField);
    $('#price').on('blur', autoAssignABCCategory);
    
    // Initialize on page load
    updateCompatibilityField();
    autoAssignABCCategory();

    // Dynamic color image inputs based on available_colors
    function renderColorImageInputs(){
        var colorsRaw = $('#available_colors').val() || '';
        var colors = colorsRaw.split(',').map(function(c){ return c.trim(); }).filter(function(c){ return c.length > 0; });
        var container = $('#colorImagesContainer');
        container.empty();
        if(colors.length === 0){ return; }
        colors.forEach(function(color){
            var safe = color.replace(/[^a-z0-9]+/gi,'_').toLowerCase();
            var group = $(
                '<div class="mb-2">'
                + '<label class="mb-1">'+ $('<div>').text(color).html() +'</label>'
                + '<div class="custom-file">'
                + '<input type="file" class="custom-file-input" name="color_image['+ safe +']" id="color_image_'+ safe +'">'
                + '<label class="custom-file-label" for="color_image_'+ safe +'">Choose image for '+ $('<div>').text(color).html() +'</label>'
                + '</div>'
                + '</div>'
            );
            container.append(group);
        });
        // hook up filenames display
        container.find('.custom-file-input').on('change', function(){
            var fileName = this.files && this.files[0] ? this.files[0].name : 'Choose file';
            $(this).siblings('.custom-file-label').text(fileName);
        });
    }
    $('#available_colors').on('input', renderColorImageInputs);
    renderColorImageInputs();

    // Price input handling: allow large numbers while typing; format on blur
    $('#price').on('focus', function(){
        // remove commas for editing
        $(this).val($(this).val().replace(/,/g, ''));
    });
    $('#price').on('input', function(){
        let v = $(this).val();
        // keep digits and a single dot
        v = v.replace(/[^0-9.]/g, '');
        const d = v.indexOf('.');
        if(d !== -1){
            v = v.slice(0, d + 1) + v.slice(d + 1).replace(/\./g, '');
        }
        $(this).val(v);
    });
    $('#price').on('blur', function(){
        let v = $(this).val().replace(/,/g, '');
        if(!v){ return; }
        const num = parseFloat(v);
        if(!isNaN(num)){
            const fixed = num.toFixed(2);
            const parts = fixed.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            $(this).val(parts.join('.'));
        }
    });

    $('#product-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        // Client-side validation
        var hasError = false;
        function markInvalid(selector, msg){
            hasError = true;
            var $el = $(selector);
            $el.addClass('is-invalid');
            var $msg = $('<div>').addClass('invalid-feedback d-block').text(msg);
            if($el.next('.invalid-feedback').length==0){ $el.after($msg); }
        }
        $(this).find('.is-invalid').removeClass('is-invalid');
        $(this).find('.invalid-feedback').remove();

        var nameVal = ($('#name').val()||'').trim();
        if(nameVal.length < 3) markInvalid('#name','Name must be at least 3 characters.');

        var catVal = ($('#category_id').val()||'');
        if(!catVal) markInvalid('#category_id','Please select a category.');

        var modelVal = ($('#models').val()||'');
        var categoryText = $('#category_id option:selected').text().toLowerCase();
        
        if(categoryText.includes('motorcycle') || categoryText.includes('oil')) {
            // For motorcycles and oils, compatibility is optional but recommended
            if(!modelVal || modelVal.length === 0) {
                // Just show a warning, don't mark as invalid
                console.log('Compatibility not selected - this is optional but recommended');
            }
        } else {
            // For other categories, compatibility is required
            if(!modelVal) markInvalid('#models','Please select a compatible model.');
        }

        var priceVal = ($('#price').val()||'').replace(/,/g,'');
        if(!priceVal || isNaN(priceVal) || parseFloat(priceVal) <= 0){ markInvalid('#price','Enter a valid price greater than 0.'); }

        var descHtml = $('#description').val()||'';
        var descText = $('<div>').html(descHtml).text().trim();
        if(descText.length < 10){ markInvalid('#description','Specifications must be at least 10 characters.'); }

        if(hasError){
            alert_toast('Please correct the highlighted fields.','error');
            return false;
        }

        start_loader();
        // sanitize price: remove commas before submit
        var rawPrice = $('#price').val();
        if(rawPrice){ $('#price').val(rawPrice.replace(/,/g, '')); }
        $.ajax({
            url:_base_url_+"classes/Master.php?f=save_product",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error:err=>{
                console.log(err)
                alert_toast("An error occured",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp =='object' && resp.status == 'success'){
                    // Save compatibility selections if any
                    var compat = $('#compatible_models').val() || [];
                    if(compat.length > 0){
                        $.ajax({
                            url:_base_url_+"classes/Master.php?f=save_product_compatibility",
                            method:'POST',
                            dataType:'json',
                            data:{product_id: resp.id, models: compat},
                            complete:function(){
                                alert_toast(resp.msg || "Product saved successfully.", 'success');
                                setTimeout(() => {
                                    location.href = "./?page=products/view_product&id="+resp.id;
                                }, 1500);
                            }
                        });
                    } else {
                        alert_toast(resp.msg || "Product saved successfully.", 'success');
                        setTimeout(() => {
                            location.href = "./?page=products/view_product&id="+resp.id;
                        }, 1500);
                    }
                }else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>')
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
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
    });

    $('.summernote').summernote({
        height: 200,
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
        ]
    });
});
</script>