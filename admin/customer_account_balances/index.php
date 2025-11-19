<?php 
// config.php is already loaded by admin/index.php
// Only need to load the class file
if(file_exists(base_app.'classes/CustomerAccountBalance.php')){
	require_once(base_app.'classes/CustomerAccountBalance.php');
}else{
	die('CustomerAccountBalance class file not found');
}

if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success');
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Customer Account Balances</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-default" type="button" id="print_reports">
				<span class="fa fa-print"></span> Print Report
			</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-bordered table-stripped" id="account_list">
					<colgroup>
						<col width="3%">
						<col width="12%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="12%">
						<col width="8%">
						<col width="5%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Customer</th>
							<th>Installment Plan</th>
							<th>MonthlyPayment</th>
							<th>Due Date</th>
							<th>Paid Amount</th>
							<th>Unpaid Amount</th>
							<th>Status</th>
							<th>Credit App</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$accountBalance = new CustomerAccountBalance($conn);
						$i = 1;
						$qry = $conn->query("
							SELECT cab.*, 
								   CONCAT(cl.firstname, ' ', cl.middlename, ' ', cl.lastname) as customer_name,
								   cl.email, cl.contact, cl.credit_application_completed
							FROM customer_account_balances cab
							LEFT JOIN client_list cl ON cab.client_id = cl.id
							WHERE cab.status != 'closed'
							ORDER BY cab.created_at DESC
						");
						
						while($row = $qry->fetch_assoc()):
							// Check and apply late fees
							$accountBalance->checkAndApplyLateFees($row['id']);
							// Refresh row data after late fee check
							$refreshed = $accountBalance->getAccountInfo($row['id']);
							if($refreshed) $row = array_merge($row, $refreshed);
							
							// Get payment schedule once (used for both due date and status)
							$schedule_info = $accountBalance->getPaymentSchedule($row['id']);
							$next_due_date = null;
							foreach($schedule_info as $sched){
								if(!$next_due_date && in_array($sched['payment_status'], ['Unpaid', 'Partial', 'Late'])){
									$next_due_date = $sched['due_date'];
									break;
								}
							}
							
							// Format installment plan
							$installment_months = isset($row['installment_plan_months']) && $row['installment_plan_months'] > 0 ? $row['installment_plan_months'] : null;
							$installment_plan_display = $installment_months ? $installment_months . ' Months' : 'Full Payment';
							
							// Get monthly payment
							$monthly_payment = isset($row['monthly_payment_amount']) && $row['monthly_payment_amount'] > 0 ? $row['monthly_payment_amount'] : 0;
							
							// Credit application status
							$credit_app_status = isset($row['credit_application_completed']) && $row['credit_application_completed'] == 1;
							
							// Enhanced status calculation with late fee awareness
							$status_display = '';
							$has_overdue = false;
							$max_days_overdue = 0;
							
							foreach($schedule_info as $sched){
								if($sched['payment_status'] == 'Late'){
									$has_overdue = true;
									$days_overdue = floor((strtotime('today') - strtotime($sched['due_date'])) / (60*60*24));
									if($days_overdue > $max_days_overdue) $max_days_overdue = $days_overdue;
								}
							}
							
							if($row['status'] == 'paid' || $row['remaining_balance'] <= 0){
								$status_display = '<span class="badge badge-success">🟢 Fully Paid</span>';
							} elseif($has_overdue && $max_days_overdue > 0){
								$late_fee_total = array_sum(array_column($schedule_info, 'late_fee'));
								$status_display = '<span class="badge badge-danger">🔴 Late: '.$max_days_overdue.'d</span> <small class="text-danger">+₱'.number_format($late_fee_total, 2).' late fee</small>';
							} elseif($row['remaining_balance'] > 0 && $next_due_date){
								$status_display = '<span class="badge badge-warning">🟡 Pending / Due '.date('M d, Y', strtotime($next_due_date)).'</span>';
							} elseif($row['status'] == 'active'){
								$status_display = '<span class="badge badge-primary">🟦 Active</span>';
							} elseif($row['status'] == 'defaulted'){
								$status_display = '<span class="badge badge-danger">🔴 Defaulted</span>';
							} else {
								$status_display = '<span class="badge badge-secondary">⚪ '.ucfirst($row['status']).'</span>';
							}
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td>
									<strong><?php echo ucwords($row['customer_name']) ?></strong><br>
									<small class="text-muted"><?php echo $row['email'] ?></small>
								</td>
								<td class="text-center">
									<strong><?php echo $installment_plan_display ?></strong>
								</td>
								<td class="text-right">
									<?php if($monthly_payment > 0): ?>
										<strong>₱<?php echo number_format($monthly_payment, 2) ?></strong>
									<?php else: ?>
										<span class="text-muted">N/A</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<?php if($next_due_date): ?>
										<strong><?php echo date('M d, Y', strtotime($next_due_date)) ?></strong>
									<?php else: ?>
										<span class="text-muted">-</span>
									<?php endif; ?>
								</td>
								<td class="text-right text-success"><strong>₱<?php echo number_format($row['paid_amount'], 2) ?></strong></td>
								<td class="text-right text-danger"><strong>₱<?php echo number_format($row['remaining_balance'], 2) ?></strong></td>
								<td class="text-center">
									<?php echo $status_display ?>
								</td>
								<td class="text-center">
									<?php if($credit_app_status): ?>
										<span class="badge badge-success"><i class="fa fa-check-circle"></i> Completed</span>
									<?php else: ?>
										<span class="badge badge-warning"><i class="fa fa-exclamation-triangle"></i> Pending</span>
									<?php endif; ?>
								</td>
								<td align="center">
									<div class="btn-group">
										<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Action
											<span class="sr-only">Toggle Dropdown</span>
										</button>
										<div class="dropdown-menu dropdown-menu-right" role="menu">
											<a class="dropdown-item view_account" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo htmlspecialchars($row['customer_name']) ?>">
												<span class="fa fa-eye text-info"></span> View Details
											</a>
											<a class="dropdown-item add_payment" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo htmlspecialchars($row['customer_name']) ?>">
												<span class="fa fa-money-bill-wave text-success"></span> Add Payment
											</a>
											<?php if($_settings->userdata('login_type') == 1): // Admin only ?>
											<div class="dropdown-divider"></div>
											<a class="dropdown-item upload_orcr" href="javascript:void(0)" data-id="<?php echo $row['client_id'] ?>" data-name="<?php echo htmlspecialchars($row['customer_name']) ?>">
												<span class="fa fa-upload text-success"></span> Upload OR/CR
											</a>
											<a class="dropdown-item view_orcr" href="javascript:void(0)" data-id="<?php echo $row['client_id'] ?>" data-name="<?php echo htmlspecialchars($row['customer_name']) ?>">
												<span class="fa fa-file-pdf text-warning"></span> View OR/CR
											</a>
											<?php endif; ?>
										</div>
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

<!-- Account Details Modal -->
<div class="modal fade" id="account_modal" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Account Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="account_details"></div>
			</div>
		</div>
	</div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="payment_modal" role="dialog">
	<div class="modal-dialog modal-md modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Add Payment</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="payment_form">
					<input type="hidden" name="account_id" id="payment_account_id">
					<div class="form-group">
						<label for="schedule_id" class="control-label">Payment For</label>
						<select name="schedule_id" id="schedule_id" class="form-control">
							<option value="">General Payment</option>
						</select>
						<small class="form-text text-muted">Select specific installment month or leave blank for general payment</small>
					</div>
					<div class="form-group">
						<label for="amount" class="control-label">Amount <span class="text-danger">*</span></label>
						<input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
					</div>
					<div class="form-group">
						<label for="payment_method" class="control-label">Payment Method <span class="text-danger">*</span></label>
						<select name="payment_method" id="payment_method" class="form-control" required>
							<option value="cash">Cash</option>
							<!-- <option value="card">Card</option>
							<option value="bank_transfer">Bank Transfer</option>
							<option value="check">Check</option> -->
						</select>
					</div>
					<div class="form-group">
						<label for="receipt_number" class="control-label">Receipt Number (Optional)</label>
						<input type="text" name="receipt_number" id="receipt_number" class="form-control" placeholder="Enter receipt number">
					</div>
					<div class="form-group">
						<label for="notes" class="control-label">Notes (Optional)</label>
						<textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Additional notes about this payment"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="submit_payment">Submit Payment</button>
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
.table td, .table th {
    padding: 8px 12px;
    vertical-align: middle;
}

/* Fix modal scrolling */
.modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

/* Improve table readability */
.table th {
    position: sticky;
    top: 0;
    background: #f4f6f9;
    z-index: 10;
}

/* Badge improvements */
.badge {
    font-size: 0.75em;
    padding: 4px 8px;
}

/* Text alignment */
.text-right {
    text-align: right !important;
}

.text-center {
    text-align: center !important;
}

/* Status colors */
.text-success { color: #28a745 !important; }
.text-danger { color: #dc3545 !important; }
.text-warning { color: #ffc107 !important; }
.text-info { color: #17a2b8 !important; }
</style>

<script>
$(document).ready(function(){
	// Initialize DataTable with better configuration
	$('.table').dataTable({
		"scrollX": true,
		"scrollY": "400px",
		"scrollCollapse": true,
		"pageLength": 25,
		"order": [[1, "asc"]],
		"language": {
			"emptyTable": "No customer accounts found",
			"info": "Showing _START_ to _END_ of _TOTAL_ accounts",
			"infoEmpty": "Showing 0 to 0 of 0 accounts",
			"infoFiltered": "(filtered from _MAX_ total accounts)",
			"lengthMenu": "Show _MENU_ accounts",
			"search": "Search:",
			"zeroRecords": "No matching accounts found"
		}
	});
	
	$(document).on('click', '.view_account', function(){
		var id = $(this).attr('data-id');
		uni_modal("Account Details", "customer_account_balances/view_account.php?id="+id, "large");
	});
	
	$(document).on('click', '.add_payment', function(){
		var account_id = $(this).attr('data-id');
		$('#payment_account_id').val(account_id);
		
		// Load payment schedule options
		$.ajax({
			url: 'customer_account_balances/load_schedule.php',
			method: 'POST',
			data: {account_id: account_id},
			dataType: 'json',
			success: function(response){
				if(response.status == 'success'){
					var options = '<option value="">General Payment</option>';
					$.each(response.schedule, function(i, item){
						var dueDate = new Date(item.due_date).toLocaleDateString();
						var status = item.payment_status;
						var amount = parseFloat(item.remaining_balance).toFixed(2);
						options += '<option value="'+item.id+'">Month '+item.installment_number+' - Due: '+dueDate+' - ₱'+amount+' ('+status+')</option>';
					});
					$('#schedule_id').html(options);
				}
			},
			error: function(){
				// Still show modal even if schedule load fails
				$('#payment_modal').modal('show');
			}
		});
		
		$('#payment_modal').modal('show');
	});
	
	$('#submit_payment').click(function(){
		var form_data = $('#payment_form').serialize();
		start_loader();
		$.ajax({
			url: 'customer_account_balances/add_payment.php',
			method: 'POST',
			data: form_data,
			dataType: 'json',
			success: function(response){
				if(response.status == 'success'){
					alert_toast(response.msg, 'success');
					$('#payment_modal').modal('hide');
					setTimeout(function(){
						location.reload();
					}, 1500);
				} else {
					alert_toast(response.msg || 'Failed to record payment', 'error');
				}
				end_loader();
			},
			error: function(){
				alert_toast('An error occurred', 'error');
				end_loader();
			}
		});
	});
	
	// OR/CR actions (aligned with customer_accounts)
	$(document).on('click', '.upload_orcr', function(e){
		e.preventDefault();
		var clientId = $(this).data('id') || '';
		var name = $(this).data('name') || '';
		if(!clientId){ alert_toast('Customer not found','error'); return; }
		$('#upload_client_id').val(clientId);
		$('#upload_customer_name').val(name);
		$('#uploadOrcrModal').modal('show');
	});

	$(document).on('click', '.view_orcr', function(e){
		e.preventDefault();
		var clientId = $(this).data('id') || '';
		if(!clientId){ alert_toast('Customer not found','error'); return; }
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=get_client_orcr",
			method: "POST",
			data: {client_id: clientId},
			dataType: "json",
			success: function(resp){
				if(resp.status == 'success'){
					$('#orcr_documents').html(resp.html);
					$('#viewOrcrModal').modal('show');
				} else {
					alert_toast(resp.msg || 'Failed to load documents','error');
				}
			}
		});
	});

	// Upload OR/CR form submission
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
					setTimeout(function(){
						location.reload();
					}, 1500);
				} else {
					alert_toast(resp.msg || 'Failed to upload documents', 'error');
				}
				end_loader();
			},
			error: function(){
				alert_toast('An error occurred', 'error');
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
	
	$('#print_reports').click(function(){
		var nw = window.open("customer_account_balances/print_accounts.php","_blank","width=1200,height=800,scrollbars=yes");
	});

	// Ensure dropdowns inside table remain clickable with DataTables
	$(document).on('click', '.table .btn-group [data-toggle="dropdown"]', function(e){
		e.stopPropagation();
	});

	// Copy of working pattern used in other sections: reparent dropdown to body to avoid clipping
	$(document).on('show.bs.dropdown', '.table .btn-group', function () {
		var $btn = $(this).find('[data-toggle="dropdown"]');
		var $menu = $(this).find('> .dropdown-menu');
		if($menu.length === 0) return;
		$('body').append($menu.detach());
		var offset = $btn.offset();
		$menu.css({
			display: 'block',
			position: 'absolute',
			left: offset.left + 'px',
			top: (offset.top + $btn.outerHeight()) + 'px',
			'z-index': 2000
		});
	});
	$(document).on('hide.bs.dropdown', '.table .btn-group', function () {
		var $container = $(this);
		var $menu = $('body > .dropdown-menu:visible');
		if($menu.length){
			$container.append($menu.detach());
			$menu.removeAttr('style');
		}
	});

	// When a modal is hidden, if others remain open, keep body from jumping
	$(document).on('hidden.bs.modal', '.modal', function () {
		if ($('.modal:visible').length > 0) {
			$('body').addClass('modal-open');
		}
	});
});
</script>

