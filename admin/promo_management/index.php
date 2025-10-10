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
	.promo-img, .customer-img{
		height: 100px;
		width: 100px;
		object-fit: cover;
		border-radius: 5px;
	}
</style>

<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-header">
			<h5 class="card-title">Promo & Customer Images Management</h5>
		</div>
		<div class="card-body">
			<!-- Promo Images Management -->
			<div class="row mb-4">
				<div class="col-12">
					<h6 class="text-primary">Promo Images</h6>
					<div class="table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Image</th>
									<th>Title</th>
									<th>Description</th>
									<th>Status</th>
									<th>Date Created</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$promo_qry = $conn->query("SELECT * FROM promo_images ORDER BY date_created DESC");
								while($row = $promo_qry->fetch_assoc()):
								?>
								<tr>
									<td>
										<img src="<?php echo validate_image($row['image_path']) ?>" alt="Promo Image" class="promo-img">
									</td>
									<td><?php echo htmlspecialchars($row['title']) ?></td>
									<td><?php echo htmlspecialchars($row['description']) ?></td>
									<td>
										<span class="badge badge-<?php echo $row['is_active'] ? 'success' : 'danger' ?>">
											<?php echo $row['is_active'] ? 'Active' : 'Inactive' ?>
										</span>
									</td>
									<td><?php echo date('M d, Y', strtotime($row['date_created'])) ?></td>
									<td>
										<button class="btn btn-sm btn-primary" onclick="togglePromoStatus(<?php echo $row['id'] ?>, <?php echo $row['is_active'] ?>)">
											<?php echo $row['is_active'] ? 'Deactivate' : 'Activate' ?>
										</button>
										<button class="btn btn-sm btn-danger" onclick="deletePromo(<?php echo $row['id'] ?>)">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Customer Images Management -->
			<div class="row">
				<div class="col-12">
					<h6 class="text-primary">Customer Purchase Images</h6>
					<div class="table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Image</th>
									<th>Customer Name</th>
									<th>Motorcycle Model</th>
									<th>Purchase Date</th>
									<th>Status</th>
									<th>Date Created</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$customer_qry = $conn->query("SELECT * FROM customer_purchase_images ORDER BY date_created DESC");
								while($row = $customer_qry->fetch_assoc()):
								?>
								<tr>
									<td>
										<img src="<?php echo validate_image($row['image_path']) ?>" alt="Customer Image" class="customer-img">
									</td>
									<td><?php echo htmlspecialchars($row['customer_name']) ?></td>
									<td><?php echo htmlspecialchars($row['motorcycle_model']) ?></td>
									<td><?php echo $row['purchase_date'] ? date('M d, Y', strtotime($row['purchase_date'])) : 'N/A' ?></td>
									<td>
										<span class="badge badge-<?php echo $row['is_active'] ? 'success' : 'danger' ?>">
											<?php echo $row['is_active'] ? 'Active' : 'Inactive' ?>
										</span>
									</td>
									<td><?php echo date('M d, Y', strtotime($row['date_created'])) ?></td>
									<td>
										<button class="btn btn-sm btn-primary" onclick="toggleCustomerStatus(<?php echo $row['id'] ?>, <?php echo $row['is_active'] ?>)">
											<?php echo $row['is_active'] ? 'Deactivate' : 'Activate' ?>
										</button>
										<button class="btn btn-sm btn-danger" onclick="deleteCustomer(<?php echo $row['id'] ?>)">Delete</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function togglePromoStatus(id, current_status) {
	var new_status = current_status ? 0 : 1;
	start_loader();
	$.ajax({
		url: _base_url_ + 'classes/Master.php?f=toggle_promo_status',
		method: 'POST',
		data: {id: id, status: new_status},
		dataType: 'json',
		error: err => {
			console.log(err);
			alert_toast("An error occurred", 'error');
			end_loader();
		},
		success: function(resp) {
			if(typeof resp == 'object' && resp.status == 'success') {
				location.reload();
			} else {
				alert_toast("An error occurred", 'error');
				end_loader();
			}
		}
	});
}

function deletePromo(id) {
	_conf("Are you sure to delete this promo image?", "delete_promo", [id]);
}

function delete_promo(id) {
	start_loader();
	$.ajax({
		url: _base_url_ + 'classes/Master.php?f=delete_promo',
		method: 'POST',
		data: {id: id},
		dataType: 'json',
		error: err => {
			console.log(err);
			alert_toast("An error occurred", 'error');
			end_loader();
		},
		success: function(resp) {
			if(typeof resp == 'object' && resp.status == 'success') {
				location.reload();
			} else {
				alert_toast("An error occurred", 'error');
				end_loader();
			}
		}
	});
}

function toggleCustomerStatus(id, current_status) {
	var new_status = current_status ? 0 : 1;
	start_loader();
	$.ajax({
		url: _base_url_ + 'classes/Master.php?f=toggle_customer_status',
		method: 'POST',
		data: {id: id, status: new_status},
		dataType: 'json',
		error: err => {
			console.log(err);
			alert_toast("An error occurred", 'error');
			end_loader();
		},
		success: function(resp) {
			if(typeof resp == 'object' && resp.status == 'success') {
				location.reload();
			} else {
				alert_toast("An error occurred", 'error');
				end_loader();
			}
		}
	});
}

function deleteCustomer(id) {
	_conf("Are you sure to delete this customer image?", "delete_customer", [id]);
}

function delete_customer(id) {
	start_loader();
	$.ajax({
		url: _base_url_ + 'classes/Master.php?f=delete_customer',
		method: 'POST',
		data: {id: id},
		dataType: 'json',
		error: err => {
			console.log(err);
			alert_toast("An error occurred", 'error');
			end_loader();
		},
		success: function(resp) {
			if(typeof resp == 'object' && resp.status == 'success') {
				location.reload();
			} else {
				alert_toast("An error occurred", 'error');
				end_loader();
			}
		}
	});
}
</script>

