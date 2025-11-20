<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Invoice Management</h3>
		<div class="card-tools">
			<button class="btn btn-primary btn-sm" type="button" id="print_reports">
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
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary btn-sm" id="print_invoice">Print Invoice</button>
			</div>
		</div>
	</div>
</div>

<!-- Create Receipt Modal (Updated with working version) -->
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
				<div id="orcr_documents"><!-- Documents will be loaded here --></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Edit Invoice Modal -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Invoice Status</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="edit_invoice_form">
        <div class="modal-body">
          <input type="hidden" name="invoice_id" id="edit_invoice_id">
          <div class="form-group">
            <label>Payment Status</label>
            <select class="form-control" name="status" id="edit_invoice_status" required>
              <option value="pending">Pending</option>
              <option value="paid">Paid</option>
              <option value="partial">Partial</option>
              <option value="late">Late</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Update Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.create_receipt, .view_order_btn {
	cursor: pointer;
	position: relative;
	z-index: 1;
}
.create_receipt:hover, .view_order_btn:hover {
	opacity: 0.9;
}
.table td {
	position: relative;
}
</style>

<script>
// Logo URLs for printing
var mainLogoUrl = '<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>';
var secondaryLogoUrl = '<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>';

function formatTransactionType(type){
	if(!type) return 'Motorcycle Purchase';
	var normalized = type.toString().trim().toLowerCase().replace(/\s+/g,'_');
	switch(normalized){
		case 'motorcycle_purchase':
			return 'Motorcycle Purchase';
		case 'motorcycle_parts_purchase':
		case 'motorcycle_parts':
			return 'Motorcycle Parts Purchase';
		case 'oils_purchase':
		case 'oil_purchase':
			return 'Oils Purchase';
		default:
			var cleaned = type.toString().replace(/_/g,' ').trim();
			return cleaned.replace(/\b\w/g, function(letter){ return letter.toUpperCase(); });
	}
}

$(document).ready(function(){
	// Load initial data
	loadInvoices();
	loadStats();

	// Create receipt button click - use delegated event handler (WORKING VERSION)
	$(document).on('click', '.create_receipt', function(e){
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
	
	// Submit receipt form (WORKING VERSION)
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

	// Archive invoice
	$(document).on('click', '.delete_invoice', function(){
		var invoice_id = $(this).data('id');
		_conf("Are you sure to archive this invoice? It will be hidden from active lists but can be restored later.","delete_invoice",[invoice_id]);
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
			dataType: 'json',
			success: function(resp){
				if(resp && resp.status === 'success'){
					$('#uploadOrcrModal').modal('hide');
					alert_toast(resp.msg || 'Documents uploaded','success');
				}else{
					alert_toast(resp.msg || 'Upload failed','error');
				}
				end_loader();
			}
		});
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
		// Build printable content dynamically
		var printContent = '<div class="printable">';
		printContent += '<div class="report-header">';
		printContent += '<div style="flex:0 0 auto; margin-right:20px;">';
		printContent += '<img src="' + mainLogoUrl + '" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">';
		printContent += '</div>';
		printContent += '<div style="flex:1; text-align:center;">';
		printContent += '<h2>Invoice Management Report</h2>';
		printContent += '<p>Generated on ' + new Date().toLocaleDateString() + '</p>';
		printContent += '</div>';
		printContent += '<div style="flex:0 0 auto; margin-left:20px;">';
		printContent += '<img src="' + secondaryLogoUrl + '" alt="Secondary Logo" style="width:100px; height:100px; object-fit:contain;">';
		printContent += '</div>';
		printContent += '</div>';
		
		// Stats section
		printContent += '<div class="stats-section">';
		printContent += '<div class="stat-item"><div class="stat-number">' + ($('#total_invoices').text() || '0') + '</div><div class="stat-label">Total Invoices</div></div>';
		printContent += '<div class="stat-item"><div class="stat-number">' + ($('#paid_invoices').text() || '0') + '</div><div class="stat-label">Paid</div></div>';
		printContent += '<div class="stat-item"><div class="stat-number">' + ($('#unpaid_invoices').text() || '0') + '</div><div class="stat-label">Unpaid</div></div>';
		printContent += '<div class="stat-item"><div class="stat-number">' + ($('#total_amount').text() || '₱0.00') + '</div><div class="stat-label">Total Amount</div></div>';
		printContent += '</div>';
		
		// Table
		printContent += '<table class="invoices-table">';
		printContent += '<thead><tr>';
		printContent += '<th>#</th><th>Invoice No.</th><th>Customer</th><th>Transaction Type</th><th>Total Amount</th><th>Payment Status</th><th>Generated Date</th><th>Receipt</th>';
		printContent += '</tr></thead><tbody>';
		
		// Get data from current table
		$('#invoices_table tbody tr').each(function(){
			var $row = $(this);
			if($row.find('td').length > 1 && !$row.find('td').first().text().includes('No invoices') && !$row.find('td').first().text().includes('Error')){
				printContent += '<tr>';
				$row.find('td').each(function(index){
					if(index < 8){ // Exclude action column
						var text = $(this).clone().find('button, .dropdown').remove().end().text().trim();
						printContent += '<td>' + text + '</td>';
					}
				});
				printContent += '</tr>';
			}
		});
		
		printContent += '</tbody></table>';
		printContent += '</div>';
		
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

		// Open new window
		var nw = window.open('', '_blank');
		nw.document.write('<html><head><title>Invoice Management Report</title>' + ns + '</head><body>' + printContent + '</body></html>');
		nw.document.close();

		// Wait until content is fully loaded before printing
		nw.onload = function(){
			nw.focus();
			nw.print();
			setTimeout(function(){ nw.close(); }, 500);
		};
	});

	// OR/CR actions (aligned with customer_accounts)
	$(document).on('click', '.upload_orcr', function(e){
		e.preventDefault();
		var clientId = $(this).data('client-id') || '';
		var name = $(this).data('name') || '';
		if(!clientId){ alert_toast('Customer not found for this invoice','error'); return; }
		$('#upload_client_id').val(clientId);
		$('#upload_customer_name').val(name);
		$('#uploadOrcrModal').modal('show');
	});

	$(document).on('click', '.view_orcr', function(e){
		e.preventDefault();
		var clientId = $(this).data('client-id') || '';
		if(!clientId){ alert_toast('Customer not found for this invoice','error'); return; }
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

	$(document).on('click', '.mark-status', function(e){
    e.preventDefault();
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
                loadInvoices();
                loadStats();
            }else{
                alert_toast(resp.msg || 'Failed to update status','error');
            }
        },
        error: function(){ alert_toast('Failed to update status','error'); }
    });
});

	$(document).on('click', '.edit_invoice', function(e){
  e.preventDefault();
  var invoiceId = $(this).data('id');
  $('#edit_invoice_id').val(invoiceId);
  // Load current status via AJAX
  $.get(_base_url_+'classes/Invoice.php', {action: 'get_invoice', invoice_id: invoiceId}, function(data){
    if(data && data.status == 'success' && data.data.payment_status){
      $('#edit_invoice_status').val(data.data.payment_status);
    } else {
      $('#edit_invoice_status').val('pending');
    }
    $('#editInvoiceModal').modal('show');
  },'json');
});

$('#edit_invoice_form').submit(function(e){
  e.preventDefault();
  var formData = $(this).serialize();
  $.post(_base_url_+'classes/Invoice.php?action=update_status', formData, function(resp){
    if(resp && resp.status == 'success'){
      $('#editInvoiceModal').modal('hide');
      alert_toast('Status updated','success');
      location.reload();
    } else {
      alert_toast(resp.msg || 'Failed to update status','error');
    }
  },'json');
});

	function loadInvoices(){
		var filters = {
			date_start: $('#date_start').val(),
			date_end: $('#date_end').val(),
			payment_status: $('#payment_status').val()
		};

		start_loader();
		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=get_all_invoices',
			method: 'GET',
			data: filters,
			dataType: 'json',
			success: function(resp){
				end_loader();
				if(resp && resp.status == 'success'){
					var html = '';
					var printHtml = '';
					
					if(resp.data && resp.data.length > 0){
						$.each(resp.data, function(index, invoice){
							var status_class = '';
							var status_text = '';
							switch(invoice.payment_status){
								case 'paid':
									status_class = 'badge badge-success';
									status_text = 'Paid';
									break;
								case 'late':
									status_class = 'badge badge-danger';
									status_text = 'Late';
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

							// Regular table HTML
							html += '<tr>';
							html += '<td>' + (index + 1) + '</td>';
							html += '<td><strong>' + (invoice.invoice_number || 'N/A') + '</strong></td>';
							html += '<td>' + (invoice.firstname || '') + ' ' + (invoice.lastname || '') + '<br><small>' + (invoice.email || '') + '</small></td>';
							var formattedType = formatTransactionType(invoice.transaction_type);
							html += '<td>' + formattedType + '</td>';
							html += '<td class="text-right">₱' + parseFloat(invoice.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
							html += '<td><span class="' + status_class + '">' + status_text + '</span></td>';
							html += '<td>' + (invoice.generated_at ? new Date(invoice.generated_at).toLocaleDateString() : '-') + '</td>';
							var receiptCell = '<span class="text-muted">None</span>';
							var receiptCount = parseInt(invoice.receipt_count || 0);
							if(receiptCount > 0){
								var latestReceipt = invoice.receipt_date ? new Date(invoice.receipt_date).toLocaleDateString() : '';
								var label = receiptCount === 1 ? 'Receipt' : 'Receipts';
								receiptCell = '<span class="badge badge-secondary">'+receiptCount+' '+label+'</span>';
								if(latestReceipt){
									receiptCell += '<br><small>Last: '+latestReceipt+'</small>';
								}
							}
							html += '<td>' + receiptCell + '</td>';
							html += '<td>';
							html += '<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">Action <span class="sr-only">Toggle Dropdown</span></button>';
							html += '<div class="dropdown-menu" role="menu">';
							html += '<a class="dropdown-item view_invoice" href="#" data-id="' + invoice.id + '"><span class="fa fa-eye text-primary"></span> View</a>';
							html += '<div class="dropdown-divider"></div>';
							html += '<a class="dropdown-item edit_invoice" href="#" data-id="' + invoice.id + '"><span class="fa fa-edit text-primary"></span> Edit</a>';
							if(invoice.payment_status !== 'paid'){
								html += '<div class="dropdown-divider"></div>';
								var balance = typeof invoice.balance_remaining !== 'undefined' && invoice.balance_remaining !== null
									? invoice.balance_remaining
									: invoice.total_amount;
								html += '<a class="dropdown-item create_receipt" href="#" data-id="' + invoice.id + '" data-amount="' + balance + '"><span class="fa fa-receipt text-success"></span> Create Receipt</a>';
							}
							html += '<div class="dropdown-divider"></div>';
							html += '<a class="dropdown-item delete_invoice" href="#" data-id="' + invoice.id + '"><span class="fa fa-archive text-warning"></span> Archive</a>';
							html += '</div>';
							html += '</td>';
							html += '</tr>';

							// Print table HTML
							printHtml += '<tr>';
							printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + (index + 1) + '</td>';
							printHtml += '<td style="border:1px solid #ddd; padding:8px;"><strong>' + (invoice.invoice_number || 'N/A') + '</strong></td>';
							printHtml += '<td style="border:1px solid #ddd; padding:8px;">' + (invoice.firstname || '') + ' ' + (invoice.lastname || '') + '<br><small>' + (invoice.email || '') + '</small></td>';
							printHtml += '<td style="border:1px solid #ddd; padding:8px;">' + formattedType + '</td>';
							printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:right;">₱' + parseFloat(invoice.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
							printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + status_text + '</td>';
							printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + (invoice.generated_at ? new Date(invoice.generated_at).toLocaleDateString() : '-') + '</td>';
							if(receiptCount > 0){
								var latestReceiptPrint = invoice.receipt_date ? new Date(invoice.receipt_date).toLocaleDateString() : '';
								var labelPrint = receiptCount === 1 ? 'receipt' : 'receipts';
								printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">' + receiptCount + ' ' + labelPrint + (latestReceiptPrint ? ' (last: '+latestReceiptPrint+')' : '') + '</td>';
							}else{
								printHtml += '<td style="border:1px solid #ddd; padding:8px; text-align:center;">-</td>';
							}
							printHtml += '</tr>';
						});
					} else {
						html = '<tr><td colspan="9" class="text-center">No invoices found</td></tr>';
						printHtml = '<tr><td colspan="7" class="text-center">No invoices found</td></tr>';
					}
					
					$('#invoices_table tbody').html(html);
					
					// Update print table if it exists
					if($('#print_invoices_table').length){
						$('#print_invoices_table').html(printHtml);
					}
					
					// Ensure DataTable is initialized/reinitialized after content load
					if($.fn.DataTable){
						if($.fn.DataTable.isDataTable('#invoices_table')){
							$('#invoices_table').DataTable().clear().destroy();
						}
						$('#invoices_table').DataTable({
							responsive: true,
							pageLength: 25,
							order: [[6, 'desc']], // Sort by generated date descending
							columnDefs: [
								{ orderable: false, targets: -1 },
								{ className: 'text-right', targets: [4] },
							],
						});
					}
				} else {
					var errorMsg = resp && resp.msg ? resp.msg : 'Failed to load invoices';
					alert_toast(errorMsg, 'error');
					$('#invoices_table tbody').html('<tr><td colspan="9" class="text-center">Error loading invoices</td></tr>');
				}
			},
			error: function(xhr, status, error){
				end_loader();
				console.error('AJAX Error loading invoices:', error);
				console.error('Response:', xhr.responseText);
				alert_toast('Error loading invoices. Please check console for details.', 'error');
				$('#invoices_table tbody').html('<tr><td colspan="9" class="text-center">Error loading invoices</td></tr>');
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
				if(resp && resp.status == 'success' && resp.data){
					// Update regular stats
					$('#total_invoices').text(resp.data.total_invoices || 0);
					$('#paid_invoices').text(resp.data.paid_invoices || 0);
					$('#unpaid_invoices').text(resp.data.unpaid_invoices || 0);
					$('#total_amount').text('₱' + parseFloat(resp.data.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
					
					// Update print stats if elements exist
					if($('#print_total_invoices').length){
						$('#print_total_invoices').text(resp.data.total_invoices || 0);
					}
					if($('#print_paid_invoices').length){
						$('#print_paid_invoices').text(resp.data.paid_invoices || 0);
					}
					if($('#print_unpaid_invoices').length){
						$('#print_unpaid_invoices').text(resp.data.unpaid_invoices || 0);
					}
					if($('#print_total_amount').length){
						$('#print_total_amount').text('₱' + parseFloat(resp.data.total_amount || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
					}
				}
			},
			error: function(xhr, status, error){
				console.error('Error loading stats:', error);
			}
		});
	}

	function viewInvoice(invoice_id){
		start_loader();
		$.ajax({
			url: _base_url_ + 'classes/Invoice.php?action=get_invoice&invoice_id=' + invoice_id,
			method: 'GET',
			dataType: 'json',
			success: function(resp){
				end_loader();
				if(resp && resp.status == 'success' && resp.data){
					var invoice = resp.data;
					var html = generateInvoiceHTML(invoice);
					$('#invoice_details').html(html);
					$('#viewInvoiceModal').data('invoice-id', invoice_id);
					$('#viewInvoiceModal').modal('show');
				} else {
					alert_toast(resp && resp.msg ? resp.msg : 'Failed to load invoice details', 'error');
				}
			},
			error: function(xhr, status, error){
				end_loader();
				console.error('Error loading invoice:', error);
				alert_toast('Error loading invoice details. Please try again.', 'error');
			}
		});
	}

	window.delete_invoice = function(invoice_id){
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
					alert_toast("Invoice archived successfully.", 'success');
					loadInvoices();
					loadStats();
				} else {
					var msg = resp.msg || "An error occurred while archiving invoice.";
					alert_toast(msg, 'error');
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
		html += '<p><strong>Transaction Type:</strong> ' + formatTransactionType(invoice.transaction_type) + '</p>';
		html += '</div>';
		var statusClass = (invoice.payment_status == 'paid') ? 'success' : (invoice.payment_status == 'late' ? 'danger' : 'warning');
		html += '<div class="col-md-6 text-right">';
		html += '<p><strong>Date:</strong> ' + new Date(invoice.generated_at).toLocaleDateString() + '</p>';
		html += '<p><strong>Due Date:</strong> ' + new Date(invoice.due_date).toLocaleDateString() + '</p>';
		html += '<p><strong>Status:</strong> <span class="badge badge-' + statusClass + '">' + (invoice.payment_status || '').toUpperCase() + '</span></p>';
		html += '</div>';
		html += '</div>';

		html += '<div class="table-responsive">';
		html += '<table class="table table-bordered">';
		html += '<thead><tr><th>Item</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>';
		html += '<tbody>';
		$.each(invoice.items, function(index, item){
			// Calculate unit_price if it's zero or missing, using total_price and quantity
			var unit_price = parseFloat(item.unit_price) || 0;
			if(unit_price == 0 && parseFloat(item.quantity) > 0 && parseFloat(item.total_price) > 0){
				unit_price = parseFloat(item.total_price) / parseFloat(item.quantity);
			}
			html += '<tr>';
			html += '<td>' + item.item_name + '</td>';
			html += '<td>' + (item.item_description || '-') + '</td>';
			html += '<td>' + item.quantity + '</td>';
			html += '<td>₱' + unit_price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
			html += '<td>₱' + parseFloat(item.total_price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
			html += '</tr>';
		});
		html += '</tbody>';
		html += '</table>';
		html += '</div>';

		html += '<div class="row">';
		html += '<div class="col-md-6">';
		// html += '<p><strong>Pickup Location:</strong><br>' + invoice.pickup_location + '</p>';
		// html += '<p><strong>Payment Instructions:</strong><br>' + invoice.payment_instructions + '</p>';
		html += '</div>';
		html += '<div class="col-md-6 text-right">';
		html += '<p><strong>Total Amount:</strong> ₱' + parseFloat(invoice.total_amount).toLocaleString() + '</p>';
		if(typeof invoice.balance_remaining !== 'undefined'){
			html += '<p><strong>Balance Remaining:</strong> ₱' + parseFloat(invoice.balance_remaining).toLocaleString() + '</p>';
		}
		if(typeof invoice.late_fee_amount !== 'undefined' && parseFloat(invoice.late_fee_amount) > 0){
			html += '<p><strong>Late Fee:</strong> ₱' + parseFloat(invoice.late_fee_amount).toLocaleString() + '</p>';
		}
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
		html += '<p><strong>Transaction Type:</strong> ' + formatTransactionType(invoice.transaction_type) + '</p>';
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
				// Calculate unit_price if it's zero or missing, using total_price and quantity
				var unit_price = parseFloat(item.unit_price) || 0;
				if(unit_price == 0 && parseFloat(item.quantity) > 0 && parseFloat(item.total_price) > 0){
					unit_price = parseFloat(item.total_price) / parseFloat(item.quantity);
				}
				html += '<tr>';
				html += '<td>' + item.item_name + '</td>';
				html += '<td>' + (item.item_description || '-') + '</td>';
				html += '<td class="text-center">' + item.quantity + '</td>';
				html += '<td class="text-right">₱' + unit_price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
				html += '<td class="text-right">₱' + parseFloat(item.total_price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
				html += '</tr>';
			});
		}
		
		html += '</tbody>';
		html += '</table>';
		
		// Totals
		html += '<div class="totals">';
		html += '<table class="totals-table">';
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