
<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
				<div class="form-group col-6">
					<label for="name">First Name</label>
					<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
				</div>
				<div class="form-group col-6">
					<label for="name">Last Name</label>
					<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
				</div>
				<div class="form-group col-6">
					<label for="username">Username</label>
					<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required  autocomplete="off">
					<small class="form-text text-muted">Username must be unique and contain only letters, numbers, and underscores.</small>
				</div>
				<div class="form-group col-6">
					<label for="email">Email</label>
					<input type="email" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" required autocomplete="off">
					<small class="form-text text-muted">Email must be unique and valid format.</small>
				</div>
				<div class="form-group col-6">
					<label for="password">Password</label>
					<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off" <?php echo isset($meta['id']) ? "": 'required' ?>>
                    <?php if(isset($_GET['id'])): ?>
					<small><i>Leave this blank if you dont want to change the password.</i></small>
                    <?php endif; ?>
				</div>
				<div class="form-group col-6">
					<label for="role_type">Staff Position</label>
					<select name="role_type" id="role_type" class="custom-select">
						<option value="admin" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'admin' ? 'selected' : '' ?>>Admin</option>
						<option value="system_admin" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'system_admin' ? 'selected' : '' ?>>System Admin</option>
						<option value="desk_staff" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'desk_staff' ? 'selected' : '' ?>>Desk/Staff</option>
						<option value="data_admin" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'data_admin' ? 'selected' : '' ?>>Data Admin</option>
						<option value="inventory" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'inventory' ? 'selected' : '' ?>>Inventory</option>
						<option value="service_admin" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'service_admin' ? 'selected' : '' ?>>Service Receptionist</option>
					</select>
					<small class="form-text text-muted">Select the staff position for this user.</small>
				</div>
				
				<!-- Permissions Checklist -->
				<div class="form-group col-12">
					<label class="control-label">Access Permissions</label>
					<div class="card card-outline card-info">
						<div class="card-header">
							<h6 class="card-title mb-0">Select modules/features this user can access:</h6>
						</div>
						<div class="card-body">
							<?php 
							// Get existing permissions if editing
							$existing_permissions = [];
							if(isset($meta['permissions']) && !empty($meta['permissions'])){
								$existing_permissions = json_decode($meta['permissions'], true);
								if(!is_array($existing_permissions)){
									$existing_permissions = [];
								}
							}
							
							// Define all available permissions/modules
							$permissions_list = [
								'user_management' => 'User Management',
								'customer_management' => 'Customer Management',
								'product_management' => 'Product Management',
								'inventory_management' => 'Inventory Management',
								'stock_management' => 'Stock Management',
								'service_requests' => 'Service Requests',
								'promo_images' => 'Promo Images',
								'customer_images' => 'Customer Images',
								'reviews_management' => 'Reviews Management',
								'announcements_management' => 'Announcements Management',
								'content_management' => 'Content Management',
								'order_management' => 'Order Management',
								'invoice_management' => 'Invoice Management',
								'customer_accounts' => 'Customer Accounts',
								'orcr_documents' => 'OR/CR Documents',
								'reports' => 'Reports',
								'system_settings' => 'System Settings',
								'mechanics' => 'Mechanics Management'
							];
							
							// Group permissions by category
							$permission_groups = [
								'User & Customer' => ['user_management', 'customer_management', 'customer_accounts'],
								'Inventory & Products' => ['product_management', 'inventory_management', 'stock_management'],
								'Services' => ['service_requests', 'mechanics', 'promo_images', 'customer_images'],
								'Content & Data' => ['reviews_management', 'announcements_management', 'content_management'],
								'Sales & Orders' => ['order_management', 'invoice_management'],
								'Documents' => ['orcr_documents'],
								'System' => ['reports', 'system_settings']
							];
							?>
							
							<div class="row">
								<?php foreach($permission_groups as $group_name => $permissions): ?>
								<div class="col-md-6 mb-3">
									<div class="border rounded p-3">
										<h6 class="text-primary mb-2"><i class="fa fa-folder"></i> <?php echo $group_name ?></h6>
										<?php foreach($permissions as $perm_key): ?>
										<?php if(isset($permissions_list[$perm_key])): ?>
										<div class="form-check mb-2">
											<input class="form-check-input permission-checkbox" type="checkbox" 
												name="permissions[]" 
												id="perm_<?php echo $perm_key ?>" 
												value="<?php echo $perm_key ?>"
												<?php echo in_array($perm_key, $existing_permissions) ? 'checked' : '' ?>>
											<label class="form-check-label" for="perm_<?php echo $perm_key ?>">
												<?php echo $permissions_list[$perm_key] ?>
											</label>
										</div>
										<?php endif; ?>
										<?php endforeach; ?>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
							
							<div class="mt-3">
								<button type="button" class="btn btn-sm btn-secondary" id="select_all_perms">
									<i class="fa fa-check-square"></i> Select All
								</button>
								<button type="button" class="btn btn-sm btn-secondary" id="deselect_all_perms">
									<i class="fa fa-square"></i> Deselect All
								</button>
							</div>
						</div>
					</div>
					<small class="form-text text-muted">Note: Permissions work in conjunction with the selected Staff Position. Some permissions may be automatically granted based on role.</small>
				</div>
				<div class="form-group col-6">
					<label for="" class="control-label">Avatar</label>
					<div class="custom-file">
		              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		              <label class="custom-file-label" for="customFile">Choose file</label>
		            </div>
				</div>
				<div class="form-group col-6 d-flex justify-content-center">
					<img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary mr-2" form="manage-user">Save</button>
					<a class="btn btn-sm btn-secondary" href="./?page=user/list">Cancel</a>
				</div>
			</div>
		</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	// Select All / Deselect All permissions
	$('#select_all_perms').click(function(){
		$('.permission-checkbox').prop('checked', true);
	});
	
	$('#deselect_all_perms').click(function(){
		$('.permission-checkbox').prop('checked', false);
	});
	
	// Auto-select permissions based on role type
	$('#role_type').change(function(){
		var role = $(this).val();
		var permissions = [];
		
		// Define default permissions for each role
		if(role === 'admin'){
			// Admin gets all permissions
			$('.permission-checkbox').prop('checked', true);
		} else if(role === 'system_admin'){
			// System Admin: Full access to all user accounts (System Users and Customers)
			$('.permission-checkbox').prop('checked', false);
			$('#perm_user_management, #perm_customer_management').prop('checked', true);
		} else if(role === 'desk_staff'){
			// Desk/Staff: Daily transactions - Inventory staff (product stocks) and Reception staff (service appointments)
			$('.permission-checkbox').prop('checked', false);
			$('#perm_product_management, #perm_inventory_management, #perm_stock_management, #perm_service_requests, #perm_mechanics, #perm_order_management').prop('checked', true);
		} else if(role === 'data_admin'){
			// Data Admin: Manages promos, reviews, announcements, content and other data-driven modules
			$('.permission-checkbox').prop('checked', false);
			$('#perm_promo_images, #perm_customer_images, #perm_reviews_management, #perm_announcements_management, #perm_content_management').prop('checked', true);
		} else if(role === 'inventory'){
			// Inventory role gets inventory-related permissions
			$('.permission-checkbox').prop('checked', false);
			$('#perm_product_management, #perm_inventory_management, #perm_stock_management').prop('checked', true);
		} else if(role === 'service_admin'){
			// Service admin gets service-related permissions
			$('.permission-checkbox').prop('checked', false);
			$('#perm_service_requests, #perm_promo_images, #perm_customer_images, #perm_mechanics').prop('checked', true);
		}
	});
	
	$('#manage-user').submit(function(e){
		e.preventDefault();
		var _this = $(this)
		
		// Collect permissions into a JSON string
		var permissions = [];
		$('.permission-checkbox:checked').each(function(){
			permissions.push($(this).val());
		});
		
		// Create a hidden input for permissions JSON
		if($('#permissions_json').length === 0){
			$(this).append('<input type="hidden" name="permissions_json" id="permissions_json">');
		}
		$('#permissions_json').val(JSON.stringify(permissions));
		
		start_loader()
		$.ajax({
			url:_base_url_+'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				try {
					var response = typeof resp === 'string' ? JSON.parse(resp) : resp;
					if(response.status === 'success'){
						alert_toast(response.msg || "User data saved successfully.", 'success');
						setTimeout(() => {
							location.href = './?page=user/list';
						}, 1500);
					}else{
						$('#msg').html('<div class="alert alert-danger">' + response.msg + '</div>')
						$("html, body").animate({ scrollTop: 0 }, "fast");
					}
				} catch(e) {
					$('#msg').html('<div class="alert alert-danger">An error occurred while processing the request.</div>')
					$("html, body").animate({ scrollTop: 0 }, "fast");
				}
                end_loader()
			},
			error: function(xhr, status, error) {
				$('#msg').html('<div class="alert alert-danger">An error occurred: ' + error + '</div>')
				$("html, body").animate({ scrollTop: 0 }, "fast");
				end_loader()
			}
		})
	})

</script>