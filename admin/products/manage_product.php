<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<style>
    .template-preview {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        margin-top: 10px;
        font-size: 0.9rem;
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Create New " ?> Product</h3>
	</div>
	<div class="card-body">
		<form action="" id="product-form">
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
            <div class="form-group">
				<label for="brand_id" class="control-label">Brand</label>
                <select name="brand_id" id="brand_id" class="custom-select select2">
                    <option value="" <?= !isset($brand_id) ? "selected" : "" ?> disabled></option>
                    <?php 
                    $brands = $conn->query("SELECT * FROM brand_list where delete_flag = 0 ".(isset($brand_id) ? " or id = '{$brand_id}'" : "")." order by `name` asc ");
                    while($row= $brands->fetch_assoc()):
                    ?>
                    <option value="<?= $row['id'] ?>" <?= isset($brand_id) && $brand_id == $row['id'] ? "selected" : "" ?>><?= $row['name'] ?> <?= $row['delete_flag'] == 1 ? "<small>Deleted</small>" : "" ?></option>
                    <?php endwhile; ?>
                </select>
			</div>
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
			<div class="form-group">
				<label for="models" class="control-label">Compatible for: <small>(model)</small></label>
                <input name="models" id="models" type="text" class="form-control rounded-0" value="<?php echo isset($models) ? $models : ''; ?>" required>
			</div>
            
            <!-- Template Description System -->
            <div class="form-group">
				<label for="description_template" class="control-label">Description Template</label>
                <select name="description_template" id="description_template" class="custom-select select2">
                    <option value="">Select a template or write custom description</option>
                    <option value="crash_guard" <?= (isset($description) && strpos($description, 'Crash Guard') !== false) ? 'selected' : '' ?>>Crash Guard Template</option>
                    <option value="steering_damper" <?= (isset($description) && strpos($description, 'Steering Damper') !== false) ? 'selected' : '' ?>>Steering Damper Template</option>
                    <option value="exhaust_system" <?= (isset($description) && strpos($description, 'Exhaust System') !== false) ? 'selected' : '' ?>>Exhaust System Template</option>
                    <option value="brake_system" <?= (isset($description) && strpos($description, 'Brake System') !== false) ? 'selected' : '' ?>>Brake System Template</option>
                    <option value="lighting" <?= (isset($description) && strpos($description, 'Lighting') !== false) ? 'selected' : '' ?>>Lighting Template</option>
                    <option value="performance" <?= (isset($description) && strpos($description, 'Performance') !== false) ? 'selected' : '' ?>>Performance Parts Template</option>
                    <option value="custom">Custom Description</option>
                </select>
                <div id="template_preview" class="template-preview" style="display:none;"></div>
            </div>
            
            <div class="form-group">
				<label for="description" class="control-label">Description</label>
                <textarea name="description" id="description" type="text" class="form-control rounded-0 summernote" required><?php echo isset($description) ? $description : ''; ?></textarea>
			</div>
            
            <!-- Manual Price Input -->
			<div class="form-group">
				<label for="price" class="control-label">Price</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">₱</span>
                    </div>
                    <input type="number" name="price" id="price" class="form-control rounded-0" value="<?php echo isset($price) ? $price : ''; ?>" step="0.01" min="0" required>
                </div>
                <small class="form-text text-muted">Enter the product price manually</small>
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
			placeholder:"Please Select Here",
			dropdownParent: $('#uni_modal')
		})

        // Initialize Select2 for brand and category dropdowns
        $('#brand_id, #category_id').select2({
            placeholder: "Please Select Here",
            allowClear: true,
            dropdownParent: $('body')
        });

        // Template description system
        const templates = {
            crash_guard: {
                description: `<p><strong>High-Quality Crash Guard</strong></p>
<p>This premium crash guard provides excellent protection for your motorcycle's engine and body parts. Features include:</p>
<ul>
<li>Heavy-duty steel construction for maximum durability</li>
<li>Powder-coated finish for rust resistance</li>
<li>Easy installation with included mounting hardware</li>
<li>Compatible with multiple motorcycle models</li>
<li>Provides comprehensive engine and body protection</li>
</ul>
<p>Perfect for riders who want reliable protection for their valuable motorcycle investment.</p>`
            },
            steering_damper: {
                description: `<p><strong>Performance Steering Damper</strong></p>
<p>Enhance your motorcycle's handling and stability with this professional-grade steering damper. Features include:</p>
<ul>
<li>Adjustable damping settings for personalized feel</li>
<li>High-quality hydraulic system for smooth operation</li>
<li>CNC-machined aluminum construction</li>
<li>Easy installation with detailed instructions</li>
<li>Reduces handlebar vibration and improves control</li>
</ul>
<p>Ideal for performance riders and long-distance touring.</p>`
            },
            exhaust_system: {
                description: `<p><strong>Performance Exhaust System</strong></p>
<p>Upgrade your motorcycle's performance and sound with this premium exhaust system. Features include:</p>
<ul>
<li>Stainless steel construction for durability</li>
<li>Performance-tuned for optimal power output</li>
<li>Deep, aggressive exhaust note</li>
<li>Easy bolt-on installation</li>
<li>Includes all necessary mounting hardware</li>
</ul>
<p>Transform your motorcycle's performance and appearance.</p>`
            },
            brake_system: {
                description: `<p><strong>High-Performance Brake System</strong></p>
<p>Upgrade your motorcycle's stopping power with this premium brake system. Features include:</p>
<ul>
<li>High-quality brake pads for superior stopping power</li>
<li>Stainless steel brake lines for consistent performance</li>
<li>Easy installation and maintenance</li>
<li>Compatible with stock brake calipers</li>
<li>Improved brake feel and response</li>
</ul>
<p>Essential upgrade for safety-conscious riders.</p>`
            },
            lighting: {
                description: `<p><strong>LED Lighting System</strong></p>
<p>Illuminate your path with this advanced LED lighting system. Features include:</p>
<ul>
<li>High-brightness LED technology for maximum visibility</li>
<li>Energy-efficient design for longer battery life</li>
<li>Easy plug-and-play installation</li>
<li>Weather-resistant construction</li>
<li>Multiple lighting modes and patterns</li>
</ul>
<p>Enhance visibility and safety during night rides.</p>`
            },
            performance: {
                description: `<p><strong>Performance Enhancement Kit</strong></p>
<p>Boost your motorcycle's performance with this comprehensive upgrade kit. Features include:</p>
<ul>
<li>High-flow air filter for improved breathing</li>
<li>Performance ECU tuning for optimal power</li>
<li>Lightweight components for better handling</li>
<li>Easy installation with detailed instructions</li>
<li>Compatible with stock motorcycle systems</li>
</ul>
<p>Unlock your motorcycle's full potential.</p>`
            }
        };

        $('#description_template').change(function() {
            var template = $(this).val();
            
            if (template && template !== 'custom' && templates[template]) {
                $('#description').val(templates[template].description);
                $('#template_preview').html(templates[template].description).show();
            } else {
                $('#template_preview').hide();
                if (template === 'custom') {
                    $('#description').val('').focus();
                }
            }
        });

		$('#product-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
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
						location.href = "?page=products";
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
		})

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
		    })
	})
</script>