<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Installment Payments</h3>
		<div class="card-tools">
			<button class="btn btn-primary btn-sm" type="button" id="create_contract_btn">
				<span class="fa fa-plus"></span> Create Installment Contract
			</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- Statistics Cards -->
			<div class="row mb-3">
				<div class="col-md-3">
					<div class="info-box bg-primary">
						<span class="info-box-icon"><i class="fas fa-file-contract"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Active Contracts</span>
							<span class="info-box-number" id="active_contracts">0</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-success">
						<span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Completed</span>
							<span class="info-box-number" id="completed_contracts">0</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-warning">
						<span class="info-box-icon"><i class="fas fa-exclamation-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Overdue</span>
							<span class="info-box-number" id="overdue_count">0</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-info">
						<span class="info-box-icon"><i class="fas fa-peso-sign"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Balance</span>
							<span class="info-box-number" id="total_balance">₱0.00</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Contracts Table -->
			<div class="table-responsive">
				<table class="table table-bordered table-stripped" id="contracts_table">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="15%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="15%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Contract No.</th>
							<th>Customer</th>
							<th>Invoice</th>
							<th>Total Amount</th>
							<th>Balance</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<!-- Data will be loaded via AJAX -->
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Create Contract Modal -->
<div class="modal fade" id="createContractModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create Installment Contract</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="contract_form">
					<div class="form-group">
						<label>Invoice</label>
						<select class="form-control" name="invoice_id" id="contract_invoice_id" required>
							<option value="">Select Invoice</option>
						</select>
					</div>
					<div class="form-group">
						<label>Installment Plan</label>
						<select class="form-control" name="plan_id" id="contract_plan_id" required>
							<option value="">Select Plan</option>
						</select>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
				<button type="submit" form="contract_form" class="btn btn-success btn-sm">Create Contract</button>
			</div>
		</div>
	</div>
</div>

<!-- View Contract Modal -->
<div class="modal fade" id="viewContractModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Contract Details</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="contract_details">
				<!-- Contract details will be loaded here -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary btn-sm" id="print_schedule">Print Schedule</button>
			</div>
		</div>
	</div>
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="processPaymentModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Process Payment</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="payment_form">
					<input type="hidden" name="schedule_id" id="payment_schedule_id">
					<div class="form-group">
						<label>Installment</label>
						<input type="text" class="form-control" id="installment_info" readonly>
					</div>
					<div class="form-group">
						<label>Amount Due</label>
						<input type="text" class="form-control" id="amount_due" readonly>
					</div>
					<div class="form-group">
						<label>Amount Paid</label>
						<input type="number" class="form-control" name="amount_paid" id="payment_amount_paid" step="0.01" required>
					</div>
					<div class="form-group">
						<label>Payment Method</label>
						<select class="form-control" name="payment_method" id="payment_method" required>
							<option value="cash">Cash</option>
							<option value="card">Card</option>
							<option value="bank_transfer">Bank Transfer</option>
							<option value="check">Check</option>
						</select>
					</div>
					<div class="form-group">
						<label>Notes (Optional)</label>
						<textarea class="form-control" name="notes" id="payment_notes" rows="2"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
				<button type="submit" form="payment_form" class="btn btn-success btn-sm">Process Payment</button>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// Load initial data
	loadContracts();
	loadStats();
	loadPendingInvoices();
	loadInstallmentPlans();

	// Create contract button
	$('#create_contract_btn').click(function(){
		$('#createContractModal').modal('show');
	});

	// Load pending invoices
	function loadPendingInvoices(){
		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=get_all_invoices',
			method: 'GET',
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					var html = '<option value="">Select Invoice</option>';
					$.each(resp.data, function(index, invoice){
						if(invoice.payment_status == 'pending' && invoice.payment_type == 'cash'){
							html += '<option value="' + invoice.id + '">' + invoice.invoice_number + 
							        ' - ' + invoice.firstname + ' ' + invoice.lastname + 
							        ' - ₱' + parseFloat(invoice.total_amount).toLocaleString() + '</option>';
						}
					});
					$('#contract_invoice_id').html(html);
				}
			}
		});
	}

	// Load installment plans
	function loadInstallmentPlans(){
		$.ajax({
			url: _base_url_ + 'classes/InstallmentManager.php?action=get_plans',
			method: 'GET',
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					var html = '<option value="">Select Plan</option>';
					$.each(resp.data, function(index, plan){
						html += '<option value="' + plan.id + '">' + plan.plan_name + 
						        ' (' + plan.number_of_installments + ' installments';
						if(plan.interest_rate > 0){
							html += ' - ' + plan.interest_rate + '% interest';
						}
						html += ')</option>';
					});
					$('#contract_plan_id').html(html);
				}
			}
		});
	}

	// Contract form submission
	$('#contract_form').submit(function(e){
		e.preventDefault();
		$.ajax({
			url: _base_url_ + 'classes/InstallmentManager.php?action=create_contract',
			method: 'POST',
			data: $(this).serialize(),
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					alert_toast('Contract created successfully!', 'success');
					$('#createContractModal').modal('hide');
					loadContracts();
					loadStats();
				} else {
					alert_toast(resp.msg || 'Failed to create contract', 'error');
				}
			}
		});
	});

	// View contract
	$(document).on('click', '.view_contract', function(){
		var contract_id = $(this).data('id');
		viewContract(contract_id);
	});

	// Process payment
	$(document).on('click', '.process_payment', function(){
		var schedule_id = $(this).data('id');
		var installment = $(this).data('installment');
		var amount = $(this).data('amount');
		$('#payment_schedule_id').val(schedule_id);
		$('#installment_info').val('Installment #' + installment);
		$('#amount_due').val('₱' + parseFloat(amount).toLocaleString());
		$('#payment_amount_paid').val(amount);
		$('#processPaymentModal').modal('show');
	});

	// Payment form submission
	$('#payment_form').submit(function(e){
		e.preventDefault();
		var formData = $(this).serialize();
		formData += '&created_by=<?= $_settings->userdata("id") ?>';
		
		$.ajax({
			url: _base_url_ + 'classes/InstallmentPaymentProcessor.php?action=process_payment',
			method: 'POST',
			data: formData,
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					alert_toast('Payment processed successfully!', 'success');
					$('#processPaymentModal').modal('hide');
					loadContracts();
					loadStats();
				} else {
					alert_toast(resp.msg || 'Failed to process payment', 'error');
				}
			}
		});
	});

	// Print schedule
	$('#print_schedule').click(function(){
		var contract_id = $('#viewContractModal').data('contract-id');
		printSchedule(contract_id);
	});

	function loadContracts(){
		$.ajax({
			url: _base_url_ + 'classes/Master.php?f=get_all_installment_contracts',
			method: 'GET',
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					var html = '';
					$.each(resp.data, function(index, contract){
						var status_class = '';
						var status_text = '';
						switch(contract.status){
							case 'completed':
								status_class = 'badge badge-success';
								status_text = 'Completed';
								break;
							case 'defaulted':
								status_class = 'badge badge-danger';
								status_text = 'Defaulted';
								break;
							case 'cancelled':
								status_class = 'badge badge-secondary';
								status_text = 'Cancelled';
								break;
							default:
								status_class = 'badge badge-primary';
								status_text = 'Active';
								break;
						}

						html += '<tr>';
						html += '<td>' + (index + 1) + '</td>';
						html += '<td><strong>' + contract.contract_number + '</strong></td>';
						html += '<td>' + contract.firstname + ' ' + contract.lastname + '<br><small>' + contract.email + '</small></td>';
						html += '<td>' + contract.invoice_number + '</td>';
						html += '<td class="text-right">₱' + parseFloat(contract.total_amount).toLocaleString() + '</td>';
						html += '<td class="text-right">₱' + parseFloat(contract.remaining_balance).toLocaleString() + '</td>';
						html += '<td><span class="' + status_class + '">' + status_text + '</span></td>';
						html += '<td>';
						html += '<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">Action</button>';
						html += '<div class="dropdown-menu" role="menu">';
						html += '<a class="dropdown-item view_contract" href="#" data-id="' + contract.id + '"><span class="fa fa-eye text-primary"></span> View Contract</a>';
						if(contract.status == 'active'){
							html += '<div class="dropdown-divider"></div>';
							html += '<a class="dropdown-item process_payment" href="#" data-id="' + contract.id + '"><span class="fa fa-money text-success"></span> Process Payment</a>';
						}
						html += '</div>';
						html += '</td>';
						html += '</tr>';
					});
					$('#contracts_table tbody').html(html);
					
					if($.fn.DataTable){
						if($.fn.DataTable.isDataTable('#contracts_table')){
							$('#contracts_table').DataTable().clear().destroy();
						}
						$('#contracts_table').DataTable({
							responsive: true,
							pageLength: 25,
							columnDefs: [
								{ orderable: false, targets: -1 },
								{ className: 'text-right', targets: [4, 5] },
							],
						});
					}
				}
			}
		});
	}

	function loadStats(){
		$.ajax({
			url: _base_url_ + 'classes/Master.php?f=get_installment_stats',
			method: 'GET',
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					$('#active_contracts').text(resp.data.active || 0);
					$('#completed_contracts').text(resp.data.completed || 0);
					$('#overdue_count').text(resp.data.overdue || 0);
					$('#total_balance').text('₱' + parseFloat(resp.data.total_balance || 0).toLocaleString());
				}
			}
		});
	}

	function viewContract(contract_id){
		$.ajax({
			url: _base_url_ + 'classes/InstallmentManager.php?action=get_contract&contract_id=' + contract_id,
			method: 'GET',
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					displayContractDetails(resp.data);
					$('#viewContractModal').data('contract-id', contract_id);
					$('#viewContractModal').modal('show');
				}
			}
		});
	}

	function displayContractDetails(contract){
		var html = '<div class="contract-details-preview">';
		html += '<h5>Contract Information</h5>';
		html += '<table class="table table-bordered">';
		html += '<tr><th width="30%">Contract Number:</th><td>' + contract.contract_number + '</td></tr>';
		html += '<tr><th>Customer:</th><td>' + contract.firstname + ' ' + contract.lastname + '</td></tr>';
		html += '<tr><th>Invoice:</th><td>' + contract.invoice_number + '</td></tr>';
		html += '<tr><th>Plan:</th><td>' + contract.plan_name + '</td></tr>';
		html += '<tr><th>Total Amount:</th><td>₱' + parseFloat(contract.total_amount).toLocaleString() + '</td></tr>';
		html += '<tr><th>Remaining Balance:</th><td>₱' + parseFloat(contract.remaining_balance).toLocaleString() + '</td></tr>';
		html += '<tr><th>Status:</th><td>' + contract.status.toUpperCase() + '</td></tr>';
		html += '</table>';

		html += '<h5>Payment Schedule</h5>';
		html += '<div class="table-responsive">';
		html += '<table class="table table-bordered">';
		html += '<thead><tr>';
		html += '<th>#</th><th>Due Date</th><th>Amount Due</th><th>Status</th><th>Action</th>';
		html += '</tr></thead><tbody>';

		if(contract.schedule && contract.schedule.length > 0){
			$.each(contract.schedule, function(index, item){
				var status_class = '';
				var status_text = '';
				switch(item.status){
					case 'paid':
						status_class = 'badge badge-success';
						status_text = 'Paid';
						break;
					case 'overdue':
						status_class = 'badge badge-danger';
						status_text = 'Overdue';
						break;
					case 'partial':
						status_class = 'badge badge-info';
						status_text = 'Partial';
						break;
					default:
						status_class = 'badge badge-warning';
						status_text = 'Pending';
						break;
				}

				html += '<tr>';
				html += '<td>' + item.installment_number + '</td>';
				html += '<td>' + new Date(item.due_date).toLocaleDateString() + '</td>';
				html += '<td>₱' + parseFloat(item.amount_due).toLocaleString() + '</td>';
				html += '<td><span class="' + status_class + '">' + status_text + '</span></td>';
				html += '<td>';
				if(item.status != 'paid'){
					html += '<button class="btn btn-sm btn-success process_payment" data-id="' + item.id + '" data-installment="' + item.installment_number + '" data-amount="' + item.amount_due + '">Pay</button>';
				}
				html += '</td>';
				html += '</tr>';
			});
		}

		html += '</tbody></table></div>';
		html += '</div>';
		$('#contract_details').html(html);
	}

	function printSchedule(contract_id){
		window.open(_base_url_ + 'classes/InstallmentReceiptGenerator.php?action=generate_schedule&contract_id=' + contract_id);
	}
});
</script>

