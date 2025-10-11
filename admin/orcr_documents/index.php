<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">OR/CR Documents Management</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-primary" type="button" id="add_document"><span class="fa fa-plus"></span> Add Document</button>
			<button class="btn btn-flat btn-sm btn-default" type="button" id="print_reports"><span class="fa fa-print"></span> Print Report</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- Filter Section -->
			<div class="row mb-3">
				<div class="col-md-12">
					<div class="card card-outline card-info">
						<div class="card-header">
							<h5 class="card-title">Filter Options</h5>
						</div>
						<div class="card-body">
							<form id="filter-form">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Date Start</label>
											<input type="date" class="form-control form-control-sm" name="date_start" value="<?= date('Y-m-d', strtotime('-30 days')) ?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Date End</label>
											<input type="date" class="form-control form-control-sm" name="date_end" value="<?= date('Y-m-d') ?>">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Status</label>
											<select class="form-control form-control-sm" name="status">
												<option value="">All Status</option>
												<option value="pending">Pending</option>
												<option value="released">Released</option>
												<option value="expired">Expired</option>
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>&nbsp;</label>
											<div>
												<button type="submit" class="btn btn-primary btn-sm">
													<i class="fa fa-filter"></i> Filter
												</button>
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row mb-3">
				<div class="col-md-3">
					<div class="info-box bg-primary">
						<span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Documents</span>
							<span class="info-box-number">
								<?php 
									try {
										$total_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status != 'expired'")->fetch_assoc()['total'];
										echo number_format($total_docs);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-success">
						<span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Released</span>
							<span class="info-box-number">
								<?php 
									try {
										$released_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'released' AND status != 'expired'")->fetch_assoc()['total'];
										echo number_format($released_docs);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-warning">
						<span class="info-box-icon"><i class="fas fa-clock"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Pending</span>
							<span class="info-box-number">
								<?php 
									try {
										$pending_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'pending' AND status != 'expired'")->fetch_assoc()['total'];
										echo number_format($pending_docs);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
				</div>
			</div>
			
			<div id="printable">
				<!-- Header with dual logos -->
				<div class="report-header" style="display:flex; justify-content: space-between; align-items: center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;">
					<!-- Main Logo on the left -->
					<div style="flex:0 0 auto; margin-right:20px;">
						<img src="<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">
					</div>

					<!-- Centered Organization Name -->
					<div style="flex:1; text-align:center;">
						<h2 style="margin:0; text-transform:uppercase; font-weight:bold;"><?php echo $_settings->info('name') ?></h2>
						<h4 style="margin:0;"><b>OR/CR Documents Management Report</b></h4>
						<p style="margin:0;">Generated on: <?php echo date('F d, Y \a\t H:i A') ?></p>
					</div>

					<!-- Secondary Logo on the right -->
					<div style="flex:0 0 auto; margin-left:20px;">
						<img src="<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" style="width:100px; height:100px; object-fit:contain;">
					</div>
				</div>

				<!-- Statistics Section -->
				<div class="stats-section" style="display:flex; justify-content:space-around; margin:20px 0; padding:15px; background-color:#f8f9fa; border:1px solid #dee2e6;">
					<div class="stat-item" style="text-align:center;">
						<div class="stat-number" style="font-size:24px; font-weight:bold; color:#007bff;">
							<?php 
								try {
									$total_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status != 'expired'")->fetch_assoc()['total'];
									echo number_format($total_docs);
								} catch (Exception $e) {
									echo "0";
								}
							?>
						</div>
						<div class="stat-label" style="font-size:11px; color:#666; margin-top:5px;">Total Documents</div>
					</div>
					<div class="stat-item" style="text-align:center;">
						<div class="stat-number" style="font-size:24px; font-weight:bold; color:#007bff;">
							<?php 
								try {
									$released_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'released' AND status != 'expired'")->fetch_assoc()['total'];
									echo number_format($released_docs);
								} catch (Exception $e) {
									echo "0";
								}
							?>
						</div>
						<div class="stat-label" style="font-size:11px; color:#666; margin-top:5px;">Released</div>
					</div>
					<div class="stat-item" style="text-align:center;">
						<div class="stat-number" style="font-size:24px; font-weight:bold; color:#007bff;">
							<?php 
								try {
									$pending_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'pending' AND status != 'expired'")->fetch_assoc()['total'];
									echo number_format($pending_docs);
								} catch (Exception $e) {
									echo "0";
								}
							?>
						</div>
						<div class="stat-label" style="font-size:11px; color:#666; margin-top:5px;">Pending</div>
					</div>
				</div>

				<!-- Documents Table -->
				<table class="documents-table" style="width:100%; border-collapse:collapse; margin:20px 0;">
					<thead>
						<tr>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">#</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Customer</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Document Type</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Document Number</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Plate Number</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Release Date</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Status</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Date Uploaded</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						try {
							$i = 1;
							$qry = $conn->query("SELECT d.*, 
												CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) as customer_name
												FROM `or_cr_documents` d 
												INNER JOIN client_list c ON d.client_id = c.id 
												WHERE d.status != 'expired' 
												ORDER BY d.date_created DESC");
							while($row = $qry->fetch_assoc()):
								foreach($row as $k=> $v){
									$row[$k] = trim(stripslashes($v));
								}
						?>
							<tr>
								<td style="border:1px solid #ddd; padding:8px; text-align:center;"><?php echo $i++; ?></td>
								<td style="border:1px solid #ddd; padding:8px;"><?php echo ucwords($row['customer_name']) ?></td>
								<td style="border:1px solid #ddd; padding:8px; text-align:center;">
									<span style="padding:2px 6px; border-radius:3px; font-size:10px; font-weight:bold; background-color:<?= $row['document_type'] == 'or' ? '#cce5ff' : '#d1ecf1' ?>; color:<?= $row['document_type'] == 'or' ? '#004085' : '#0c5460' ?>;">
										<?= strtoupper($row['document_type']) ?>
									</span>
								</td>
								<td style="border:1px solid #ddd; padding:8px;"><?php echo $row['document_number'] ?></td>
								<td style="border:1px solid #ddd; padding:8px;"><?php echo $row['plate_number'] ?: 'N/A' ?></td>
								<td style="border:1px solid #ddd; padding:8px; text-align:center;">
									<?php 
									if($row['release_date']){
										echo date("M d, Y", strtotime($row['release_date']));
									} else {
										echo '<span style="color: #999;">Not set</span>';
									}
									?>
								</td>
								<td style="border:1px solid #ddd; padding:8px; text-align:center;">
									<span style="padding:2px 8px; border-radius:3px; font-size:10px; font-weight:bold; background-color:<?= $row['status'] == 'released' ? '#d4edda' : ($row['status'] == 'expired' ? '#f8d7da' : '#fff3cd') ?>; color:<?= $row['status'] == 'released' ? '#155724' : ($row['status'] == 'expired' ? '#721c24' : '#856404') ?>;">
										<?= ucfirst($row['status']) ?>
									</span>
								</td>
								<td style="border:1px solid #ddd; padding:8px; text-align:center;"><?php echo date("M d, Y", strtotime($row['date_created'])) ?></td>
							</tr>
						<?php endwhile; ?>
						<?php if($qry->num_rows <= 0): ?>
						<tr>
							<td colspan="8" style="border:1px solid #ddd; padding:8px; text-align:center;">No documents found.</td>
						</tr>
						<?php endif; ?>
						<?php } catch (Exception $e) { ?>
						<tr>
							<td colspan="8" style="border:1px solid #ddd; padding:8px; text-align:center; color:red;">Error loading documents: <?php echo $e->getMessage(); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>

				<!-- Footer Information -->
				<div class="footer-info" style="margin-top:30px; padding:15px; border-top:1px solid #ddd; text-align:center; font-size:10px; color:#666;">
					<p><strong>Company Information:</strong></p>
					<p>📍 National Highway Brgy. Parian, Calamba City, Laguna</p>
					<p>📞 0948-235-3207 | ✉️ starhondacalamba55@gmail.com</p>
					<p>📘 Facebook: @starhondacalambabranch</p>
					<hr style="margin: 10px 0;">
					<p>This report was generated on <?= date('F d, Y \a\t H:i A') ?> by <?= ucwords($_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname')) ?></p>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-bordered table-stripped">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Customer</th>
							<th>Document Type</th>
							<th>Document Number</th>
							<th>Plate Number</th>
							<th>Release Date</th>
							<th>Status</th>
							<th>Date Uploaded</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						try {
							$i = 1;
							$qry = $conn->query("SELECT d.*, 
												CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) as customer_name
												FROM `or_cr_documents` d 
												INNER JOIN client_list c ON d.client_id = c.id 
												WHERE d.status != 'expired' 
												ORDER BY d.date_created DESC");
							while($row = $qry->fetch_assoc()):
								foreach($row as $k=> $v){
									$row[$k] = trim(stripslashes($v));
								}
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td><?php echo ucwords($row['customer_name']) ?></td>
								<td>
									<span class="badge badge-<?= $row['document_type'] == 'or' ? 'primary' : 'info' ?>">
										<?= strtoupper($row['document_type']) ?>
									</span>
								</td>
								<td><?php echo $row['document_number'] ?></td>
								<td><?php echo $row['plate_number'] ?: 'N/A' ?></td>
								<td>
									<?php 
									if($row['release_date']){
										echo date("M d, Y", strtotime($row['release_date']));
									} else {
										echo '<span class="text-muted">Not set</span>';
									}
									?>
								</td>
								<td class="text-center">
									<?php if($row['status'] == 'released'): ?>
										<span class="badge badge-success">Released</span>
									<?php elseif($row['status'] == 'expired'): ?>
										<span class="badge badge-danger">Expired</span>
									<?php else: ?>
										<span class="badge badge-warning">Pending</span>
									<?php endif; ?>
								</td>
								<td><?php echo date("M d, Y", strtotime($row['date_created'])) ?></td>
								<td align="center">
									<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
										Action
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<?php if($row['file_path']): ?>
										<a class="dropdown-item" href="<?= validate_image($row['file_path']) ?>" target="_blank">
											<span class="fa fa-eye text-primary"></span> View Document
										</a>
										<?php endif; ?>
										<a class="dropdown-item update_status" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-status="<?php echo $row['status'] ?>">
											<span class="fa fa-edit text-info"></span> Update Status
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_document" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
											<span class="fa fa-trash text-danger"></span> Delete
										</a>
									</div>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php if($qry->num_rows <= 0): ?>
						<tr>
							<td colspan="9" class="text-center">No documents found.</td>
						</tr>
						<?php endif; ?>
						<?php } catch (Exception $e) { ?>
						<tr>
							<td colspan="9" class="text-center text-danger">Error loading documents: <?php echo $e->getMessage(); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Update Document Status</h5>
				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<form id="updateStatusForm">
				<div class="modal-body">
					<input type="hidden" name="document_id" id="update_document_id">
					<div class="form-group">
						<label for="update_status">Document Status</label>
						<select name="status" id="update_status" class="form-control" required>
							<option value="pending">Pending</option>
							<option value="released">Released</option>
							<option value="expired">Expired</option>
						</select>
					</div>
					<div class="form-group">
						<label for="update_release_date">Release Date</label>
						<input type="date" name="release_date" id="update_release_date" class="form-control">
					</div>
					<div class="form-group">
						<label for="update_remarks">Remarks</label>
						<textarea name="remarks" id="update_remarks" class="form-control" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Update Status</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add OR/CR Document</h5>
				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<form id="addDocumentForm" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="form-group">
						<label for="client_id">Customer</label>
						<select name="client_id" id="client_id" class="form-control" required>
							<option value="">Select Customer</option>
							<?php 
							$customers = $conn->query("SELECT * FROM client_list WHERE delete_flag = 0 ORDER BY lastname, firstname");
							while($customer = $customers->fetch_assoc()):
							?>
							<option value="<?= $customer['id'] ?>"><?= ucwords($customer['lastname'] . ', ' . $customer['firstname'] . ' ' . $customer['middlename']) ?></option>
							<?php endwhile; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="document_type">Document Type</label>
						<select name="document_type" id="document_type" class="form-control" required>
							<option value="">Select Document Type</option>
							<option value="or">Original Receipt (OR)</option>
							<option value="cr">Certificate of Registration (CR)</option>
						</select>
					</div>
					<div class="form-group">
						<label for="document_number">Document Number</label>
						<input type="text" name="document_number" id="document_number" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="plate_number">Plate Number</label>
						<input type="text" name="plate_number" id="plate_number" class="form-control">
					</div>
					<div class="form-group">
						<label for="vehicle_model">Vehicle Model</label>
						<input type="text" name="vehicle_model" id="vehicle_model" class="form-control">
					</div>
					<div class="form-group">
						<label for="vehicle_brand">Vehicle Brand</label>
						<input type="text" name="vehicle_brand" id="vehicle_brand" class="form-control">
					</div>
					<div class="form-group">
						<label for="release_date">Release Date</label>
						<input type="date" name="release_date" id="release_date" class="form-control">
					</div>
					<div class="form-group">
						<label for="expiry_date">Expiry Date</label>
						<input type="date" name="expiry_date" id="expiry_date" class="form-control">
					</div>
					<div class="form-group">
						<label for="document_file">Document File</label>
						<input type="file" name="document_file" id="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
						<small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG</small>
					</div>
					<div class="form-group">
						<label for="status">Status</label>
						<select name="status" id="status" class="form-control" required>
							<option value="pending">Pending</option>
							<option value="released">Released</option>
							<option value="expired">Expired</option>
						</select>
					</div>
					<div class="form-group">
						<label for="remarks">Remarks</label>
						<textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Add Document</button>
				</div>
			</form>
		</div>
	</div>
</div>

<style>
/* Fix scrolling issues */
.content-wrapper {
    overflow-y: auto !important;
    height: calc(100vh - 60px) !important;
}

.card-body {
    overflow-x: auto;
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
}

/* Ensure proper spacing */
.info-box {
    margin-bottom: 15px;
}

/* Fix modal scrolling */
.modal-body {
    max-height: 60vh;
    overflow-y: auto;
}
</style>

<script>
	$(document).ready(function(){
		$('.table').dataTable({
			"scrollX": true,
			"scrollY": "400px",
			"scrollCollapse": true
		});
		
		$('#add_document').click(function(){
			$('#addDocumentModal').modal('show');
		});
		
		$('.update_status').click(function(){
			var id = $(this).attr('data-id');
			var status = $(this).attr('data-status');
			
			$('#update_document_id').val(id);
			$('#update_status').val(status);
			$('#updateStatusModal').modal('show');
		});
		
		$('.delete_document').click(function(){
			var id = $(this).attr('data-id');
			_conf("Are you sure to delete this document?","delete_document",[id]);
		});
		
		$('#addDocumentForm').submit(function(e){
			e.preventDefault();
			console.log('Add document form submitted');
			start_loader();
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=add_document",
				method: "POST",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(resp){
					console.log('Response:', resp);
					if(resp.status == 'success'){
						$('#addDocumentModal').modal('hide');
						alert_toast(resp.msg, 'success');
						setTimeout(function(){
							location.reload();
						}, 1000);
					} else {
						alert_toast(resp.msg, 'error');
					}
					end_loader();
				},
				error: function(xhr, status, error){
					console.log('AJAX Error:', error);
					console.log('Response:', xhr.responseText);
					alert_toast('An error occurred while adding document', 'error');
					end_loader();
				}
			});
		});
		
		$('#updateStatusForm').submit(function(e){
			e.preventDefault();
			start_loader();
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=update_document_status",
				method: "POST",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#updateStatusModal').modal('hide');
						location.reload();
					} else {
						alert_toast(resp.msg, 'error');
					}
					end_loader();
				}
			});
		});
		
		$('#filter-form').submit(function(e){
			e.preventDefault();
			var formData = $(this).serialize();
			location.href = "./?page=orcr_documents&" + formData;
		});
		
		$('#print_reports').click(function(){
			// Clone printable content
			var rep = $('#printable').clone();
			var ns = '<style>' +
						'body{margin:40px;font-size:14px;min-height:100vh;position:relative;}' +
						'table{border-collapse:collapse;width:100%;}' +
						'table th, table td{border:1px solid #000;padding:5px;}' +
						'.text-center{text-align:center;}' +
						'.text-right{text-align:right;}' +
						'.report-header{display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;}' +
						'.report-header h2{text-transform:uppercase;font-weight:bold;margin:0;}' +
						'.report-header h4{text-transform:uppercase;font-weight:bold;margin:0;}' +
						'.report-header p{margin:0;}' +
						'.stats-section{display:flex; justify-content:space-around; margin:20px 0; padding:15px; background-color:#f8f9fa; border:1px solid #dee2e6;}' +
						'.stat-item{text-align:center;}' +
						'.stat-number{font-size:24px; font-weight:bold; color:#007bff;}' +
						'.stat-label{font-size:11px; color:#666; margin-top:5px;}' +
						'.report-info{margin:15px 0; padding:10px; background-color:#e9ecef; border-left:4px solid #007bff;}' +
						'.documents-table{width:100%; border-collapse:collapse; margin:20px 0;}' +
						'.documents-table th, .documents-table td{border:1px solid #ddd; padding:8px; text-align:left; font-size:11px;}' +
						'.documents-table th{background-color:#f8f9fa; font-weight:bold; text-align:center;}' +
						'.status-badge{padding:2px 8px; border-radius:3px; font-size:10px; font-weight:bold;}' +
						'.status-released{background-color:#d4edda; color:#155724;}' +
						'.status-pending{background-color:#fff3cd; color:#856404;}' +
						'.status-expired{background-color:#f8d7da; color:#721c24;}' +
						'.doc-type-badge{padding:2px 6px; border-radius:3px; font-size:10px; font-weight:bold;}' +
						'.doc-type-or{background-color:#cce5ff; color:#004085;}' +
						'.doc-type-cr{background-color:#d1ecf1; color:#0c5460;}' +
						'.footer-info{position:fixed;bottom:0;left:0;right:0;margin-top:30px;padding:15px;border-top:1px solid #ddd;text-align:center;font-size:10px;color:#666;background-color:white;}' +
						'@media print { #filter-form, #print_reports { display:none !important; } .footer-info{position:fixed;bottom:0;left:0;right:0;margin:0;padding:15px;border-top:1px solid #ddd;text-align:center;font-size:10px;color:#666;background-color:white;page-break-inside:avoid;}' +
					'</style>';
			rep.prepend(ns);

			// Open new window
			var nw = window.open('', '_blank');
			nw.document.write('<html><head><title>OR/CR Documents Report</title></head><body>' + rep.html() + '</body></html>');
			nw.document.close();

			// Wait until content is fully loaded before printing
			nw.onload = function(){
				nw.focus();
				nw.print();
				setTimeout(function(){ nw.close(); }, 500);
			};
		});
	})
	
	function delete_document($id){
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_document",
			method: "POST",
			data: {document_id: $id},
			dataType: "json",
			success: function(resp){
				if(resp.status == 'success'){
					location.reload();
				} else {
					alert_toast(resp.msg, 'error');
				}
				end_loader();
			}
		});
	}
</script>
