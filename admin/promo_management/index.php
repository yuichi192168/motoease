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
										<div class="btn-group" role="group">
											<button class="btn btn-sm btn-info" onclick="editPromo(<?php echo $row['id'] ?>, '<?php echo htmlspecialchars($row['title'], ENT_QUOTES) ?>', '<?php echo htmlspecialchars($row['description'], ENT_QUOTES) ?>')" title="Edit">
												<i class="fa fa-edit"></i>
											</button>
											<button class="btn btn-sm btn-<?php echo $row['is_active'] ? 'warning' : 'success' ?>" onclick="togglePromoStatus(<?php echo $row['id'] ?>, <?php echo $row['is_active'] ?>)" title="<?php echo $row['is_active'] ? 'Deactivate' : 'Activate' ?>">
												<i class="fa fa-<?php echo $row['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
											</button>
											<button class="btn btn-sm btn-danger" onclick="deletePromo(<?php echo $row['id'] ?>)" title="Delete">
												<i class="fa fa-trash"></i>
											</button>
										</div>
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
										<div class="btn-group" role="group">
											<button class="btn btn-sm btn-info" onclick="editCustomer(<?php echo $row['id'] ?>, '<?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES) ?>', '<?php echo htmlspecialchars($row['motorcycle_model'], ENT_QUOTES) ?>', '<?php echo $row['purchase_date'] ?>', '<?php echo htmlspecialchars($row['testimonial'], ENT_QUOTES) ?>')" title="Edit">
												<i class="fa fa-edit"></i>
											</button>
											<button class="btn btn-sm btn-<?php echo $row['is_active'] ? 'warning' : 'success' ?>" onclick="toggleCustomerStatus(<?php echo $row['id'] ?>, <?php echo $row['is_active'] ?>)" title="<?php echo $row['is_active'] ? 'Deactivate' : 'Activate' ?>">
												<i class="fa fa-<?php echo $row['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
											</button>
											<button class="btn btn-sm btn-danger" onclick="deleteCustomer(<?php echo $row['id'] ?>)" title="Delete">
												<i class="fa fa-trash"></i>
											</button>
										</div>
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

<!-- Edit Promo Modal -->
<div class="modal fade" id="editPromoModal" tabindex="-1" role="dialog" aria-labelledby="editPromoModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editPromoModalLabel">Edit Promo Image</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="editPromoForm">
					<input type="hidden" id="edit_promo_id" name="id">
					<div class="form-group">
						<label for="edit_promo_title">Title</label>
						<input type="text" class="form-control" id="edit_promo_title" name="title" required>
					</div>
					<div class="form-group">
						<label for="edit_promo_description">Description</label>
						<textarea class="form-control" id="edit_promo_description" name="description" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" onclick="updatePromo()">Update Promo</button>
			</div>
		</div>
	</div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="editCustomerModalLabel">Edit Customer Image</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="editCustomerForm">
					<input type="hidden" id="edit_customer_id" name="id">
					<div class="form-group">
						<label for="edit_customer_name">Customer Name</label>
						<input type="text" class="form-control" id="edit_customer_name" name="customer_name" required>
					</div>
					<div class="form-group">
						<label for="edit_motorcycle_model">Motorcycle Model</label>
						<input type="text" class="form-control" id="edit_motorcycle_model" name="motorcycle_model" required>
					</div>
					<div class="form-group">
						<label for="edit_purchase_date">Purchase Date</label>
						<input type="date" class="form-control" id="edit_purchase_date" name="purchase_date">
					</div>
					<div class="form-group">
						<label for="edit_testimonial">Testimonial</label>
						<textarea class="form-control" id="edit_testimonial" name="testimonial" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" onclick="updateCustomer()">Update Customer</button>
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

function editPromo(id, title, description) {
	$('#edit_promo_id').val(id);
	$('#edit_promo_title').val(title);
	$('#edit_promo_description').val(description);
	$('#editPromoModal').modal('show');
}

function updatePromo() {
	start_loader();
	$.ajax({
		url: _base_url_ + 'classes/Master.php?f=update_promo',
		method: 'POST',
		data: $('#editPromoForm').serialize(),
		dataType: 'json',
		error: err => {
			console.log(err);
			alert_toast("An error occurred", 'error');
			end_loader();
		},
		success: function(resp) {
			if(typeof resp == 'object' && resp.status == 'success') {
				$('#editPromoModal').modal('hide');
				location.reload();
			} else {
				alert_toast("An error occurred", 'error');
				end_loader();
			}
		}
	});
}

function editCustomer(id, customer_name, motorcycle_model, purchase_date, testimonial) {
	$('#edit_customer_id').val(id);
	$('#edit_customer_name').val(customer_name);
	$('#edit_motorcycle_model').val(motorcycle_model);
	$('#edit_purchase_date').val(purchase_date);
	$('#edit_testimonial').val(testimonial);
	$('#editCustomerModal').modal('show');
}

function updateCustomer() {
	start_loader();
	$.ajax({
		url: _base_url_ + 'classes/Master.php?f=update_customer',
		method: 'POST',
		data: $('#editCustomerForm').serialize(),
		dataType: 'json',
		error: err => {
			console.log(err);
			alert_toast("An error occurred", 'error');
			end_loader();
		},
		success: function(resp) {
			if(typeof resp == 'object' && resp.status == 'success') {
				$('#editCustomerModal').modal('hide');
				location.reload();
			} else {
				alert_toast("An error occurred", 'error');
				end_loader();
			}
		}
	});
}
</script>

