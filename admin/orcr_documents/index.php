<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">OR/CR Documents Management</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-default" type="button" id="print_reports"><span class="fa fa-print"></span> Print Report</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="row mb-3">
				<div class="col-md-3">
					<div class="info-box bg-primary">
						<span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Documents</span>
							<span class="info-box-number">
								<?php 
									try {
										$total_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents")->fetch_assoc()['total'];
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
										$released_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'released'")->fetch_assoc()['total'];
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
										$pending_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'pending'")->fetch_assoc()['total'];
										echo number_format($pending_docs);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-danger">
						<span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Expired</span>
							<span class="info-box-number">
								<?php 
									try {
										$expired_docs = $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'expired'")->fetch_assoc()['total'];
										echo number_format($expired_docs);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
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
						<label>Document Status</label>
						<select name="status" class="form-control" required>
							<option value="pending">Pending</option>
							<option value="released">Released</option>
							<option value="expired">Expired</option>
						</select>
					</div>
					<div class="form-group">
						<label>Release Date</label>
						<input type="date" name="release_date" class="form-control">
					</div>
					<div class="form-group">
						<label>Remarks</label>
						<textarea name="remarks" class="form-control" rows="3"></textarea>
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
		
		$('.update_status').click(function(){
			var id = $(this).attr('data-id');
			var status = $(this).attr('data-status');
			
			$('#update_document_id').val(id);
			$('select[name="status"]').val(status);
			$('#updateStatusModal').modal('show');
		});
		
		$('.delete_document').click(function(){
			var id = $(this).attr('data-id');
			_conf("Are you sure to delete this document?","delete_document",[id]);
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
		
		$('#print_reports').click(function(){
			var nw = window.open("print_orcr_documents.php","_blank","width=800,height=600")
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
