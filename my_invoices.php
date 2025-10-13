<?php 
if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2){
    $qry = $conn->query("SELECT * FROM `client_list` where id = '{$_settings->userdata('id')}'");
    if($qry->num_rows >0){
        $res = $qry->fetch_array();
        foreach($res as $k => $v){
            if(!is_numeric($k)){
                $$k = $v;
            }
        }
    }else{
        echo "<script> alert('You are not allowed to access this page. Unknown User ID.'); location.replace('./') </script>";
    }
}else{
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

$customer_id = $_settings->userdata('id');
?>

<div class="content py-5 mt-3">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-invoice fa-3x text-primary me-3"></i>
                                <div>
                                    <h3 class="mb-1">My Invoices & Receipts</h3>
                                    <p class="text-muted mb-0">View and download your purchase invoices and payment receipts</p>
                                </div>
                            </div>
                            <div>
                                <a href="./" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card card-outline card-info shadow rounded-0">
                    <div class="card-body text-center">
                        <i class="fas fa-file-invoice fa-2x text-info mb-2"></i>
                        <h4 id="total_invoices">0</h4>
                        <p class="text-muted mb-0">Total Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-success shadow rounded-0">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h4 id="paid_invoices">0</h4>
                        <p class="text-muted mb-0">Paid Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-outline card-warning shadow rounded-0">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h4 id="unpaid_invoices">0</h4>
                        <p class="text-muted mb-0">Unpaid Invoices</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="row">
            <div class="col-12">
                <div class="card card-outline card-dark shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Invoice History</b></h4>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-primary" onclick="refreshInvoices()">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="invoices_table">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>Transaction Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
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
                <button type="button" class="btn btn-primary" id="print_invoice_btn">Print Invoice</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

    // View invoice
    $(document).on('click', '.view_invoice', function(){
        var invoice_id = $(this).data('id');
        viewInvoice(invoice_id);
    });

    // Download receipt
    $(document).on('click', '.download_receipt', function(){
        var invoice_id = $(this).data('id');
        downloadReceipt(invoice_id);
    });

    // Print invoice
    $(document).on('click', '#print_invoice_btn', function(){
        var invoice_id = $('#viewInvoiceModal').data('invoice-id');
        if(invoice_id && invoice_id !== 'undefined'){
            window.open(_base_url_ + 'admin/invoices/print_invoice.php?id=' + invoice_id, '_blank');
        } else {
            alert('Invoice ID not found');
        }
    });

    // Handle modal close events
    $('#viewInvoiceModal').on('hidden.bs.modal', function () {
        // Clear modal data when closed
        $(this).removeData('invoice-id');
        $('#invoice_details').empty();
    });

    // Ensure close button works properly
    $('#viewInvoiceModal .close, #viewInvoiceModal [data-dismiss="modal"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Close button clicked'); // Debug log
        $('#viewInvoiceModal').modal('hide');
    });

    // Alternative close method using ESC key
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // ESC key
            $('#viewInvoiceModal').modal('hide');
        }
    });

    // Force modal to close when clicking outside
    $('#viewInvoiceModal').on('click', function(e) {
        if (e.target === this) {
            $(this).modal('hide');
        }
    });


    function loadInvoices(){
        $.ajax({
            url: _base_url_ + 'classes/Invoice.php?action=get_customer_invoices&customer_id=<?= $customer_id ?>',
            method: 'GET',
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    var html = '';
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

                        html += '<tr>';
                        html += '<td><strong>' + invoice.invoice_number + '</strong></td>';
                        html += '<td>' + new Date(invoice.generated_at).toLocaleDateString() + '</td>';
                        html += '<td>' + invoice.transaction_type.replace('_', ' ').toUpperCase() + '</td>';
                        html += '<td class="text-right">₱' + parseFloat(invoice.total_amount).toLocaleString() + '</td>';
                        html += '<td><span class="' + status_class + '">' + status_text + '</span></td>';
                        html += '<td>' + (invoice.receipt_number ? '<span class="badge badge-success">' + invoice.receipt_number + '</span>' : '-') + '</td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-primary view_invoice" data-id="' + invoice.id + '">View</button> ';
                        if(invoice.payment_status == 'paid' && invoice.receipt_number) {
                            html += '<button class="btn btn-sm btn-success download_receipt" data-id="' + invoice.id + '">Download Receipt</button>';
                        }
                        html += '</td>';
                        html += '</tr>';
                    });
                    $('#invoices_table tbody').html(html);
                }
            }
        });
    }

    function loadStats(){
        $.ajax({
            url: _base_url_ + 'classes/Invoice.php?action=get_customer_invoices&customer_id=<?= $customer_id ?>',
            method: 'GET',
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    var total = resp.data.length;
                    var paid = resp.data.filter(inv => inv.payment_status == 'paid').length;
                    var unpaid = resp.data.filter(inv => inv.payment_status == 'unpaid').length;
                    
                    $('#total_invoices').text(total);
                    $('#paid_invoices').text(paid);
                    $('#unpaid_invoices').text(unpaid);
                }
            }
        });
    }

    function viewInvoice(invoice_id){
        console.log('Loading invoice:', invoice_id); // Debug log
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
                    console.log('Invoice ID stored in modal:', $('#viewInvoiceModal').data('invoice-id')); // Debug log
                    $('#viewInvoiceModal').modal('show');
                } else {
                    console.error('Failed to load invoice:', resp);
                    alert('Error loading invoice details. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error loading invoice:', error);
                alert('Error loading invoice details. Please try again.');
            }
        });
    }

    function downloadReceipt(invoice_id){
        // Open receipt in new window for printing/downloading
        window.open(_base_url_ + 'admin/invoices/print_invoice.php?id=' + invoice_id + '&type=receipt', '_blank');
    }

    // Print function removed - no longer needed
    function printInvoice_removed(invoice){
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

    function generateInvoiceHTML(invoice){
        var html = '<div class="invoice-preview">';
        html += '<div class="row mb-3">';
        html += '<div class="col-md-6">';
        html += '<h4>Invoice: ' + invoice.invoice_number + '</h4>';
        html += '<p><strong>Date:</strong> ' + new Date(invoice.generated_at).toLocaleDateString() + '</p>';
        html += '<p><strong>Due Date:</strong> ' + new Date(invoice.due_date).toLocaleDateString() + '</p>';
        html += '<p><strong>Transaction Type:</strong> ' + invoice.transaction_type.replace('_', ' ').toUpperCase() + '</p>';
        html += '</div>';
        html += '<div class="col-md-6 text-right">';
        html += '<p><strong>Status:</strong> <span class="badge badge-' + (invoice.payment_status == 'paid' ? 'success' : 'warning') + '">' + invoice.payment_status.toUpperCase() + '</span></p>';
        html += '<p><strong>Total Amount:</strong> ₱' + parseFloat(invoice.total_amount).toLocaleString() + '</p>';
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
});

function refreshInvoices(){
    loadInvoices();
    loadStats();
    alert_toast('Invoices refreshed successfully!', 'success');
}
</script>




