<?php 
// Ensure config.php is loaded (needed when accessed directly via AJAX)
if(!defined('base_app')){
	require_once('../../config.php');
}

// Only need to load the class file
if(file_exists(base_app.'classes/CustomerAccountBalance.php')){
	require_once(base_app.'classes/CustomerAccountBalance.php');
}else{
	die('CustomerAccountBalance class file not found');
}

$_settings->userdata('id') < 1 ? die('Unauthorized Access') : '';

$account_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$accountBalance = new CustomerAccountBalance($conn);

// Check and apply late fees
$accountBalance->checkAndApplyLateFees($account_id);

$account = $accountBalance->getAccountInfo($account_id);
if(!$account){
	echo "Account not found";
	exit;
}

$schedule = $accountBalance->getPaymentSchedule($account_id);
$transactions = $accountBalance->getTransactionHistory($account_id);

// Get invoice information if invoice_id exists (linked invoice)
$invoice = null;
$receipts = [];
if($account['invoice_id']){
	$invoice_query = $conn->query("
		SELECT i.*, 
			   CONCAT(cl.firstname, ' ', cl.lastname) as customer_name,
			   cl.email, cl.contact, cl.address
		FROM invoices i
		LEFT JOIN client_list cl ON i.customer_id = cl.id
		WHERE i.id = '{$account['invoice_id']}'
	");
	if($invoice_query && $invoice_query->num_rows > 0){
		$invoice = $invoice_query->fetch_assoc();
		
		// Get receipts for this invoice
		$receipts_query = $conn->query("
			SELECT r.*, 
				   CONCAT(u.firstname, ' ', u.lastname) as issued_by_name
			FROM receipts r
			LEFT JOIN users u ON r.received_by = u.id
			WHERE r.invoice_id = '{$account['invoice_id']}'
			ORDER BY r.issued_at DESC
		");
		if($receipts_query){
			while($receipt = $receipts_query->fetch_assoc()){
				$receipts[] = $receipt;
			}
		}
	}
}

// Get ALL invoices for this customer
$all_invoices = [];
$client_id = $account['client_id'];
$invoices_query = $conn->query("
	SELECT i.*, 
		   fin.total_paid, fin.balance_remaining, fin.computed_status,
		   COUNT(r.id) as receipt_count
	FROM invoices i
	LEFT JOIN invoice_financials fin ON fin.id = i.id
	LEFT JOIN receipts r ON r.invoice_id = i.id
	WHERE i.customer_id = '{$client_id}'
	GROUP BY i.id
	ORDER BY i.generated_at DESC
");
if($invoices_query){
	while($inv = $invoices_query->fetch_assoc()){
		$all_invoices[] = $inv;
	}
}

// Get pending orders for this customer
$pending_orders = [];
$orders_query = $conn->query("
	SELECT o.*, 
		   CASE 
			   WHEN o.status = 0 THEN 'Pending'
			   WHEN o.status = 1 THEN 'Processing'
			   WHEN o.status = 2 THEN 'Ready for Pickup'
			   WHEN o.status = 3 THEN 'Out for Delivery'
			   WHEN o.status = 4 THEN 'Delivered'
			   WHEN o.status = 5 THEN 'Cancelled'
			   WHEN o.status = 6 THEN 'Claimed'
			   ELSE 'Unknown'
		   END as status_text
	FROM order_list o
	WHERE o.client_id = '{$client_id}'
	AND o.status NOT IN (5, 6)
	ORDER BY o.date_created DESC
");
if($orders_query){
	while($order = $orders_query->fetch_assoc()){
		$pending_orders[] = $order;
	}
}
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<!-- Account Summary Card -->
			<div class="card card-primary">
				<div class="card-header">
					<h3 class="card-title">Account Summary</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<dl>
								<dt>Customer Name:</dt>
								<dd><?php echo ucwords($account['firstname'] . ' ' . $account['lastname']) ?></dd>
								<dt>Item Purchased:</dt>
								<dd><?php echo $account['item_purchased'] ?></dd>
								<dt>Order ID:</dt>
								<dd><?php echo $account['order_id'] ?></dd>
								<?php if($account['invoice_id']): ?>
								<dt>Invoice ID:</dt>
								<dd><?php echo $account['invoice_id'] ?></dd>
								<?php endif; ?>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt>Total Price:</dt>
								<dd><strong>₱<?php echo number_format($account['total_price'], 2) ?></strong></dd>
								<dt>Downpayment:</dt>
								<dd class="text-info">₱<?php echo number_format($account['downpayment_amount'], 2) ?></dd>
								<dt>Paid Amount:</dt>
								<dd class="text-success"><strong>₱<?php echo number_format($account['paid_amount'], 2) ?></strong></dd>
								<dt>Remaining Balance:</dt>
								<dd class="text-danger"><strong>₱<?php echo number_format($account['remaining_balance'], 2) ?></strong></dd>
								<dt>Status:</dt>
								<dd>
									<?php 
									$badge = 'secondary';
									switch($account['status']){
										case 'paid': $badge = 'success'; break;
										case 'active': $badge = 'primary'; break;
										case 'defaulted': $badge = 'danger'; break;
									}
									?>
									<span class="badge badge-<?php echo $badge ?>"><?php echo ucfirst($account['status']) ?></span>
								</dd>
							</dl>
						</div>
					</div>
					<?php if($account['installment_plan_months']): ?>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<strong>Installment Plan:</strong> <?php echo $account['installment_plan_months'] ?> months<br>
							<strong>Monthly Payment:</strong> ₱<?php echo number_format($account['monthly_payment_amount'], 2) ?>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			
			<!-- Payment Schedule Card -->
			<?php if(count($schedule) > 0): ?>
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Payment Schedule</h3>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Month</th>
									<th>Due Date</th>
									<th>Amount Due</th>
									<th>Paid Amount</th>
									<th>Late Fee</th>
									<th>Remaining Balance</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($schedule as $item): ?>
								<tr>
									<td><?php echo $item['installment_number'] ?></td>
									<td><?php echo date('M d, Y', strtotime($item['due_date'])) ?></td>
									<td class="text-right">₱<?php echo number_format($item['amount_due'], 2) ?></td>
									<td class="text-right text-success">₱<?php echo number_format($item['paid_amount'], 2) ?></td>
									<td class="text-right text-danger">
										<?php if($item['late_fee'] > 0): ?>
											₱<?php echo number_format($item['late_fee'], 2) ?>
										<?php else: ?>
											₱0.00
										<?php endif; ?>
									</td>
									<td class="text-right">₱<?php echo number_format($item['remaining_balance'], 2) ?></td>
									<td class="text-center">
										<?php 
										$status = $item['payment_status'];
										$badge = 'secondary';
										switch($status){
											case 'Paid': $badge = 'success'; break;
											case 'Late': $badge = 'danger'; break;
											case 'Partial': $badge = 'warning'; break;
											case 'Unpaid': $badge = 'info'; break;
										}
										?>
										<span class="badge badge-<?php echo $badge ?>"><?php echo $status ?></span>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- All Invoices & Orders Card -->
			<div class="card card-success">
				<div class="card-header">
					<h3 class="card-title">Invoices & Orders</h3>
				</div>
				<div class="card-body">
					<!-- Pending Orders Section -->
					<?php if(count($pending_orders) > 0): ?>
					<h5 class="mb-3">Pending Orders</h5>
					<div class="table-responsive mb-4">
						<table class="table table-bordered table-striped table-sm">
							<thead>
								<tr>
									<th>Order ID</th>
									<th>Date</th>
									<th>Total Amount</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($pending_orders as $order): ?>
								<tr>
									<td><strong>#<?php echo $order['id'] ?></strong></td>
									<td><?php echo date('M d, Y', strtotime($order['date_created'])) ?></td>
									<td class="text-right"><strong>₱<?php echo number_format($order['total_amount'], 2) ?></strong></td>
									<td><span class="badge badge-info"><?php echo $order['status_text'] ?></span></td>
									<td>
										<button type="button" class="btn btn-sm btn-info view_order_btn" data-id="<?php echo $order['id'] ?>">
											<i class="fas fa-eye"></i> View
										</button>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php endif; ?>
					
					<!-- All Invoices Section -->
					<h5 class="mb-3">All Invoices</h5>
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-sm">
							<thead>
								<tr>
									<th>Invoice #</th>
									<th>Date</th>
									<th>Total Amount</th>
									<th>Paid</th>
									<th>Balance</th>
									<th>Status</th>
									<th>Receipts</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php if(count($all_invoices) > 0): ?>
									<?php foreach($all_invoices as $inv): 
										$is_linked = ($inv['id'] == $account['invoice_id']);
										$payment_status = strtolower($inv['computed_status'] ?? $inv['payment_status'] ?? 'pending');
										$badge_class = 'secondary';
										switch($payment_status){
											case 'paid': $badge_class = 'success'; break;
											case 'partial': $badge_class = 'warning'; break;
											case 'unpaid':
											case 'pending': $badge_class = 'info'; break;
											case 'late': $badge_class = 'danger'; break;
										}
									?>
									<tr class="<?php echo $is_linked ? 'table-primary' : '' ?>">
										<td>
											<strong><?php echo $inv['invoice_number'] ?></strong>
											<?php if($is_linked): ?>
												<small class="badge badge-primary">Linked</small>
											<?php endif; ?>
										</td>
										<td><?php echo date('M d, Y', strtotime($inv['generated_at'])) ?></td>
										<td class="text-right"><strong>₱<?php echo number_format($inv['total_amount'], 2) ?></strong></td>
										<td class="text-right text-success">₱<?php echo number_format($inv['total_paid'] ?? 0, 2) ?></td>
										<td class="text-right text-danger">₱<?php echo number_format($inv['balance_remaining'] ?? $inv['total_amount'], 2) ?></td>
										<td>
											<span class="badge badge-<?php echo $badge_class ?>">
												<?php echo ucfirst($payment_status) ?>
											</span>
										</td>
										<td class="text-center">
											<span class="badge badge-secondary"><?php echo $inv['receipt_count'] ?></span>
										</td>
										<td class="text-center">
											<a href="<?php echo base_url ?>admin/invoices/print_invoice.php?id=<?php echo $inv['id'] ?>" target="_blank" class="btn btn-sm btn-primary">
												<i class="fas fa-print"></i> Print
											</a>
											<?php if($payment_status != 'paid'): ?>
											<button type="button" class="btn btn-sm btn-success create_receipt_btn" data-id="<?php echo $inv['id'] ?>" data-amount="<?php echo $inv['balance_remaining'] ?? $inv['total_amount'] ?>" style="margin-left: 5px;">
												<i class="fas fa-receipt"></i> Create Receipt
											</button>
											<?php endif; ?>
										</td>
									</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="8" class="text-center text-muted">No invoices found</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<!-- Linked Invoice Information Card (if exists) -->
			<?php if($invoice): ?>
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Linked Invoice Details</h3>
					<div class="card-tools">
						<a href="<?php echo base_url ?>admin/invoices/print_invoice.php?id=<?php echo $invoice['id'] ?>" target="_blank" class="btn btn-sm btn-primary">
							<i class="fas fa-print"></i> Print Invoice
						</a>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<dl>
								<dt>Invoice Number:</dt>
								<dd><strong><?php echo $invoice['invoice_number'] ?></strong></dd>
								<dt>Transaction Type:</dt>
								<dd><?php echo ucfirst($invoice['transaction_type'] ?? 'Order') ?></dd>
								<dt>Total Amount:</dt>
								<dd><strong>₱<?php echo number_format($invoice['total_amount'], 2) ?></strong></dd>
								<dt>Payment Status:</dt>
								<dd>
									<?php 
									$status = ucfirst($invoice['payment_status'] ?? 'pending');
									$badge = 'secondary';
									switch(strtolower($invoice['payment_status'] ?? 'pending')){
										case 'paid': $badge = 'success'; break;
										case 'partial': $badge = 'warning'; break;
										case 'unpaid': $badge = 'danger'; break;
										case 'pending': $badge = 'info'; break;
									}
									?>
									<span class="badge badge-<?php echo $badge ?>"><?php echo $status ?></span>
								</dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt>Generated Date:</dt>
								<dd><?php echo date('M d, Y h:i A', strtotime($invoice['generated_at'])) ?></dd>
								<?php if($invoice['due_date']): ?>
								<dt>Due Date:</dt>
								<dd><?php echo date('M d, Y', strtotime($invoice['due_date'])) ?></dd>
								<?php endif; ?>
								<?php if($invoice['paid_at']): ?>
								<dt>Paid Date:</dt>
								<dd class="text-success"><?php echo date('M d, Y h:i A', strtotime($invoice['paid_at'])) ?></dd>
								<?php endif; ?>
							</dl>
						</div>
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- Receipts Card -->
			<?php if(count($receipts) > 0): ?>
			<div class="card card-warning">
				<div class="card-header">
					<h3 class="card-title">Receipts</h3>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Receipt Number</th>
									<th>Amount Paid</th>
									<th>Payment Method</th>
									<th>Issued Date</th>
									<th>Issued By</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach($receipts as $receipt): ?>
								<tr>
									<td><strong><?php echo $receipt['receipt_number'] ?></strong></td>
									<td class="text-right text-success"><strong>₱<?php echo number_format($receipt['amount_paid'], 2) ?></strong></td>
									<td><?php echo ucfirst($receipt['payment_method'] ?? 'cash') ?></td>
									<td><?php echo date('M d, Y h:i A', strtotime($receipt['issued_at'])) ?></td>
									<td><?php echo $receipt['issued_by_name'] ?: 'System' ?></td>
									<td>
										<a href="<?php echo base_url ?>admin/invoices/print_receipt.php?id=<?php echo $receipt['id'] ?>" target="_blank" class="btn btn-sm btn-primary">
											<i class="fas fa-print"></i> Print
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php elseif($invoice): ?>
			<div class="card card-warning">
				<div class="card-header">
					<h3 class="card-title">Receipts</h3>
				</div>
				<div class="card-body">
					<div class="alert alert-info">
						<i class="fas fa-info-circle"></i> No receipts found for this invoice.
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- Transaction History Card -->
			<div class="card card-default">
				<div class="card-header">
					<h3 class="card-title">Transaction History</h3>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Date</th>
									<th>Type</th>
									<th>Amount</th>
									<th>Payment Method</th>
									<th>Receipt Number</th>
									<th>Processed By</th>
									<th>Notes</th>
								</tr>
							</thead>
							<tbody>
								<?php if(count($transactions) > 0): ?>
									<?php foreach($transactions as $transaction): ?>
									<tr>
										<td><?php echo date('M d, Y h:i A', strtotime($transaction['transaction_date'])) ?></td>
										<td>
											<?php 
											$type = str_replace('_', ' ', $transaction['transaction_type']);
											echo ucwords($type);
											?>
										</td>
										<td class="text-right">
											<?php 
											$color = 'text-success';
											if($transaction['transaction_type'] == 'late_fee') $color = 'text-danger';
											?>
											<span class="<?php echo $color ?>">
												₱<?php echo number_format($transaction['amount'], 2) ?>
											</span>
										</td>
										<td><?php echo ucfirst(str_replace('_', ' ', $transaction['payment_method'])) ?></td>
										<td><?php echo $transaction['receipt_number'] ?: '-' ?></td>
										<td>
											<?php 
											if($transaction['processor_firstname']){
												echo ucwords($transaction['processor_firstname'] . ' ' . $transaction['processor_lastname']);
											} else {
												echo 'System';
											}
											?>
										</td>
										<td><?php echo htmlspecialchars($transaction['notes'] ?: '-') ?></td>
									</tr>
									<?php endforeach; ?>
								<?php else: ?>
									<tr>
										<td colspan="7" class="text-center">No transactions yet</td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Create Receipt Modal -->
<div class="modal fade" id="createReceiptModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create Receipt</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="receipt_form">
					<input type="hidden" name="invoice_id" id="receipt_invoice_id">
					<div class="form-group">
						<label>Amount Paid <span class="text-danger">*</span></label>
						<input type="number" step="0.01" name="amount_paid" id="amount_paid" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Payment Method <span class="text-danger">*</span></label>
						<select name="payment_method" id="payment_method" class="form-control" required>
							<option value="cash">Cash</option>
							<option value="card">Card</option>
							<option value="bank_transfer">Bank Transfer</option>
							<option value="gcash">GCash</option>
						</select>
					</div>
					<div class="form-group">
						<label>Payment Reference (Optional)</label>
						<input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Transaction ID, Check No., etc.">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="submit_receipt">Create Receipt</button>
			</div>
		</div>
	</div>
</div>

<style>
.create_receipt_btn, .view_order_btn {
	cursor: pointer;
	position: relative;
	z-index: 1;
}
.create_receipt_btn:hover, .view_order_btn:hover {
	opacity: 0.9;
}
.table td {
	position: relative;
}
</style>

<script>
// Ensure script runs when modal content is loaded
$(document).ready(function(){
	// Create receipt button click - use delegated event handler
	$(document).on('click', '.create_receipt_btn', function(e){
		e.preventDefault();
		e.stopPropagation();
		var $btn = $(this);
		var invoice_id = $btn.data('id');
		var balance = parseFloat($btn.data('amount')) || 0;
		
		if(!invoice_id){
			alert_toast('Invalid invoice ID', 'error');
			return false;
		}
		
		$('#receipt_invoice_id').val(invoice_id);
		$('#amount_paid').val(balance.toFixed(2));
		$('#payment_reference').val('');
		$('#payment_method').val('cash');
		$('#createReceiptModal').modal('show');
		return false;
	});
	
	// View order button click
	$(document).on('click', '.view_order_btn', function(e){
		e.preventDefault();
		e.stopPropagation();
		var order_id = $(this).data('id');
		
		if(!order_id){
			alert_toast('Invalid order ID', 'error');
			return false;
		}
		
		uni_modal("Order Details", "orders/view_order.php?id="+order_id, "large");
		return false;
	});
	
	// Submit receipt form
	$('#submit_receipt').on('click', function(e){
		e.preventDefault();
		var amount_paid = parseFloat($('#amount_paid').val()) || 0;
		
		if(amount_paid <= 0){
			alert_toast('Please enter a valid amount', 'error');
			return false;
		}
		
		var form_data = {
			invoice_id: $('#receipt_invoice_id').val(),
			payment_data: {
				amount_paid: amount_paid,
				payment_method: $('#payment_method').val(),
				payment_reference: $('#payment_reference').val() || ''
			},
			staff_id: <?php echo $_settings->userdata('id') ?>
		};
		
		if(!form_data.invoice_id){
			alert_toast('Invalid invoice ID', 'error');
			return false;
		}
		
		start_loader();
		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=create_receipt',
			method: 'POST',
			data: form_data,
			dataType: 'json',
			success: function(response){
				end_loader();
				// Handle response - check if it's actually an error or success
				if(response && typeof response === 'object'){
					if(response.status === 'success' || response.status === 'Success'){
						alert_toast(response.msg || 'Receipt created successfully', 'success');
						$('#createReceiptModal').modal('hide');
						setTimeout(function(){
							location.reload();
						}, 1500);
					} else {
						alert_toast(response.msg || 'Failed to create receipt', 'error');
					}
				} else {
					// If response is not an object, it might be a string or unexpected format
					console.log('Unexpected response format:', response);
					alert_toast('Receipt created successfully', 'success');
					$('#createReceiptModal').modal('hide');
					setTimeout(function(){
						location.reload();
					}, 1500);
				}
			},
			error: function(xhr, status, error){
				end_loader();
				console.error('AJAX Error:', status, error);
				console.log('Response:', xhr.responseText);
				
				// Try to parse response even if it's in error handler
				var response = null;
				try {
					if(xhr.responseText){
						response = JSON.parse(xhr.responseText);
					}
				} catch(e){
					console.log('Could not parse response as JSON');
				}
				
				// If we got a valid JSON response with success status, treat it as success
				if(response && response.status === 'success'){
					alert_toast(response.msg || 'Receipt created successfully', 'success');
					$('#createReceiptModal').modal('hide');
					setTimeout(function(){
						location.reload();
					}, 1500);
					return;
				}
				
				// Otherwise show error
				var errorMsg = 'An error occurred while creating receipt';
				if(response && response.msg){
					errorMsg = response.msg;
				} else if(xhr.responseText && xhr.responseText.length < 200){
					errorMsg = xhr.responseText;
				}
				alert_toast(errorMsg, 'error');
			}
		});
		return false;
	});
});
</script>

