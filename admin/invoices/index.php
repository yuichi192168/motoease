<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Invoice Management</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-default" type="button" id="print_reports">
				<span class="fa fa-print"></span> Print Report
			</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- Statistics Cards -->
			<div class="row mb-3">
				<div class="col-md-3">
					<div class="info-box bg-primary">
						<span class="info-box-icon"><i class="fas fa-file-invoice"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Invoices</span>
							<span class="info-box-number" id="total_invoices">0</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-success">
						<span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Paid Invoices</span>
							<span class="info-box-number" id="paid_invoices">0</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-warning">
						<span class="info-box-icon"><i class="fas fa-clock"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Unpaid Invoices</span>
							<span class="info-box-number" id="unpaid_invoices">0</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-info">
						<span class="info-box-icon"><i class="fas fa-peso-sign"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Amount</span>
							<span class="info-box-number" id="total_amount">₱0.00</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Filters -->
			<div class="row mb-3">
				<div class="col-md-12">
					<div class="card card-outline card-secondary">
						<div class="card-header">
							<h4 class="card-title">Filters</h4>
						</div>
						<div class="card-body">
							<form id="filter-form">
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Date Start</label>
											<input type="date" class="form-control" name="date_start" id="date_start">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Date End</label>
											<input type="date" class="form-control" name="date_end" id="date_end">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Payment Status</label>
											<select class="form-control" name="payment_status" id="payment_status">
												<option value="">All Status</option>
												<option value="paid">Paid</option>
												<option value="unpaid">Unpaid</option>
												<option value="partial">Partial</option>
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>&nbsp;</label>
											<button type="submit" class="btn btn-primary btn-block">
												<i class="fa fa-filter"></i> Filter
											</button>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

			<!-- Printable Content -->
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
						<h4 style="margin:0;"><b>Invoice Management Report</b></h4>
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
						<div class="stat-number" style="font-size:24px; font-weight:bold; color:#007bff;" id="print_total_invoices">0</div>
						<div class="stat-label" style="font-size:11px; color:#666; margin-top:5px;">Total Invoices</div>
					</div>
					<div class="stat-item" style="text-align:center;">
						<div class="stat-number" style="font-size:24px; font-weight:bold; color:#007bff;" id="print_paid_invoices">0</div>
						<div class="stat-label" style="font-size:11px; color:#666; margin-top:5px;">Paid Invoices</div>
					</div>
					<div class="stat-item" style="text-align:center;">
						<div class="stat-number" style="font-size:24px; font-weight:bold; color:#007bff;" id="print_unpaid_invoices">0</div>
						<div class="stat-label" style="font-size:11px; color:#666; margin-top:5px;">Unpaid Invoices</div>
					</div>
					<div class="stat-item" style="text-align:center;">
						<div class="stat-number" style="font-size:24px; font-weight:bold; color:#007bff;" id="print_total_amount">₱0.00</div>
						<div class="stat-label" style="font-size:11px; color:#666; margin-top:5px;">Total Amount</div>
					</div>
				</div>

				<!-- Invoices Table -->
				<table class="invoices-table" style="width:100%; border-collapse:collapse; margin:20px 0;">
					<thead>
						<tr>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">#</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Invoice No.</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Customer</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Transaction Type</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Total Amount</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Payment Status</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Generated Date</th>
							<th style="border:1px solid #ddd; padding:8px; text-align:center; font-weight:bold; background-color:#f8f9fa;">Receipt</th>
						</tr>
					</thead>
					<tbody id="print_invoices_table">
						<!-- Data will be populated by JavaScript -->
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

			<!-- Invoices Table -->
			<div class="table-responsive">
				<table class="table table-bordered table-stripped" id="invoices_table">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Invoice No.</th>
							<th>Customer</th>
							<th>Transaction Type</th>
							<th>Total Amount</th>
							<th>Payment Status</th>
							<th>Generated Date</th>
							<th>Receipt</th>
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

<!-- View Invoice Modal -->
<div class="modal fade" id="viewInvoiceModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Invoice Details</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="invoice_details">
				<!-- Invoice details will be loaded here -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="print_invoice">Print Invoice</button>
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
						<label>Amount Paid</label>
						<input type="number" class="form-control" name="amount_paid" id="amount_paid" step="0.01" required>
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
						<label>Payment Reference (Optional)</label>
						<input type="text" class="form-control" name="payment_reference" id="payment_reference" placeholder="Transaction ID, Check No., etc.">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="submit" form="receipt_form" class="btn btn-success">Create Receipt</button>
			</div>
		</div>
	</div>
</div>

<script>
// Logo URLs for printing
var mainLogoUrl = '<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>';
var secondaryLogoUrl = '<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>';

$(document).ready(function(){
	// Load initial data
	loadInvoices();
	loadStats();

	// Filter form submission
	$('#filter-form').submit(function(e){
		e.preventDefault();
		loadInvoices();
		loadStats();
	});

	// View invoice
	$(document).on('click', '.view_invoice', function(){
		var invoice_id = $(this).data('id');
		viewInvoice(invoice_id);
	});

	// Create receipt
	$(document).on('click', '.create_receipt', function(){
		var invoice_id = $(this).data('id');
		var total_amount = $(this).data('amount');
		$('#receipt_invoice_id').val(invoice_id);
		$('#amount_paid').val(total_amount);
		$('#createReceiptModal').modal('show');
	});

	// Delete invoice
	$(document).on('click', '.delete_invoice', function(){
		var invoice_id = $(this).data('id');
		_conf("Are you sure to delete this invoice permanently?","delete_invoice",[invoice_id]);
	});

	// Receipt form submission
	$('#receipt_form').submit(function(e){
		e.preventDefault();
		createReceipt();
	});

	// Print invoice
	$('#print_invoice').click(function(){
		var invoice_id = $('#viewInvoiceModal').data('invoice-id');
		if(invoice_id && invoice_id !== 'undefined'){
			// Get invoice details for printing
			$.ajax({
				url: _base_url_ + 'classes/Invoice.php?action=get_invoice&invoice_id=' + invoice_id,
				method: 'GET',
				dataType: 'json',
				success: function(resp){
					if(resp.status == 'success'){
						var invoice = resp.data;
						printInvoice(invoice);
					} else {
						alert_toast('Error loading invoice details for printing', 'error');
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX error loading invoice:', error);
					alert_toast('Error loading invoice details for printing', 'error');
				}
			});
		} else {
			alert_toast('Invoice ID not found', 'error');
		}
	});

	// Print reports
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
					'.invoices-table{width:100%; border-collapse:collapse; margin:20px 0;}' +
					'.invoices-table th, .invoices-table td{border:1px solid #ddd; padding:8px; text-align:left; font-size:11px;}' +
					'.invoices-table th{background-color:#f8f9fa; font-weight:bold; text-align:center;}' +
					'.footer-info{position:fixed;bottom:0;left:0;right:0;margin-top:30px;padding:15px;border-top:1px solid #ddd;text-align:center;font-size:10px;color:#666;background-color:white;}' +
					'@media print { #filter-form, #print_reports { display:none !important; } .footer-info{position:fixed;bottom:0;left:0;right:0;margin:0;padding:15px;border-top:1px solid #ddd;text-align:center;font-size:10px;color:#666;background-color:white;page-break-inside:avoid;}' +
				'</style>';
		rep.prepend(ns);

		// Open new window
		var nw = window.open('', '_blank');
		nw.document.write('<html><head><title>Invoice Management Report</title></head><body>' + rep.html() + '</body></html>');
		nw.document.close();

		// Wait until content is fully loaded before printing
		nw.onload = function(){
			nw.focus();
			nw.print();
			setTimeout(function(){ nw.close(); }, 500);
		};
	});

	function loadInvoices(){
		var filters = {
			date_start: $('#date_start').val(),
			date_end: $('#date_end').val(),
			payment_status: $('#payment_status').val()
		};

		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=get_all_invoices',
			method: 'GET',
			data: filters,
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					var html = '';
					var printHtml = '';
					$.each(resp.data, function(index, invoice){
						var status_class = '';
						var status_text = '';
						switch(invoice.payment_status){
							case 'paid':
								status_class = 'badge badge-success';
								status_text = 'Paid';
								break;
							case 'unpaid':
								status_class = 'badge badge-warning';
								status_text = 'Unpaid';
								break;
							case 'partial':
								status_class = 'badge badge-info';
								status_text = 'Partial';
								break;
						}

						// Regular table HTML
						html += '<tr>';
						html += '<td>' + (index + 1) + '</td>';
						html += '<td><strong>' + invoice.invoice_number + '</strong></td>';
						html += '<td>' + invoice.firstname + ' ' + invoice.lastname + '<br><small>' + invoice.email + '</small></td>';
						html += '<td>' + invoice.transaction_type.replace('_', ' ').toUpperCase() + '</td>';
						html += '<td class="text-right">₱' + parseFloat(invoice.total_amount).toLocaleString() + '</td>';
						html += '<td><span class="' + status_class + '">' + status_text + '</span></td>';
						html += '<td>' + new Date(invoice.generated_at).toLocaleDateString() + '</td>';
						html += '<td>' + (invoice.receipt_number ? '<span class="badge badge-success">' + invoice.receipt_number + '</span>' : '-') + '</td>';
						html += '<td>';
						html += '<button class="btn btn-sm btn-primary view_invoice" data-id="' + invoice.id + '">View</button> ';
						if(invoice.payment_status != 'paid'){
							html += '<button class="btn btn-sm btn-success create_receipt" data-id="' + invoice.id + '" data-amount="' + invoice.total_amount + '">Receipt</button> ';
							html += '<button class="btn btn-sm btn-danger delete_invoice" data-id="' + invoice.id + '">Delete</button>';
						}
						html += '</td>';
						html += '</tr>';

						// Print table HTML
						printHtml += '<tr>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + (index + 1) + '</td>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px;"><strong>' + invoice.invoice_number + '</strong></td>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px;">' + invoice.firstname + ' ' + invoice.lastname + '<br><small>' + invoice.email + '</small></td>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px;">' + invoice.transaction_type.replace('_', ' ').toUpperCase() + '</td>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:right;">₱' + parseFloat(invoice.total_amount).toLocaleString() + '</td>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + status_text + '</td>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + new Date(invoice.generated_at).toLocaleDateString() + '</td>';
						printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + (invoice.receipt_number ? invoice.receipt_number : '-') + '</td>';
						printHtml += '</tr>';
					});
					$('#invoices_table tbody').html(html);
					$('#print_invoices_table').html(printHtml);
				}
			}
		});
	}

	function loadStats(){
		var filters = {
			date_start: $('#date_start').val(),
			date_end: $('#date_end').val()
		};

		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=get_stats',
			method: 'GET',
			data: filters,
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					// Update regular stats
					$('#total_invoices').text(resp.data.total_invoices || 0);
					$('#paid_invoices').text(resp.data.paid_invoices || 0);
					$('#unpaid_invoices').text(resp.data.unpaid_invoices || 0);
					$('#total_amount').text('₱' + parseFloat(resp.data.total_amount || 0).toLocaleString());
					
					// Update print stats
					$('#print_total_invoices').text(resp.data.total_invoices || 0);
					$('#print_paid_invoices').text(resp.data.paid_invoices || 0);
					$('#print_unpaid_invoices').text(resp.data.unpaid_invoices || 0);
					$('#print_total_amount').text('₱' + parseFloat(resp.data.total_amount || 0).toLocaleString());
				}
			}
		});
	}

	function viewInvoice(invoice_id){
		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=get_invoice&invoice_id=' + invoice_id,
			method: 'GET',
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					var invoice = resp.data;
					var html = generateInvoiceHTML(invoice);
					$('#invoice_details').html(html);
					$('#viewInvoiceModal').data('invoice-id', invoice_id);
					$('#viewInvoiceModal').modal('show');
				}
			}
		});
	}

	function createReceipt(){
		var formData = {
			invoice_id: $('#receipt_invoice_id').val(),
			payment_data: {
				amount_paid: $('#amount_paid').val(),
				payment_method: $('#payment_method').val(),
				payment_reference: $('#payment_reference').val()
			},
			staff_id: '<?= $_settings->userdata('id') ?>'
		};

		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=create_receipt',
			method: 'POST',
			data: formData,
			dataType: 'json',
			success: function(resp){
				if(resp.status == 'success'){
					alert_toast('Receipt created successfully!', 'success');
					$('#createReceiptModal').modal('hide');
					loadInvoices();
					loadStats();
				} else {
					alert_toast(resp.msg, 'error');
				}
			}
		});
	}

	function delete_invoice(invoice_id){
		start_loader();
		console.log('Deleting invoice with ID:', invoice_id);
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_invoice",
			method: "POST",
			data: {id: invoice_id},
			dataType: "json",
			error: err => {
				console.error('AJAX Error:', err);
				alert_toast("An error occurred while deleting invoice.", 'error');
				end_loader();
			},
			success: function(resp){
				console.log('Delete response:', resp);
				if(typeof resp == 'object' && resp.status == 'success'){
					alert_toast("Invoice deleted successfully.", 'success');
					loadInvoices();
					loadStats();
				} else {
					alert_toast(resp.msg || "An error occurred while deleting invoice.", 'error');
				}
				end_loader();
			}
		});
	}

	function generateInvoiceHTML(invoice){
		var html = '<div class="invoice-preview">';
		html += '<div class="row mb-3">';
		html += '<div class="col-md-6">';
		html += '<h4>Invoice: ' + invoice.invoice_number + '</h4>';
		html += '<p><strong>Customer:</strong> ' + invoice.firstname + ' ' + invoice.lastname + '</p>';
		html += '<p><strong>Email:</strong> ' + invoice.email + '</p>';
		html += '<p><strong>Contact:</strong> ' + invoice.contact + '</p>';
		html += '</div>';
		html += '<div class="col-md-6 text-right">';
		html += '<p><strong>Date:</strong> ' + new Date(invoice.generated_at).toLocaleDateString() + '</p>';
		html += '<p><strong>Due Date:</strong> ' + new Date(invoice.due_date).toLocaleDateString() + '</p>';
		html += '<p><strong>Status:</strong> <span class="badge badge-' + (invoice.payment_status == 'paid' ? 'success' : 'warning') + '">' + invoice.payment_status.toUpperCase() + '</span></p>';
		html += '</div>';
		html += '</div>';

		html += '<div class="table-responsive">';
		html += '<table class="table table-bordered">';
		html += '<thead><tr><th>Item</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>';
		html += '<tbody>';
		$.each(invoice.items, function(index, item){
			html += '<tr>';
			html += '<td>' + item.item_name + '</td>';
			html += '<td>' + (item.item_description || '-') + '</td>';
			html += '<td>' + item.quantity + '</td>';
			html += '<td>₱' + parseFloat(item.unit_price).toLocaleString() + '</td>';
			html += '<td>₱' + parseFloat(item.total_price).toLocaleString() + '</td>';
			html += '</tr>';
		});
		html += '</tbody>';
		html += '</table>';
		html += '</div>';

		html += '<div class="row">';
		html += '<div class="col-md-6">';
		html += '<p><strong>Pickup Location:</strong><br>' + invoice.pickup_location + '</p>';
		html += '<p><strong>Payment Instructions:</strong><br>' + invoice.payment_instructions + '</p>';
		html += '</div>';
		html += '<div class="col-md-6 text-right">';
		html += '<p><strong>Subtotal:</strong> ₱' + parseFloat(invoice.subtotal).toLocaleString() + '</p>';
		html += '<p><strong>VAT:</strong> ₱' + parseFloat(invoice.vat_amount).toLocaleString() + '</p>';
		html += '<p><strong>Total Amount:</strong> ₱' + parseFloat(invoice.total_amount).toLocaleString() + '</p>';
		html += '</div>';
		html += '</div>';

		if(invoice.receipt){
			html += '<hr>';
			html += '<h5>Receipt Information</h5>';
			html += '<p><strong>Receipt No:</strong> ' + invoice.receipt.receipt_number + '</p>';
			html += '<p><strong>Amount Paid:</strong> ₱' + parseFloat(invoice.receipt.amount_paid).toLocaleString() + '</p>';
			html += '<p><strong>Payment Method:</strong> ' + invoice.receipt.payment_method.toUpperCase() + '</p>';
			html += '<p><strong>Date Paid:</strong> ' + new Date(invoice.receipt.issued_at).toLocaleDateString() + '</p>';
		}

		html += '</div>';
		return html;
	}

	function printInvoice(invoice){
		// Create print content
		var printContent = generatePrintInvoiceHTML(invoice);
		
		// Create print styles
		var printStyles = '<style>' +
			'body{margin:20px;font-family:Arial,sans-serif;font-size:12px;line-height:1.4;}' +
			'table{border-collapse:collapse;width:100%;margin:10px 0;}' +
			'table th, table td{border:1px solid #000;padding:8px;text-align:left;}' +
			'table th{background-color:#f8f9fa;font-weight:bold;text-align:center;}' +
			'.text-center{text-align:center;}' +
			'.text-right{text-align:right;}' +
			'.invoice-header{display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #000;padding-bottom:15px;margin-bottom:20px;}' +
			'.company-info{text-align:center;flex:1;margin:0 20px;}' +
			'.company-info h2{text-transform:uppercase;font-weight:bold;margin:0;color:#333;}' +
			'.company-info p{margin:5px 0;color:#666;}' +
			'.invoice-details{display:flex;justify-content:space-between;margin-bottom:20px;}' +
			'.customer-info, .invoice-info{flex:1;}' +
			'.customer-info h4, .invoice-info h4{margin:0 0 10px 0;color:#333;border-bottom:1px solid #ddd;padding-bottom:5px;}' +
			'.totals{display:flex;justify-content:flex-end;margin:20px 0;}' +
			'.totals-table{width:300px;}' +
			'.totals-table td{padding:5px 10px;border-bottom:1px solid #eee;}' +
			'.totals-table .total-row{font-weight:bold;border-top:2px solid #000;border-bottom:2px solid #000;}' +
			'.payment-status{padding:10px;text-align:center;font-weight:bold;margin:20px 0;}' +
			'.payment-status.paid{background-color:#d4edda;color:#155724;border:1px solid #c3e6cb;}' +
			'.payment-status.unpaid{background-color:#fff3cd;color:#856404;border:1px solid #ffeaa7;}' +
			'.footer-info{padding:20px;border-top:1px solid #ddd;margin-top:30px;}' +
			'.footer-info h5{margin:0 0 10px 0;color:#333;}' +
			'.footer-info p{margin:5px 0;color:#666;font-size:11px;}' +
			'@media print{body{margin:0;}}' +
		'</style>';
		
		// Open new window for printing
		var printWindow = window.open('', '_blank');
		printWindow.document.write('<html><head><title>Invoice ' + invoice.invoice_number + '</title>' + printStyles + '</head><body>' + printContent + '</body></html>');
		printWindow.document.close();
		
		// Wait until content is fully loaded before printing
		printWindow.onload = function(){
			printWindow.focus();
			printWindow.print();
			setTimeout(function(){ printWindow.close(); }, 500);
		};
	}

	function generatePrintInvoiceHTML(invoice){
		var html = '<div class="invoice-container">';
		
		// Header with dual logos
		html += '<div class="invoice-header">';
		html += '<!-- Main Logo on the left -->';
		html += '<div style="flex:0 0 auto; margin-right:20px;">';
		html += '<img src="' + mainLogoUrl + '" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">';
		html += '</div>';
		
		html += '<!-- Centered Company Info -->';
		html += '<div class="company-info">';
		html += '<h2>Star Honda Calamba</h2>';
		html += '<p>National Highway Brgy. Parian, Calamba City, Laguna</p>';
		html += '<p>Phone: 0948-235-3207 | Email: starhondacalamba55@gmail.com</p>';
		html += '<h3>INVOICE</h3>';
		html += '</div>';
		
		html += '<!-- Secondary Logo on the right -->';
		html += '<div style="flex:0 0 auto; margin-left:20px;">';
		html += '<img src="' + secondaryLogoUrl + '" alt="Secondary Logo" style="width:100px; height:100px; object-fit:contain;">';
		html += '</div>';
		html += '</div>';
		
		// Invoice and Customer Details
		html += '<div class="invoice-details">';
		html += '<div class="customer-info">';
		html += '<h4>Bill To:</h4>';
		html += '<p><strong>' + invoice.firstname + ' ' + invoice.lastname + '</strong></p>';
		html += '<p>' + invoice.email + '</p>';
		html += '<p>' + invoice.contact + '</p>';
		html += '</div>';
		html += '<div class="invoice-info">';
		html += '<h4>Invoice Details:</h4>';
		html += '<p><strong>Invoice No:</strong> ' + invoice.invoice_number + '</p>';
		html += '<p><strong>Date:</strong> ' + new Date(invoice.generated_at).toLocaleDateString() + '</p>';
		html += '<p><strong>Due Date:</strong> ' + new Date(invoice.due_date).toLocaleDateString() + '</p>';
		html += '<p><strong>Transaction Type:</strong> ' + invoice.transaction_type.replace('_', ' ').toUpperCase() + '</p>';
		html += '<p><strong>Payment Type:</strong> ' + invoice.payment_type.toUpperCase() + '</p>';
		html += '</div>';
		html += '</div>';
		
		// Items Table
		html += '<table class="items-table">';
		html += '<thead>';
		html += '<tr><th>Item</th><th>Description</th><th class="text-center">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th></tr>';
		html += '</thead>';
		html += '<tbody>';
		
		if(invoice.items && invoice.items.length > 0){
			$.each(invoice.items, function(index, item){
				html += '<tr>';
				html += '<td>' + item.item_name + '</td>';
				html += '<td>' + (item.item_description || '-') + '</td>';
				html += '<td class="text-center">' + item.quantity + '</td>';
				html += '<td class="text-right">₱' + parseFloat(item.unit_price).toLocaleString() + '</td>';
				html += '<td class="text-right">₱' + parseFloat(item.total_price).toLocaleString() + '</td>';
				html += '</tr>';
			});
		}
		
		html += '</tbody>';
		html += '</table>';
		
		// Totals
		html += '<div class="totals">';
		html += '<table class="totals-table">';
		html += '<tr><td>Subtotal:</td><td class="text-right">₱' + parseFloat(invoice.subtotal).toLocaleString() + '</td></tr>';
		html += '<tr><td>VAT (12%):</td><td class="text-right">₱' + parseFloat(invoice.vat_amount).toLocaleString() + '</td></tr>';
		html += '<tr class="total-row"><td><strong>Total Amount:</strong></td><td class="text-right"><strong>₱' + parseFloat(invoice.total_amount).toLocaleString() + '</strong></td></tr>';
		html += '</table>';
		html += '</div>';
		
		// Payment Status
		html += '<div class="payment-status ' + invoice.payment_status + '">';
		if(invoice.payment_status == 'paid'){
			html += '✅ PAID - Thank you for your payment!';
		} else {
			html += '⏳ PENDING PAYMENT - Payment must be completed in-store';
		}
		html += '</div>';
		
		// Receipt Information (if paid)
		if(invoice.receipt){
			html += '<div style="padding:20px;border:1px solid #28a745;background-color:#f8fff9;margin:20px 0;">';
			html += '<h5 style="color:#28a745;margin:0 0 10px 0;">Receipt Information</h5>';
			html += '<p><strong>Receipt No:</strong> ' + invoice.receipt.receipt_number + '</p>';
			html += '<p><strong>Amount Paid:</strong> ₱' + parseFloat(invoice.receipt.amount_paid).toLocaleString() + '</p>';
			html += '<p><strong>Payment Method:</strong> ' + invoice.receipt.payment_method.toUpperCase() + '</p>';
			html += '<p><strong>Date Paid:</strong> ' + new Date(invoice.receipt.issued_at).toLocaleString() + '</p>';
			if(invoice.receipt.staff_firstname){
				html += '<p><strong>Received By:</strong> ' + invoice.receipt.staff_firstname + ' ' + invoice.receipt.staff_lastname + '</p>';
			}
			if(invoice.receipt.payment_reference){
				html += '<p><strong>Reference:</strong> ' + invoice.receipt.payment_reference + '</p>';
			}
			html += '</div>';
		}
		
		// Footer Information
		html += '<div class="footer-info">';
		html += '<h5>Important Information:</h5>';
		html += '<p><strong>Pickup Location:</strong> ' + (invoice.pickup_location || 'Store Location') + '</p>';
		html += '<p><strong>Payment Instructions:</strong> ' + (invoice.payment_instructions || 'Payment must be completed in-store') + '</p>';
		if(invoice.pickup_instructions){
			html += '<p><strong>Pickup Instructions:</strong> ' + invoice.pickup_instructions + '</p>';
		}
		html += '<hr style="margin:20px 0;">';
		html += '<h5>Contact Information:</h5>';
		html += '<p>📍 National Highway Brgy. Parian, Calamba City, Laguna</p>';
		html += '<p>📞 0948-235-3207</p>';
		html += '<p>✉️ starhondacalamba55@gmail.com</p>';
		html += '<p>📘 Facebook: @starhondacalambabranch</p>';
		html += '<p style="text-align:center;margin-top:20px;font-size:10px;color:#999;">';
		html += 'This invoice was generated on ' + new Date(invoice.generated_at).toLocaleString();
		if(invoice.staff_firstname){
			html += ' by ' + invoice.staff_firstname + ' ' + invoice.staff_lastname;
		}
		html += '</p>';
		html += '</div>';
		
		html += '</div>';
		return html;
	}
});
</script>

