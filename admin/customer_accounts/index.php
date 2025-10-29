<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Customer Account Balances</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-default" type="button" id="print_reports"><span class="fa fa-print"></span> Print Report</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-bordered table-stripped">
                    <colgroup>
						<col width="5%">
						<col width="20%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
                        <col width="10%">
                        <col width="15%">
					</colgroup>
					<thead>
                        <tr>
							<th>#</th>
							<th>Customer</th>
							<th>Total Balance</th>
							<th>Installment Plan</th>
							<th>Paid Amount</th>
							<th>Unpaid Amount</th>
                            <th>Credit App</th>
                            <th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						try {
							$i = 1;
                            $qry = $conn->query("SELECT c.*, 
                                                c.account_balance as total_balance,
                                                COALESCE(SUM(CASE WHEN o.status IN (4,6) THEN o.total_amount ELSE 0 END), 0) as paid_amount,
                                                COALESCE(SUM(CASE WHEN o.status IN (0,1,2,3) THEN o.total_amount ELSE 0 END), 0) as unpaid_amount
                                                FROM `client_list` c 
                                                LEFT JOIN order_list o ON c.id = o.client_id 
                                                WHERE c.delete_flag = 0 
                                                GROUP BY c.id 
                                                ORDER BY c.lastname, c.firstname");
							while($row = $qry->fetch_assoc()):
								$installment_plan = "₱" . number_format($row['total_balance'] / 6, 2) . "/month for 6 months";
								if($row['total_balance'] == 0) $installment_plan = "No balance";
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td>
									<strong><?php echo ucwords($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']) ?></strong><br>
									<small class="text-muted"><?php echo $row['email'] ?></small>
								</td>
								<td class="text-right">
									<strong>₱<?php echo number_format($row['total_balance'], 2) ?></strong>
								</td>
								<td class="text-center">
									<small><?php echo $installment_plan ?></small>
								</td>
								<td class="text-right text-success">
									<strong>₱<?php echo number_format($row['paid_amount'], 2) ?></strong>
								</td>
                            <td class="text-right text-danger">
									<strong>₱<?php echo number_format($row['unpaid_amount'], 2) ?></strong>
								</td>
                            <td class="text-center">
                                <?php if(isset($row['credit_application_completed'])): ?>
                                    <span class="badge <?php echo $row['credit_application_completed'] ? 'badge-success' : 'badge-warning' ?>">
                                        <?php echo $row['credit_application_completed'] ? 'Completed' : 'Required' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">N/A</span>
                                <?php endif; ?>
                            </td>
								<td align="center">
									<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
										Action
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<?php if($_settings->userdata('login_type') == 1): // Admin only ?>
										<a class="dropdown-item adjust_balance" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['lastname'] . ', ' . $row['firstname'] ?>">
											<span class="fa fa-edit text-primary"></span> Adjust Balance
										</a>
										<div class="dropdown-divider"></div>
										<?php endif; ?>
										<a class="dropdown-item upload_orcr" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['lastname'] . ', ' . $row['firstname'] ?>">
											<span class="fa fa-upload text-success"></span> Upload OR/CR
										</a>
										<a class="dropdown-item view_orcr" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['lastname'] . ', ' . $row['firstname'] ?>">
											<span class="fa fa-file-pdf text-warning"></span> View OR/CR
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item view_transactions" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
											<span class="fa fa-list text-info"></span> View Transactions
										</a>
									</div>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php if($qry->num_rows <= 0): ?>
						<tr>
							<td colspan="7" class="text-center">No customer accounts found.</td>
						</tr>
						<?php endif; ?>
						<?php } catch (Exception $e) { ?>
						<tr>
							<td colspan="7" class="text-center text-danger">Error loading customer accounts: <?php echo $e->getMessage(); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Adjust Balance Modal -->
<div class="modal fade" id="adjustBalanceModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Adjust Customer Balance</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="adjustBalanceForm">
					<input type="hidden" name="client_id" id="adjust_client_id">
					<div class="form-group">
						<label>Customer Name</label>
						<input type="text" class="form-control" id="adjust_customer_name" readonly>
					</div>
					<div class="form-group">
						<label>Current Balance</label>
						<input type="text" class="form-control" id="current_balance" readonly>
					</div>
					<div class="form-group">
						<label>Adjustment Type</label>
						<select name="adjustment_type" class="form-control" required>
							<option value="add">Add Amount</option>
							<option value="deduct">Subtract Amount</option>
							<option value="set">Set New Balance</option>
						</select>
					</div>
					<div class="form-group">
						<label>Amount</label>
						<input type="number" name="amount" class="form-control" step="0.01" min="0" required>
					</div>
					<div class="form-group">
						<label>Reason</label>
						<textarea name="reason" class="form-control" rows="3" placeholder="Reason for adjustment" required></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="submit" form="adjustBalanceForm" class="btn btn-primary">Save Changes</button>
			</div>
		</div>
	</div>
</div>

<!-- Upload OR/CR Modal -->
<div class="modal fade" id="uploadOrcrModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload OR/CR Documents</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="uploadOrcrForm" enctype="multipart/form-data">
					<input type="hidden" name="client_id" id="upload_client_id">
					<div class="form-group">
						<label>Customer Name</label>
						<input type="text" class="form-control" id="upload_customer_name" readonly>
					</div>
					<div class="form-group">
						<label>Document Number (Optional)</label>
						<input type="text" name="document_number" class="form-control" placeholder="Enter document number">
					</div>
					<div class="form-group">
						<label>Plate Number (Optional)</label>
						<input type="text" name="plate_number" class="form-control" placeholder="Enter plate number">
					</div>
					<div class="form-group">
						<label>Release Date (Optional)</label>
						<input type="date" name="release_date" class="form-control">
					</div>
					<div class="form-group">
						<label>Remarks (Optional)</label>
						<textarea name="remarks" class="form-control" rows="2" placeholder="Remarks"></textarea>
					</div>
					<div class="form-group">
						<label for="or_document">Official Receipt (OR)</label>
						<input type="file" name="or_document" id="or_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
						<small class="form-text text-muted">Upload PDF, JPG, or PNG file</small>
					</div>
					<div class="form-group">
						<label for="cr_document">Certificate of Registration (CR)</label>
						<input type="file" name="cr_document" id="cr_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
						<small class="form-text text-muted">Upload PDF, JPG, or PNG file</small>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="submit" form="uploadOrcrForm" class="btn btn-primary">Upload Documents</button>
			</div>
		</div>
	</div>
</div>

<!-- View OR/CR Modal -->
<div class="modal fade" id="viewOrcrModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">View OR/CR Documents</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="orcr_documents">
					<!-- Documents will be loaded here -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- OR/CR File Viewer Modal -->
<div class="modal fade" id="orcrFileViewer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">View Document</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="orcr_viewer_container" class="w-100">
                    <!-- dynamic: iframe or img inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
 </div>

<!-- View Transactions Modal -->
<div class="modal fade" id="viewTransactionsModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Customer Transactions</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="transactions_list">
					<!-- Transactions will be loaded here -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
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

/* Fix modal scrolling */
.modal-body {
    max-height: 60vh;
    overflow-y: auto;
}

/* Improve table readability */
.table th {
    position: sticky;
    top: 0;
    background: #f4f6f9;
    z-index: 10;
}
</style>

<script>
	$(document).ready(function(){
		$('.table').dataTable({
			"scrollX": true,
			"scrollY": "400px",
			"scrollCollapse": true
		});
		
		$('.adjust_balance').click(function(){
			var id = $(this).attr('data-id');
			var name = $(this).attr('data-name');
			
			// Get current balance
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=get_client_balance",
				method: "POST",
				data: {client_id: id},
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#adjust_client_id').val(id);
						$('#adjust_customer_name').val(name);
						$('#current_balance').val('₱' + parseFloat(resp.balance).toFixed(2));
						$('#adjustBalanceModal').modal('show');
					}
				}
			});
		});
		
		$('.upload_orcr').click(function(){
			var id = $(this).attr('data-id');
			var name = $(this).attr('data-name');
			
			$('#upload_client_id').val(id);
			$('#upload_customer_name').val(name);
			$('#uploadOrcrModal').modal('show');
		});
		
		$('.view_orcr').click(function(){
			var id = $(this).attr('data-id');
			var name = $(this).attr('data-name');
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=get_client_orcr",
				method: "POST",
				data: {client_id: id},
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#orcr_documents').html(resp.html);
						$('#viewOrcrModal').modal('show');
					} else {
						alert_toast(resp.msg, 'error');
					}
				}
			});
		});
		
		$('.view_transactions').click(function(){
			var id = $(this).attr('data-id');
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=get_client_transactions",
				method: "POST",
				data: {client_id: id},
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#transactions_list').html(resp.html);
						$('#viewTransactionsModal').modal('show');
					}
				}
			});
		});
		
		$('#adjustBalanceForm').submit(function(e){
			e.preventDefault();
			start_loader();
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=adjust_client_balance",
				method: "POST",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#adjustBalanceModal').modal('hide');
						location.reload();
					} else {
						alert_toast(resp.msg, 'error');
					}
					end_loader();
				}
			});
		});
		
		$('#uploadOrcrForm').submit(function(e){
			e.preventDefault();
			start_loader();
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=upload_client_orcr",
				method: "POST",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#uploadOrcrModal').modal('hide');
						alert_toast(resp.msg, 'success');
						location.reload();
					} else {
						alert_toast(resp.msg, 'error');
					}
					end_loader();
				}
			});
		});

		// Delegated handler to view a specific OR/CR file in a modal
		$(document).on('click', '.btn-view-orcr', function(){
			var file = $(this).attr('data-file') || '';
			var extAttr = ($(this).attr('data-ext') || '').toLowerCase();
			var pathOnly = file.split('?')[0] || file;
			var ext = extAttr || pathOnly.split('.').pop().toLowerCase();
			var html = '';
			if(file){
				if(ext === 'pdf'){
					html = '<iframe src="'+file+'" width="100%" height="500" style="border:1px solid #ddd"></iframe>';
				}else{
					html = '<img src="'+file+'" class="img-fluid" style="max-height:500px;border:1px solid #ddd">';
				}
			}else{
				html = '<div class="alert alert-secondary">No file to display.</div>';
			}
			$('#orcr_viewer_container').html(html);
			$('#orcrFileViewer').modal('show');
		});

		// Delegated handler to delete a document
		$(document).on('click', '.btn-delete-orcr', function(){
			var id = $(this).attr('data-id');
			if(!id) return;
			_confirm("Are you sure you want to delete this document?", function(){
				start_loader();
				$.ajax({
					url: _base_url_ + "classes/Master.php?f=delete_document",
					method: "POST",
					data: {document_id: id},
					dataType: "json",
					success: function(resp){
						if(resp && resp.status === 'success'){
							alert_toast('Document deleted','success');
							// refresh modal content
							$('.view_orcr:visible').click();
						}else{
							alert_toast(resp.msg || 'Delete failed','error');
						}
						end_loader();
					}
				});
			});
		});
		
		$('#print_reports').click(function(){
			var nw = window.open("print_customer_accounts.php","_blank","width=800,height=600")
		});
	})
</script>
