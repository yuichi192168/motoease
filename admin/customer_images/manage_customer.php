<?php 
require_once('./../../config.php');
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM customer_images where id = '{$_GET['id']}' ");
	$customer_data = $qry->fetch_assoc();
	if($customer_data) {
		foreach($customer_data as $k => $v){
			$$k = $v;
		}
	}
}
// Get customers list
$customers = $conn->query("SELECT id, CONCAT(lastname, ', ', firstname, ' ', middlename) as fullname FROM client_list ORDER BY lastname, firstname");
?>
<style>
	#uni_modal .modal-footer{
		display:none
	}
</style>
<div class="container-fluid">
	<form action="" id="customer_image_form">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : "" ?>">
		<div class="col-12">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label for="customer_id" class="control-label">Customer *</label>
						<select name="customer_id" id="customer_id" class="form-control form-control-sm rounded-0 select2" required>
							<option value="">Select Customer</option>
							<?php 
							while($customer = $customers->fetch_assoc()):
							?>
							<option value="<?php echo $customer['id'] ?>" <?php echo isset($customer_id) && $customer_id == $customer['id'] ? 'selected' : '' ?>>
								<?php echo htmlspecialchars($customer['fullname']) ?>
							</option>
							<?php endwhile; ?>
						</select>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="description" class="control-label">Description</label>
						<textarea name="description" id="description" class="form-control form-control-sm rounded-0" rows="3"><?php echo isset($description) ? $description : "" ?></textarea>
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label for="customer_image" class="control-label">Customer Image *</label>
						<input type="file" name="customer_image" id="customer_image" class="form-control form-control-sm rounded-0" accept="image/*" <?php echo !isset($id) ? 'required' : '' ?>>
						<small class="text-muted">Upload customer image (JPG, PNG, GIF). Max size: 5MB</small>
					</div>
					<?php if(isset($image_path) && !empty($image_path)): ?>
					<div class="form-group">
						<label class="control-label">Current Image</label>
						<div class="mt-2">
							<img src="<?php echo validate_image($image_path) ?>" alt="Customer Image" class="img-thumbnail" style="max-width: 300px; max-height: 300px;" id="customer_image_preview">
						</div>
					</div>
					<?php endif; ?>
					<div id="customer_image_preview_new" style="display:none;" class="mt-2">
						<label class="control-label">New Image Preview</label>
						<div>
							<img src="" alt="Preview" class="img-thumbnail" style="max-width: 300px; max-height: 300px;">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="w-100 d-flex justify-content-end mx-2">
			<div class="col-auto">
				<button class="btn btn-primary btn-sm rounded-0">Save</button>
				<button class="btn btn-light btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
			</div>
		</div>
	</form>
</div>
<script>
	$(function(){
		$('.select2').select2({
			placeholder:"Please Select Customer",
			dropdownParent: $('#uni_modal')
		});
		
		// Image preview handler
		$('#customer_image').on('change', function(e){
			var file = e.target.files[0];
			if(file){
				if(file.size > 5242880){ // 5MB
					alert_toast('File size exceeds 5MB limit', 'error');
					$(this).val('');
					return;
				}
				var reader = new FileReader();
				reader.onload = function(e){
					$('#customer_image_preview_new img').attr('src', e.target.result);
					$('#customer_image_preview_new').show();
					<?php if(isset($image_path)): ?>
					$('#customer_image_preview').hide();
					<?php endif; ?>
				}
				reader.readAsDataURL(file);
			} else {
				$('#customer_image_preview_new').hide();
				<?php if(isset($image_path)): ?>
				$('#customer_image_preview').show();
				<?php endif; ?>
			}
		});
		
		$('#customer_image_form').submit(function(e){
			e.preventDefault();
			start_loader();
			
			var formData = new FormData($(this)[0]);
			
			$.ajax({
				url:_base_url_+'classes/Master.php?f=save_customer_image',
				method:'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				dataType:'json',
				error:err=>{
					console.log(err);
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					end_loader();
					if(resp.status == 'success'){
						alert_toast("Data successfully saved",'success');
						setTimeout(() => {
							location.reload();
						}, 200);
					}else{
						alert_toast(resp.msg || "An error occured",'error');
					}
				}
			})
		})
	})
</script>


