
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
						<option value="inventory" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'inventory' ? 'selected' : '' ?>>Inventory</option>
						<option value="service_admin" <?php echo isset($meta['role_type']) && $meta['role_type'] == 'service_admin' ? 'selected' : '' ?>>Service Receptionist</option>
					</select>
					<small class="form-text text-muted">Select the staff position for this user.</small>
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
	$('#manage-user').submit(function(e){
		e.preventDefault();
		var _this = $(this)
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
						location.href = './?page=user/list';
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