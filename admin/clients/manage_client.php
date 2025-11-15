<?php
// Ensure config is loaded
if(!defined('base_app')){
	require_once('../config.php');
}

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `client_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
    }
}
?>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update ": "Create New " ?> Client Details</h3>
	</div>
	<div class="card-body">
		<form action="" id="client-form" enctype="multipart/form-data">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="form-group">
				<label for="firstname" class="control-label">First Name</label>
                <input name="firstname" id="firstname" type="text" class="form-control rounded-0" value="<?php echo isset($firstname) ? $firstname : ''; ?>" required>
			</div>
			<div class="form-group">
				<label for="middlename" class="control-label">Middle Name</label>
                <input name="middlename" id="middlename" type="text" class="form-control rounded-0" value="<?php echo isset($middlename) ? $middlename : ''; ?>" placeholder="optional">
			</div>
			<div class="form-group">
				<label for="lastname" class="control-label">Last Name</label>
                <input name="lastname" id="lastname" type="text" class="form-control rounded-0" value="<?php echo isset($lastname) ? $lastname : ''; ?>" required>
			</div>
			<div class="form-group">
				<label for="gender" class="control-label">Gender</label>
                <select name="gender" id="gender" class="custom-select selevt">
                <option <?php echo isset($gender) && $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                <option <?php echo isset($gender) && $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                </select>
			</div>
            <div class="form-group">
				<label for="contact" class="control-label">Contact</label>
                <input name="contact" id="contact" type="text" class="form-control rounded-0" value="<?php echo isset($contact) ? $contact : ''; ?>" required>
			</div>
			<div class="form-group">
				<label for="address" class="control-label">Address</label>
                <textarea name="address" id="address"class="form-control rounded-0" required><?php echo isset($address) ? $address : ''; ?></textarea>
			</div>
            <div class="form-group">
				<label for="email" class="control-label">Email</label>
                <input name="email" id="email" type="email" class="form-control rounded-0" value="<?php echo isset($email) ? $email : ''; ?>" required>
			</div>
			<div class="form-group">
				<label for="" class="control-label">Avatar</label>
				<div class="custom-file">
		              <input type="file" class="custom-file-input rounded-circle" id="customFile" name="img" onchange="displayImg(this,$(this))">
		              <label class="custom-file-label" for="customFile">Choose file</label>
		            </div>
			</div>
			<div class="form-group d-flex justify-content-center">
				<img src="<?php echo validate_image(isset($avatar) ? $avatar :'') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
			</div>
			<div class="form-group">
				<label for="password" class="control-label">New Password</label>
				<div class="input-group">
					<input type="password" name="password" id="password" placeholder="" class="form-control">
					<div class="input-group-append border">
						<span class="input-group-text text-sm"><i class="fa fa-eye-slash text-muted pass_type" data-type="password"></i></span>
					</div>
				</div>
				<small><em class="text-muted">Fill only to update Client's Password</em></small>
			</div>
            <div class="form-group">
				<label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="custom-select selevt">
                <option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
                <option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
			</div>
		</form>
	</div>
	
	<?php if(isset($id) && $id > 0): ?>
	<!-- Account Balances & Payment Status Section -->
	<div class="card card-outline card-primary mt-3">
		<div class="card-header">
			<h3 class="card-title">Account Balances & Payment Status</h3>
		</div>
		<div class="card-body">
			<?php
			// Load CustomerAccountBalance class if available
			if(file_exists(base_app.'classes/CustomerAccountBalance.php')){
				require_once(base_app.'classes/CustomerAccountBalance.php');
				$accountBalance = new CustomerAccountBalance($conn);
				$accounts = $accountBalance->getCustomerAccounts($id);
				
				if(count($accounts) > 0):
			?>
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-sm">
					<thead>
						<tr>
							<th>Item Purchased</th>
							<th>Total Price</th>
							<th>Paid Amount</th>
							<th>Remaining Balance</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($accounts as $account): 
							// Check and apply late fees
							$accountBalance->checkAndApplyLateFees($account['id']);
							// Refresh account data
							$account = $accountBalance->getAccountInfo($account['id']);
							$schedule = $accountBalance->getPaymentSchedule($account['id']);
							$has_overdue = false;
							$max_days_overdue = 0;
							foreach($schedule as $sched){
								if($sched['payment_status'] == 'Late'){
									$has_overdue = true;
									$days_overdue = floor((strtotime('today') - strtotime($sched['due_date'])) / (60*60*24));
									if($days_overdue > $max_days_overdue) $max_days_overdue = $days_overdue;
								}
							}
						?>
						<tr>
							<td><?php echo htmlspecialchars($account['item_purchased']) ?></td>
							<td class="text-right"><strong>₱<?php echo number_format($account['total_price'], 2) ?></strong></td>
							<td class="text-right text-success"><strong>₱<?php echo number_format($account['paid_amount'], 2) ?></strong></td>
							<td class="text-right text-danger"><strong>₱<?php echo number_format($account['remaining_balance'], 2) ?></strong></td>
							<td class="text-center">
								<?php 
								if($account['status'] == 'paid' || $account['remaining_balance'] <= 0){
									echo '<span class="badge badge-success">🟢 Fully Paid</span>';
								} elseif($has_overdue && $max_days_overdue > 0){
									$late_fee_total = array_sum(array_column($schedule, 'late_fee'));
									echo '<span class="badge badge-danger">🔴 Late: '.$max_days_overdue.'d</span>';
									if($late_fee_total > 0){
										echo ' <small class="text-danger">+₱'.number_format($late_fee_total, 2).' late fee</small>';
									}
								} elseif($account['status'] == 'active'){
									echo '<span class="badge badge-primary">🟦 Active</span>';
								} else {
									echo '<span class="badge badge-secondary">⚪ '.ucfirst($account['status']).'</span>';
								}
								?>
							</td>
							<td class="text-center">
								<a href="javascript:void(0)" class="btn btn-sm btn-info view_account_details" data-id="<?php echo $account['id'] ?>">
									<i class="fa fa-eye"></i> View Details
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php else: ?>
			<div class="alert alert-info">
				<i class="fa fa-info-circle"></i> No account balances found for this customer.
			</div>
			<?php endif; ?>
			<?php } else { ?>
			<div class="alert alert-warning">
				<i class="fa fa-exclamation-triangle"></i> CustomerAccountBalance class not found.
			</div>
			<?php } ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="client-form">Save</button>
		<a class="btn btn-flat btn-default" href="./?page=clients">Cancel</a>
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
	$(document).ready(function(){
		$('.pass_type').click(function(){
            var type = $(this).attr('data-type')
            if(type == 'password'){
                $(this).attr('data-type','text')
                $(this).closest('.input-group').find('input').attr('type',"text")
                $(this).removeClass("fa-eye-slash")
                $(this).addClass("fa-eye")
            }else{
                $(this).attr('data-type','password')
                $(this).closest('.input-group').find('input').attr('type',"password")
                $(this).removeClass("fa-eye")
                $(this).addClass("fa-eye-slash")
            }
        })
		$('#client-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Users.php?f=save_client",
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
						alert_toast(resp.msg || "Client data saved successfully.", 'success');
						setTimeout(() => {
							location.href = "./?page=clients";
						}, 1500);
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
		    
		// View account details
		$(document).on('click', '.view_account_details', function(){
			var account_id = $(this).data('id');
			if(account_id){
				uni_modal("Account Details", "customer_account_balances/view_account.php?id="+account_id, "large");
			}
		});
	})
</script>