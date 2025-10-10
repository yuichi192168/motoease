<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
	img#cimg2{
		height: 50vh;
		width: 100%;
		object-fit: contain;
		/* border-radius: 100% 100%; */
	}
</style>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">System Information</h5>
			<!-- <div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-primary new_department" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div> -->
		</div>
		<div class="card-body">
			<form action="" id="system-frm">
				<div id="msg" class="form-group"></div>
				<div class="form-group">
					<label for="name" class="control-label">System Name</label>
					<input type="text" class="form-control form-control-sm" name="name" id="name" value="<?php echo $_settings->info('name') ?>">
				</div>
				<div class="form-group">
					<label for="short_name" class="control-label">System Short Name</label>
					<input type="text" class="form-control form-control-sm" name="short_name" id="short_name" value="<?php echo  $_settings->info('short_name') ?>">
				</div>
			<div class="form-group">
				<label for="" class="control-label">About Us</label>
	             <textarea name="about_us" id="" cols="30" rows="2" class="form-control summernote"><?php echo  is_file(base_app.'about.html') ? file_get_contents(base_app.'about.html') : "" ?></textarea>
			</div>
			<div class="form-group">
				<label for="" class="control-label">System Logo</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
	              <label class="custom-file-label" for="customFile">Choose file</label>
	            </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
			</div>
			<div class="form-group">
				<label for="" class="control-label">Website Cover</label>
				<div class="custom-file">
	              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="cover" onchange="displayImg2(this,$(this))">
	              <label class="custom-file-label" for="customFile">Choose file</label>
	            </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image($_settings->info('cover')) ?>" alt="" id="cimg2" class="img-fluid img-thumbnail">
			</div>
			
			<!-- Print Report Logos Section -->
			<hr>
			<h5 class="text-primary">Print Report Logos</h5>
			<p class="text-muted">Upload separate logos for print reports. Main logo appears on the left, secondary logo on the right.</p>
			
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="control-label">Main Logo (Left Side)</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input" id="mainLogoFile" name="main_logo" onchange="displayMainLogo(this,$(this))">
							<label class="custom-file-label" for="mainLogoFile">Choose main logo file</label>
						</div>
					</div>
					<div class="form-group d-flex justify-content-center">
						<img src="<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" id="mainLogoImg" class="img-fluid img-thumbnail" style="max-height: 150px; max-width: 200px;">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="control-label">Secondary Logo (Right Side)</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input" id="secondaryLogoFile" name="secondary_logo" onchange="displaySecondaryLogo(this,$(this))">
							<label class="custom-file-label" for="secondaryLogoFile">Choose secondary logo file</label>
						</div>
					</div>
					<div class="form-group d-flex justify-content-center">
						<img src="<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" id="secondaryLogoImg" class="img-fluid img-thumbnail" style="max-height: 150px; max-width: 200px;">
					</div>
				</div>
			</div>
			
			<!-- Promo Images Section -->
			<hr>
			<h5 class="text-primary">Promo Images</h5>
			<p class="text-muted">Upload promotional images to display on the home page.</p>
			
			<div class="form-group">
				<label for="" class="control-label">Promo Images</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="promoImages" name="promo_images[]" multiple accept="image/*">
					<label class="custom-file-label" for="promoImages">Choose promo images</label>
				</div>
			</div>
			
			<div id="promo-preview" class="row"></div>
			
			<!-- Customer Purchase Images Section -->
			<hr>
			<h5 class="text-primary">Customer Purchase Images</h5>
			<p class="text-muted">Upload images of customers with their purchased motorcycles.</p>
			
			<div class="form-group">
				<label for="" class="control-label">Customer Images</label>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="customerImages" name="customer_images[]" multiple accept="image/*">
					<label class="custom-file-label" for="customerImages">Choose customer images</label>
				</div>
			</div>
			
			<div id="customer-preview" class="row"></div>
			
			</form>
		</div>
		<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary" form="system-frm">Update</button>
				</div>
			</div>
		</div>

	</div>
</div>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	function displayImg2(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        	$('#cimg2').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	function displayImg3(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        	$('#cimg3').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	function displayMainLogo(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#mainLogoImg').attr('src', e.target.result);
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	function displaySecondaryLogo(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#secondaryLogoImg').attr('src', e.target.result);
	        	_this.siblings('.custom-file-label').html(input.files[0].name)
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	
	// Handle promo images preview
	$('#promoImages').on('change', function() {
		var files = this.files;
		var preview = $('#promo-preview');
		preview.empty();
		
		for (var i = 0; i < files.length; i++) {
			var file = files[i];
			var reader = new FileReader();
			
			reader.onload = function(e) {
				var col = $('<div class="col-md-3 mb-3"></div>');
				var card = $('<div class="card"></div>');
				var img = $('<img class="card-img-top" style="height: 150px; object-fit: cover;">');
				img.attr('src', e.target.result);
				
				var cardBody = $('<div class="card-body p-2"></div>');
				var title = $('<input type="text" class="form-control form-control-sm mb-1" placeholder="Promo Title" name="promo_titles[]">');
				var description = $('<textarea class="form-control form-control-sm" placeholder="Description" name="promo_descriptions[]" rows="2"></textarea>');
				
				cardBody.append(title).append(description);
				card.append(img).append(cardBody);
				col.append(card);
				preview.append(col);
			};
			
			reader.readAsDataURL(file);
		}
	});
	
	// Handle customer images preview
	$('#customerImages').on('change', function() {
		var files = this.files;
		var preview = $('#customer-preview');
		preview.empty();
		
		for (var i = 0; i < files.length; i++) {
			var file = files[i];
			var reader = new FileReader();
			
			reader.onload = function(e) {
				var col = $('<div class="col-md-3 mb-3"></div>');
				var card = $('<div class="card"></div>');
				var img = $('<img class="card-img-top" style="height: 150px; object-fit: cover;">');
				img.attr('src', e.target.result);
				
				var cardBody = $('<div class="card-body p-2"></div>');
				var customerName = $('<input type="text" class="form-control form-control-sm mb-1" placeholder="Customer Name" name="customer_names[]">');
				var motorcycleModel = $('<input type="text" class="form-control form-control-sm mb-1" placeholder="Motorcycle Model" name="motorcycle_models[]">');
				var purchaseDate = $('<input type="date" class="form-control form-control-sm mb-1" name="purchase_dates[]">');
				var testimonial = $('<textarea class="form-control form-control-sm" placeholder="Testimonial" name="customer_testimonials[]" rows="2"></textarea>');
				
				cardBody.append(customerName).append(motorcycleModel).append(purchaseDate).append(testimonial);
				card.append(img).append(cardBody);
				col.append(card);
				preview.append(col);
			};
			
			reader.readAsDataURL(file);
		}
	});
	$(document).ready(function(){
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