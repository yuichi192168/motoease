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
                        <col width="12%">
                        <col width="18%">
					</colgroup>
					<thead>
                        <tr>
							<th>#</th>
							<th>Customer</th>
							<th>Installment Plan</th>
							<th>Paid Amount</th>
							<th>Unpaid Amount</th>
                            <th>Status</th>
                            <th>Credit App</th>
                            <th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						try {
							$i = 1;
                            // Updated query to align with installment system
                            $qry = $conn->query("SELECT 
                                                c.*,
                                                COALESCE(SUM(ic.total_amount), 0) as total_contract_amount,
                                                COALESCE(SUM(ic.down_payment_amount + IFNULL(ip.paid_amount, 0)), 0) as paid_amount,
                                                COALESCE(SUM(ic.remaining_balance), 0) as unpaid_amount,
                                                COUNT(DISTINCT ic.id) as active_contracts
                                                FROM `client_list` c 
                                                LEFT JOIN installment_contracts ic ON c.id = ic.customer_id AND ic.status = 'active'
                                                LEFT JOIN (SELECT contract_id, SUM(amount_paid) as paid_amount FROM installment_payments GROUP BY contract_id) ip ON ic.id = ip.contract_id
                                                WHERE c.delete_flag = 0 
                                                GROUP BY c.id 
                                                ORDER BY c.lastname, c.firstname");
							while($row = $qry->fetch_assoc()):
                                // Calculate total balance
                                $total_balance = (float)$row['total_contract_amount'];
                                
                                // Determine installment plan based on active contracts
                                if($row['active_contracts'] > 0) {
                                    // Get contract details for plan display
                                    $contract_q = $conn->query("SELECT ic.*, ip.plan_name, ip.number_of_installments 
                                                                FROM installment_contracts ic 
                                                                LEFT JOIN installment_plans ip ON ic.installment_plan_id = ip.id 
                                                                WHERE ic.customer_id = '{$row['id']}' AND ic.status = 'active' 
                                                                ORDER BY ic.created_at DESC LIMIT 1");
                                    if($contract_q && $contract_q->num_rows > 0) {
                                        $contract_info = $contract_q->fetch_assoc();
                                        $monthly_payment = $contract_info['remaining_balance'] > 0 ? ($contract_info['remaining_balance'] / max($contract_info['number_of_installments'], 1)) : 0;
                                        $installment_plan = $contract_info['plan_name'] ?: "₱" . number_format($monthly_payment, 2) . "/month";
                                    } else {
                                        $installment_plan = "Active Contract(s)";
                                    }
                                } else if($total_balance > 0) {
                                    $installment_plan = "₱" . number_format($total_balance / 6, 2) . "/month for 6 months";
                                } else {
                                    $installment_plan = "No balance";
                                }
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td>
									<strong><?php echo ucwords($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']) ?></strong><br>
									<small class="text-muted"><?php echo $row['email'] ?></small><br>
									<small class="text-info">ID: <?php echo $row['id'] ?></small>
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
                            <?php 
                                // Updated status calculation based on installment system
                                $client_id = (int)$row['id'];
                                $status_badge = '';
                                $late_fee_rate = 0.03; // 3% per month
                                
                                // Get installment contract status
                                $contract_q = $conn->query("SELECT 
                                    ic.id,
                                    ic.contract_number,
                                    ic.status as contract_status,
                                    ic.remaining_balance,
                                    COUNT(DISTINCT isch.id) as total_installments,
                                    SUM(CASE WHEN isch.status = 'paid' THEN 1 ELSE 0 END) as paid_installments,
                                    SUM(CASE WHEN isch.status = 'overdue' THEN 1 ELSE 0 END) as overdue_installments,
                                    MIN(CASE WHEN isch.status IN ('pending', 'overdue') THEN isch.due_date END) as next_due_date,
                                    MAX(CASE WHEN isch.status = 'overdue' THEN DATEDIFF(CURDATE(), isch.due_date) END) as max_days_overdue,
                                    SUM(CASE WHEN isch.status = 'overdue' THEN isch.amount_due * {$late_fee_rate} * FLOOR(DATEDIFF(CURDATE(), isch.due_date)/30) ELSE 0 END) as penalty_total
                                    FROM installment_contracts ic
                                    LEFT JOIN installment_schedule isch ON ic.id = isch.contract_id
                                    WHERE ic.customer_id = '{$client_id}' AND ic.status = 'active'
                                    GROUP BY ic.id
                                    ORDER BY ic.created_at DESC
                                    LIMIT 1");
                                
                                if($contract_q && $contract_q->num_rows > 0) {
                                    $contract = $contract_q->fetch_assoc();
                                    
                                    if($contract['contract_status'] == 'completed' || ($contract['remaining_balance'] <= 0 && $row['unpaid_amount'] <= 0)) {
                                        $status_badge = '<span class="badge badge-success">🟢 Fully Paid</span>';
                                    } elseif($contract['overdue_installments'] > 0 && $contract['max_days_overdue'] > 0) {
                                        $status_badge = '<span class="badge badge-danger">🔴 Late: '.(int)$contract['max_days_overdue'].'d</span> <small class="text-danger">+'.(int)($late_fee_rate*100).'%/mo ₱'.number_format($contract['penalty_total'] ?: 0,2).'</small>';
                                    } elseif($contract['remaining_balance'] > 0 || $row['unpaid_amount'] > 0) {
                                        $next_due = $contract['next_due_date'] ? date('M d, Y', strtotime($contract['next_due_date'])) : '—';
                                        $status_badge = '<span class="badge badge-warning">🟡 Pending / Due '.$next_due.'</span>';
                                    } else {
                                        $status_badge = '<span class="badge badge-success">🟢 Paid</span>';
                                    }
                                } else {
                                    // No active contracts - check if has balance
                                    if($row['unpaid_amount'] <= 0 && $row['paid_amount'] > 0) {
                                        $status_badge = '<span class="badge badge-success">🟢 Fully Paid</span>';
                                    } elseif($row['unpaid_amount'] > 0) {
                                        $status_badge = '<span class="badge badge-warning">🟡 Has Balance</span>';
                                    } else {
                                        $status_badge = '<span class="badge badge-secondary">⚪ No Activity</span>';
                                    }
                                }
                            ?>
                            <td class="text-center">
                                <?php echo $status_badge; ?>
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
										<a class="dropdown-item view_transactions" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['lastname'] . ', ' . $row['firstname'] ?>">
											<span class="fa fa-list text-info"></span> View Transactions
										</a>
										<?php if($_settings->userdata('login_type') == 1): // Admin only ?>
										<a class="dropdown-item adjust_balance" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['lastname'] . ', ' . $row['firstname'] ?>">
											<span class="fa fa-edit text-primary"></span> Adjust Balance
										</a>
										<?php endif; ?>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item upload_orcr" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['lastname'] . ', ' . $row['firstname'] ?>">
											<span class="fa fa-upload text-success"></span> Upload OR/CR
										</a>
										<a class="dropdown-item view_orcr" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['lastname'] . ', ' . $row['firstname'] ?>">
											<span class="fa fa-file-pdf text-warning"></span> View OR/CR
										</a>
									</div>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php if($qry->num_rows <= 0): ?>
						<tr>
							<td colspan="8" class="text-center">No customer accounts found.</td>
						</tr>
						<?php endif; ?>
						<?php } catch (Exception $e) { ?>
						<tr>
							<td colspan="8" class="text-center text-danger">Error loading customer accounts: <?php echo $e->getMessage(); ?></td>
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
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Customer Transactions & Installments</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
            <div class="modal-body">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Customer:</strong> <span id="vt_customer_name">-</span>
                    </div>
                    <div class="btn-group" role="group" aria-label="Actions">
                        <?php if($_settings->userdata('login_type') == 1): // Admin only ?>
                        <button type="button" class="btn btn-sm btn-primary" id="vt_adjust_balance">
                            <span class="fa fa-edit"></span> Adjust Balance
                        </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm btn-success" id="vt_upload_orcr">
                            <span class="fa fa-upload"></span> Upload OR/CR
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" id="vt_view_orcr">
                            <span class="fa fa-file-pdf"></span> View OR/CR
                        </button>
                    </div>
                </div>
                
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs" id="transactionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="contracts-tab" data-toggle="tab" href="#contracts" role="tab" aria-controls="contracts" aria-selected="true">
                            <i class="fa fa-file-contract"></i> Installment Contracts
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="payments-tab" data-toggle="tab" href="#payments" role="tab" aria-controls="payments" aria-selected="false">
                            <i class="fa fa-money-bill-wave"></i> Payment History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="schedule-tab" data-toggle="tab" href="#schedule" role="tab" aria-controls="schedule" aria-selected="false">
                            <i class="fa fa-calendar-alt"></i> Payment Schedule
                        </a>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content mt-3" id="transactionTabsContent">
                    <!-- Contracts Tab -->
                    <div class="tab-pane fade show active" id="contracts" role="tabpanel" aria-labelledby="contracts-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Contract #</th>
                                        <th>Invoice #</th>
                                        <th>Plan</th>
                                        <th class="text-right">Total Amount</th>
                                        <th class="text-right">Paid</th>
                                        <th class="text-right">Remaining</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody id="contracts_list">
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">
                                            <i class="fa fa-spinner fa-spin"></i> Loading contracts...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Payments Tab -->
                    <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Contract #</th>
                                        <th>Installment #</th>
                                        <th class="text-right">Amount</th>
                                        <th>Method</th>
                                        <th>Receipt #</th>
                                        <th>Processed By</th>
                                    </tr>
                                </thead>
                                <tbody id="payments_list">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fa fa-spinner fa-spin"></i> Loading payment history...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Schedule Tab -->
                    <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Contract #</th>
                                        <th>Installment #</th>
                                        <th>Due Date</th>
                                        <th class="text-right">Amount Due</th>
                                        <th class="text-right">Paid</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="schedule_list">
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fa fa-spinner fa-spin"></i> Loading payment schedule...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
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

/* Tab styling */
.nav-tabs .nav-link {
    color: #495057;
}

.nav-tabs .nav-link.active {
    color: #007bff;
    font-weight: 600;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #007bff;
}

.tab-content {
    min-height: 300px;
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
				"info": "Showing _START_ to _END_ of _TOTAL_ customers",
				"infoEmpty": "Showing 0 to 0 of 0 customers",
				"infoFiltered": "(filtered from _MAX_ total customers)",
				"lengthMenu": "Show _MENU_ customers",
				"search": "Search:",
				"zeroRecords": "No matching customers found"
			}
		});
		
		$('.adjust_balance').click(function(){
			var id = $(this).attr('data-id');
			var name = $(this).attr('data-name');
			
			// Get client current balance
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=get_client_balance",
				method: "POST",
				data: {client_id: id},
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#adjust_client_id').val(id);
						$('#adjust_customer_name').val(name);
						$('#current_balance').val('₱' + parseFloat(resp.current_balance || resp.balance || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
						$('#adjustBalanceModal').modal('show');
					} else {
						alert_toast(resp.msg, 'error');
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
            var name = $(this).attr('data-name') || '';
            
            // Store context for modal action buttons
            $('#viewTransactionsModal').data('client-id', id);
            $('#viewTransactionsModal').data('client-name', name);
            $('#vt_customer_name').text(name || '-');
            
            // Reset tabs to first tab
            $('#contracts-tab').tab('show');
            
            $.ajax({
				url: _base_url_ + "classes/Master.php?f=get_client_transactions",
				method: "POST",
				data: {client_id: id},
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						// Populate Contracts
						var contractsHtml = '';
						if(resp.contracts && resp.contracts.length > 0){
							$.each(resp.contracts, function(index, contract){
								var statusClass = contract.status == 'completed' ? 'success' : 
								                  (contract.status == 'active' ? 'primary' : 
								                  (contract.status == 'defaulted' ? 'danger' : 'secondary'));
								contractsHtml += '<tr>';
								contractsHtml += '<td>' + (contract.contract_number || '-') + '</td>';
								contractsHtml += '<td>' + (contract.invoice_number || '-') + '</td>';
								contractsHtml += '<td>' + (contract.plan_name || '-') + '</td>';
								contractsHtml += '<td class="text-right">₱' + parseFloat(contract.total_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
								contractsHtml += '<td class="text-right text-success">₱' + parseFloat(contract.paid_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
								contractsHtml += '<td class="text-right text-danger">₱' + parseFloat(contract.remaining_balance || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
								contractsHtml += '<td><span class="badge badge-' + statusClass + '">' + (contract.status ? contract.status.charAt(0).toUpperCase() + contract.status.slice(1) : '-') + '</span></td>';
								contractsHtml += '<td>' + (contract.created_at ? new Date(contract.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : '-') + '</td>';
								contractsHtml += '</tr>';
							});
						} else {
							contractsHtml = '<tr><td colspan="8" class="text-center text-muted">No installment contracts found.</td></tr>';
						}
						$('#contracts_list').html(contractsHtml);
						
						// Populate Payments
						var paymentsHtml = '';
						if(resp.payments && resp.payments.length > 0){
							$.each(resp.payments, function(index, payment){
								paymentsHtml += '<tr>';
								paymentsHtml += '<td>' + (payment.payment_date ? new Date(payment.payment_date).toLocaleString('en-US', {year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'}) : '-') + '</td>';
								paymentsHtml += '<td>' + (payment.contract_number || '-') + '</td>';
								paymentsHtml += '<td>#' + (payment.installment_number || '-') + '</td>';
								paymentsHtml += '<td class="text-right text-success">₱' + parseFloat(payment.amount_paid || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
								paymentsHtml += '<td>' + (payment.payment_method ? payment.payment_method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : '-') + '</td>';
								paymentsHtml += '<td>' + (payment.receipt_number || '-') + '</td>';
								var staffName = payment.staff_firstname || payment.staff_name || '';
								if(payment.staff_lastname) staffName += ' ' + payment.staff_lastname;
								paymentsHtml += '<td>' + (staffName || 'System') + '</td>';
								paymentsHtml += '</tr>';
							});
						} else {
							paymentsHtml = '<tr><td colspan="7" class="text-center text-muted">No payment history found.</td></tr>';
						}
						$('#payments_list').html(paymentsHtml);
						
						// Populate Schedule
						var scheduleHtml = '';
						if(resp.schedule && resp.schedule.length > 0){
							$.each(resp.schedule, function(index, schedule){
								var statusClass = schedule.status == 'paid' ? 'success' : 
								                  (schedule.status == 'overdue' ? 'danger' : 
								                  (schedule.status == 'partial' ? 'warning' : 'secondary'));
								var isOverdue = schedule.status == 'overdue' || (schedule.status == 'pending' && new Date(schedule.due_date) < new Date());
								var rowClass = isOverdue ? 'table-danger' : '';
								scheduleHtml += '<tr class="' + rowClass + '">';
								scheduleHtml += '<td>' + (schedule.contract_number || '-') + '</td>';
								scheduleHtml += '<td>#' + (schedule.installment_number || '-') + '</td>';
								scheduleHtml += '<td>' + (schedule.due_date ? new Date(schedule.due_date).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : '-') + '</td>';
								scheduleHtml += '<td class="text-right">₱' + parseFloat(schedule.amount_due || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
								scheduleHtml += '<td class="text-right">₱' + parseFloat(schedule.paid_amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
								scheduleHtml += '<td><span class="badge badge-' + statusClass + '">' + (schedule.status ? schedule.status.charAt(0).toUpperCase() + schedule.status.slice(1) : '-') + '</span></td>';
								scheduleHtml += '</tr>';
							});
						} else {
							scheduleHtml = '<tr><td colspan="6" class="text-center text-muted">No payment schedule found.</td></tr>';
						}
						$('#schedule_list').html(scheduleHtml);
						
						$('#viewTransactionsModal').modal('show');
					} else {
						alert_toast(resp.msg || 'Failed to load transactions', 'error');
					}
				},
				error: function(){
					$('#contracts_list').html('<tr><td colspan="8" class="text-center text-danger">Error loading data.</td></tr>');
					$('#payments_list').html('<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>');
					$('#schedule_list').html('<tr><td colspan="6" class="text-center text-danger">Error loading data.</td></tr>');
					alert_toast('Failed to load transactions', 'error');
				}
			});
		});

        // Actions inside View Transactions modal
        $('#vt_adjust_balance').click(function(){
            var id = $('#viewTransactionsModal').data('client-id') || '';
            var name = $('#viewTransactionsModal').data('client-name') || '';
            if(!id) return;
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=get_client_balance",
                method: "POST",
                data: {client_id: id},
                dataType: "json",
                success: function(resp){
					if(resp.status == 'success'){
						$('#adjust_client_id').val(id);
						$('#adjust_customer_name').val(name);
						$('#current_balance').val('₱' + parseFloat(resp.current_balance || resp.balance || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ","));
						$('#adjustBalanceModal').modal('show');
					}
                }
            });
        });

        $('#vt_upload_orcr').click(function(){
            var id = $('#viewTransactionsModal').data('client-id') || '';
            var name = $('#viewTransactionsModal').data('client-name') || '';
            if(!id) return;
            $('#upload_client_id').val(id);
            $('#upload_customer_name').val(name);
            $('#uploadOrcrModal').modal('show');
        });

        $('#vt_view_orcr').click(function(){
            var id = $('#viewTransactionsModal').data('client-id') || '';
            if(!id) return;
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
						alert_toast(resp.msg, 'success');
						setTimeout(function(){
							location.reload();
						}, 2000);
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
			var nw = window.open("print_customer_accounts.php","_blank","width=1200,height=800,scrollbars=yes");
		});

		// Handle multiple stacked modals so new ones sit above previous
		$(document).on('show.bs.modal', '.modal', function () {
			// Ensure modal is not trapped inside a lower z-index stacking context
			$(this).appendTo('body');
			// Compute z-index above any currently visible modals
			var zIndex = 1050 + (10 * $('.modal:visible').length);
			$(this).css('z-index', zIndex);
			setTimeout(function() {
				$('.modal-backdrop').not('.modal-stack')
					.css('z-index', zIndex - 1)
					.addClass('modal-stack');
			}, 0);
		});

		// When a modal is hidden, if others remain open, keep body from jumping
		$(document).on('hidden.bs.modal', '.modal', function () {
			if ($('.modal:visible').length > 0) {
				$('body').addClass('modal-open');
			}
		});

		$(document).on('click', '.mark-status', function(){
			var id = $(this).data('id');
			var status = $(this).data('status');
			if(!id || !status) return;
			$.ajax({
				url: _base_url_ + 'classes/Invoice.php?action=update_status',
				method: 'POST',
				data: { invoice_id: id, status: status },
				dataType: 'json',
				success: function(resp){
					if(resp && resp.status === 'success'){
						alert_toast('Status updated to '+status.toUpperCase(),'success');
						location.reload();
					}else{
						alert_toast(resp.msg || 'Failed to update status','error');
					}
				},
				error: function(){ alert_toast('Failed to update status','error'); }
			});
		});
	})
</script>
