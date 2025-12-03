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

<style>
    .invoice-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .invoice-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
    }
    .badge-lg {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
    .invoice-preview .card {
        border: none;
        border-radius: 8px;
    }
    .invoice-preview .card-header {
        border-radius: 8px 8px 0 0;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    /* Receipt Design Styles */
    .receipt-container {
        background: #fff;
        padding: 30px;
        font-family: 'Arial', sans-serif;
        max-width: 800px;
        margin: 0 auto;
    }
    .receipt-header {
        text-align: center;
        border-bottom: 3px solid #28a745;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .receipt-header h2 {
        color: #28a745;
        font-weight: bold;
        margin: 0;
        font-size: 28px;
    }
    .receipt-header p {
        color: #666;
        margin: 5px 0;
        font-size: 14px;
    }
    .receipt-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }
    .receipt-info-left, .receipt-info-right {
        flex: 1;
        min-width: 250px;
    }
    .receipt-info-item {
        margin-bottom: 12px;
        display: flex;
        align-items: flex-start;
    }
    .receipt-info-label {
        font-weight: 600;
        color: #333;
        min-width: 140px;
        font-size: 14px;
    }
    .receipt-info-value {
        color: #555;
        font-size: 14px;
        flex: 1;
    }
    .receipt-amount-box {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        text-align: center;
        margin: 25px 0;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    .receipt-amount-box .amount-label {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 10px;
        font-weight: 500;
    }
    .receipt-amount-box .amount-value {
        font-size: 42px;
        font-weight: bold;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .receipt-details-card {
        background: #f8f9fa;
        border-left: 4px solid #28a745;
        padding: 20px;
        border-radius: 5px;
        margin: 20px 0;
    }
    .receipt-details-card h5 {
        color: #28a745;
        margin-bottom: 15px;
        font-weight: 600;
        font-size: 16px;
    }
    .receipt-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .receipt-detail-row:last-child {
        border-bottom: none;
    }
    .receipt-detail-label {
        font-weight: 600;
        color: #495057;
    }
    .receipt-detail-value {
        color: #212529;
        text-align: right;
    }
    .receipt-footer {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px dashed #dee2e6;
        text-align: center;
    }
    .receipt-footer p {
        color: #6c757d;
        font-size: 13px;
        margin: 5px 0;
    }
    .receipt-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .receipt-badge-success {
        background: #d4edda;
        color: #155724;
    }
    .receipt-icon {
        width: 50px;
        height: 50px;
        background: #28a745;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin-bottom: 15px;
    }
    @media print {
        .receipt-container {
            padding: 20px;
            box-shadow: none;
        }
        .modal-footer {
            display: none !important;
        }
    }
</style>
<div class="content py-5 mt-3">
    <div class="container">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary shadow-lg rounded invoice-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-file-invoice fa-2x"></i>
                                </div>
                                <div>
                                    <h3 class="mb-1 text-primary">My Invoices & Receipts</h3>
                                    <p class="text-muted mb-0">View your purchase invoices and payment receipts</p>
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
            <div class="col-md-4 mb-3">
                <div class="card card-outline card-info shadow invoice-card">
                    <div class="card-body text-center">
                        <div class="bg-info text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                        <h3 class="text-info mb-1" id="total_invoices">0</h3>
                        <p class="text-muted mb-0 font-weight-bold">Total Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-outline card-success shadow invoice-card">
                    <div class="card-body text-center">
                        <div class="bg-success text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h3 class="text-success mb-1" id="paid_invoices">0</h3>
                        <p class="text-muted mb-0 font-weight-bold">Paid Invoices</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-outline card-warning shadow invoice-card">
                    <div class="card-body text-center">
                        <div class="bg-warning text-white rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h3 class="text-warning mb-1" id="unpaid_invoices">0</h3>
                        <p class="text-muted mb-0 font-weight-bold">Unpaid Invoices</p>
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
                        <!-- <div class="card-tools">
                            <button class="btn btn-sm btn-primary" onclick="refreshInvoices()">
                                <i class="fa fa-refresh"></i> Refresh
                            </button>
                        </div> -->
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
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title"><i class="fas fa-file-invoice"></i> Invoice Details</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="invoice_details" style="max-height: 70vh; overflow-y: auto;">
                <!-- Invoice details will be loaded here -->
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-primary" onclick="downloadInvoice()">
                    <i class="fas fa-download"></i> Download Invoice
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View Receipt Modal -->
<div class="modal fade" id="viewReceiptModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title"><i class="fas fa-receipt"></i> Payment Receipt</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="receipt_details" style="max-height: 75vh; overflow-y: auto;">
                <!-- Receipt details will be loaded here -->
            </div>
            <div class="modal-footer bg-light">
                <!-- <button type="button" class="btn btn-success" onclick="printReceipt()">
                    <i class="fas fa-print"></i> Print Receipt
                </button> -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Logo URLs for printing - using same method as admin dashboard
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
        case 'genuine_oil_purchase':
        case 'oils_purchase':
        case 'oil_purchase':
            return 'Genuine Oil Purchase';
        default:
            var cleaned = type.toString().replace(/_/g,' ').trim();
            return cleaned.replace(/\b\w/g, function(letter){ return letter.toUpperCase(); });
    }
}

$(document).ready(function(){
    // Load initial data
    loadInvoices();
    loadStats();

    // View invoice
    $(document).on('click', '.view_invoice', function(){
        var invoice_id = $(this).data('id');
        viewInvoice(invoice_id);
    });

    // View receipt
    $(document).on('click', '.view_receipt', function(e){
        e.preventDefault();
        var invoice_id = $(this).data('id');
        $.ajax({
            url: _base_url_ + 'classes/Invoice.php?action=get_all_receipts&invoice_id=' + invoice_id,
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if(resp.status === 'success' && resp.data && resp.data.length > 0) {
                    var receipts = resp.data;
                    var html = '';
                    
                    // Display all receipts
                    if(receipts.length > 1) {
                        html += '<div class="alert alert-info m-3"><strong>' + receipts.length + ' Payment(s) Found</strong></div>';
                    }
                    
                    receipts.forEach(function(receipt, index) {
                        html += generateStyledReceiptHTML(receipt);
                        if(index < receipts.length - 1) {
                            html += '<hr class="my-4">';
                        }
                    });
                    
                    $('#receipt_details').html(html);
                    $('#viewReceiptModal').data('receipt-data', receipts);
                    $('#viewReceiptModal').modal('show');
                } else {
                    // Fallback to single receipt if all_receipts fails
                    $.ajax({
                        url: _base_url_ + 'classes/Invoice.php?action=get_receipt&invoice_id=' + invoice_id,
                        method: 'GET',
                        dataType: 'json',
                        success: function(resp) {
                            if(resp.status === 'success' && resp.data) {
                                var r = resp.data;
                                var html = generateStyledReceiptHTML(r);
                                $('#receipt_details').html(html);
                                $('#viewReceiptModal').data('receipt-data', r);
                                $('#viewReceiptModal').modal('show');
                            } else {
                                $('#receipt_details').html('<div class="alert alert-danger m-3">Receipt not found.</div>');
                                $('#viewReceiptModal').modal('show');
                            }
                        }
                    });
                }
            },
            error: function(){
                // Fallback to single receipt on error
                $.ajax({
                    url: _base_url_ + 'classes/Invoice.php?action=get_receipt&invoice_id=' + invoice_id,
                    method: 'GET',
                    dataType: 'json',
                    success: function(resp) {
                        if(resp.status === 'success' && resp.data) {
                            var r = resp.data;
                            var html = generateStyledReceiptHTML(r);
                            $('#receipt_details').html(html);
                            $('#viewReceiptModal').data('receipt-data', r);
                            $('#viewReceiptModal').modal('show');
                        } else {
                            $('#receipt_details').html('<div class="alert alert-danger m-3">Receipt not found.</div>');
                            $('#viewReceiptModal').modal('show');
                        }
                    }
                });
            }
        });
    });


    // Handle modal close events
    $('#viewInvoiceModal').on('hidden.bs.modal', function () {
        // Clear modal data when closed
        $(this).removeData('invoice-id');
        $('#invoice_details').empty();
    });

    $('#viewReceiptModal').on('hidden.bs.modal', function () {
        // Clear modal data when closed
        $(this).removeData('receipt-id');
        $('#receipt_details').empty();
    });

    // Ensure close button works properly
    $('#viewInvoiceModal .close, #viewInvoiceModal [data-dismiss="modal"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Close button clicked'); // Debug log
        $('#viewInvoiceModal').modal('hide');
    });

    $('#viewReceiptModal .close, #viewReceiptModal [data-dismiss="modal"]').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Close button clicked'); // Debug log
        $('#viewReceiptModal').modal('hide');
    });

    // Alternative close method using ESC key
    $(document).on('keydown', function(e) {
        if (e.keyCode === 27) { // ESC key
            $('#viewInvoiceModal').modal('hide');
            $('#viewReceiptModal').modal('hide');
        }
    });

    // Force modal to close when clicking outside
    $('#viewInvoiceModal').on('click', function(e) {
        if (e.target === this) {
            $(this).modal('hide');
        }
    });

    $('#viewReceiptModal').on('click', function(e) {
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

                        html += '<tr>';
                        html += '<td><strong>' + invoice.invoice_number + '</strong></td>';
                        html += '<td>' + new Date(invoice.generated_at).toLocaleDateString() + '</td>';
                        html += '<td>' + formatTransactionType(invoice.transaction_type) + '</td>';
                        html += '<td class="text-right">₱' + parseFloat(invoice.total_amount).toLocaleString() + '</td>';
                        html += '<td><span class="' + status_class + '">' + status_text + '</span></td>';
                        html += '<td class="text-center">';
                        html += '<button class="btn btn-sm btn-primary view_invoice" data-id="' + invoice.id + '" title="View Invoice"><i class="fas fa-eye"></i> View</button>';
                        if(parseInt(invoice.receipt_count || 0) > 0){
                            html += ' <button class="btn btn-sm btn-success view_receipt" data-id="' + invoice.id + '" title="View Receipt"><i class="fas fa-receipt"></i> Receipt</button>';
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
        start_loader();
        $.ajax({
            url: _base_url_ + 'classes/Invoice.php?action=get_invoice&invoice_id=' + invoice_id,
            method: 'GET',
            dataType: 'json',
            success: function(resp){
                end_loader();
                if(resp.status == 'success' && resp.data){
                    var invoice = resp.data;
                    var html = generateInvoiceHTML(invoice);
                    $('#invoice_details').html(html);
                    $('#viewInvoiceModal').data('invoice-id', invoice_id);
                    console.log('Invoice ID stored in modal:', $('#viewInvoiceModal').data('invoice-id')); // Debug log
                    $('#viewInvoiceModal').modal('show');
                } else {
                    console.error('Failed to load invoice:', resp);
                    var errorMsg = resp.msg || 'Error loading invoice details. Please try again.';
                    alert_toast(errorMsg, 'error');
                }
            },
            error: function(xhr, status, error) {
                end_loader();
                console.error('AJAX error loading invoice:', error);
                console.error('Response:', xhr.responseText);
                var errorMsg = 'Error loading invoice details. ';
                if(xhr.responseText){
                    try {
                        var resp = JSON.parse(xhr.responseText);
                        if(resp.msg) errorMsg += resp.msg;
                    } catch(e) {
                        errorMsg += 'Please try again.';
                    }
                } else {
                    errorMsg += 'Please try again.';
                }
                alert_toast(errorMsg, 'error');
            }
        });
    }

    function viewReceipt(invoice_id){
        console.log('Loading receipt for invoice:', invoice_id); // Debug log
        $.ajax({
            url: _base_url_ + 'classes/Invoice.php?action=get_receipt&invoice_id=' + invoice_id,
            method: 'GET',
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    var receipt = resp.data;
                    var html = generateReceiptHTML(receipt);
                    $('#receipt_details').html(html);
                    $('#viewReceiptModal').data('receipt-id', invoice_id);
                    console.log('Receipt ID stored in modal:', $('#viewReceiptModal').data('receipt-id')); // Debug log
                    $('#viewReceiptModal').modal('show');
                } else {
                    console.error('Failed to load receipt:', resp);
                    alert('Error loading receipt details. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error loading receipt:', error);
                alert('Error loading receipt details. Please try again.');
            }
        });
    }


    function generateInvoiceHTML(invoice){
        var html = '<div class="invoice-preview" style="font-family: Arial, sans-serif;">';
        html += '<div class="card mb-3 shadow-sm">';
        html += '<div class="card-header bg-primary text-white">';
        html += '<h4 class="mb-0"><i class="fas fa-file-invoice"></i> Invoice: ' + invoice.invoice_number + '</h4>';
        html += '</div>';
        html += '<div class="card-body">';
        html += '<div class="row mb-3">';
        html += '<div class="col-md-6">';
        // Customer Name
        if(invoice.firstname || invoice.lastname){
            var customerName = (invoice.firstname || '') + ' ' + (invoice.middlename || '') + ' ' + (invoice.lastname || '');
            customerName = customerName.trim().replace(/\s+/g, ' ');
            html += '<p class="mb-2"><strong><i class="fas fa-user"></i> Customer:</strong> ' + customerName + '</p>';
        }
        html += '<p class="mb-2"><strong><i class="fas fa-calendar"></i> Date:</strong> ' + new Date(invoice.generated_at).toLocaleDateString() + '</p>';
        html += '<p class="mb-2"><strong><i class="fas fa-calendar-check"></i> Due Date:</strong> ' + new Date(invoice.due_date).toLocaleDateString() + '</p>';
        html += '<p class="mb-2"><strong><i class="fas fa-receipt"></i> Transaction Type:</strong> ' + formatTransactionType(invoice.transaction_type) + '</p>';
        // Payment Type
        if(invoice.payment_type){
            html += '<p class="mb-2"><strong><i class="fas fa-credit-card"></i> Payment Type:</strong> <span class="badge badge-info">' + (invoice.payment_type || '').toUpperCase() + '</span></p>';
        }
        html += '</div>';
        var statusClass = (invoice.payment_status == 'paid') ? 'success' : (invoice.payment_status == 'late' ? 'danger' : 'warning');
        html += '<div class="col-md-6 text-right">';
        html += '<p class="mb-2"><strong>Status:</strong> <span class="badge badge-' + statusClass + ' badge-lg">' + (invoice.payment_status || '').toUpperCase() + '</span></p>';
        html += '<p class="mb-2"><strong><i class="fas fa-money-bill-wave"></i> Total Amount:</strong> <span class="text-primary font-weight-bold">₱' + parseFloat(invoice.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
        if(typeof invoice.balance_remaining !== 'undefined' && parseFloat(invoice.balance_remaining) > 0){
            html += '<p class="mb-2"><strong>Balance Remaining:</strong> <span class="text-danger">₱' + parseFloat(invoice.balance_remaining).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
        }
        // Interest amount if available
        if(typeof invoice.interest_amount !== 'undefined' && parseFloat(invoice.interest_amount) > 0){
            html += '<p class="mb-2"><strong><i class="fas fa-percent"></i> Interest:</strong> <span class="text-warning">₱' + parseFloat(invoice.interest_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
        }
        if(typeof invoice.late_fee_amount !== 'undefined' && parseFloat(invoice.late_fee_amount) > 0){
            html += '<p class="mb-2"><strong><i class="fas fa-exclamation-triangle"></i> Late Fee:</strong> <span class="text-danger">₱' + parseFloat(invoice.late_fee_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
        }
        if(typeof invoice.arrears_amount !== 'undefined' && parseFloat(invoice.arrears_amount) > 0){
            html += '<p class="mb-2"><strong><i class="fas fa-ban"></i> Arrears (Penalties):</strong> <span class="text-danger">₱' + parseFloat(invoice.arrears_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
        }
        if(typeof invoice.total_balance_due !== 'undefined' && parseFloat(invoice.total_balance_due) > 0){
            html += '<p class="mb-2"><strong><i class="fas fa-money-bill-wave"></i> Total Monthly Due:</strong> <span class="text-danger font-weight-bold">₱' + parseFloat(invoice.total_balance_due).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</span></p>';
        }
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        html += '<div class="card mb-3 shadow-sm">';
        html += '<div class="card-header bg-light">';
        html += '<h5 class="mb-0"><i class="fas fa-list"></i> Invoice Items</h5>';
        html += '</div>';
        html += '<div class="card-body p-0">';
        html += '<div class="table-responsive">';
        html += '<table class="table table-bordered table-hover mb-0">';
        html += '<thead class="thead-light"><tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th></tr></thead>';
        html += '<tbody>';
        $.each(invoice.items, function(index, item){
            // Calculate unit_price if it's zero or missing, using total_price and quantity
            var unit_price = parseFloat(item.unit_price) || 0;
            if(unit_price == 0 && parseFloat(item.quantity) > 0 && parseFloat(item.total_price) > 0){
                unit_price = parseFloat(item.total_price) / parseFloat(item.quantity);
            }
            html += '<tr>';
            html += '<td><strong>' + item.item_name + '</strong></td>';
            html += '<td class="text-center">' + item.quantity + '</td>';
            html += '<td class="text-right">₱' + unit_price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
            html += '<td class="text-right"><strong>₱' + parseFloat(item.total_price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
            html += '</tr>';
        });
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // Service History Section
        if(invoice.service_details && invoice.service_details.length){
            html += '<div class="card mb-3 shadow-sm">';
            html += '<div class="card-header bg-info text-white">';
            html += '<h5 class="mb-0"><i class="fas fa-tools"></i> Service History</h5>';
            html += '</div>';
            html += '<div class="card-body p-0">';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm table-bordered mb-0">';
            html += '<thead class="thead-light"><tr><th>Service</th><th class="text-right">Price</th></tr></thead><tbody>';
            invoice.service_details.forEach(function(service){
                var amount = parseFloat(service.amount || 0);
                html += '<tr>';
                html += '<td><strong>' + (service.name || 'Service') + '</strong></td>';
                html += '<td class="text-right"><strong>₱' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            html += '</div>';
            if(invoice.service_schedule && (invoice.service_schedule.preferred_date || invoice.service_schedule.preferred_time)){
                var scheduleParts = [];
                if(invoice.service_schedule.preferred_date){
                    var formattedDate = new Date(invoice.service_schedule.preferred_date).toLocaleDateString();
                    scheduleParts.push(formattedDate);
                }
                if(invoice.service_schedule.preferred_time){
                    scheduleParts.push(invoice.service_schedule.preferred_time);
                }
                if(scheduleParts.length){
                    html += '<div class="p-3 bg-light border-top">';
                    html += '<p class="mb-0"><strong><i class="fas fa-calendar-alt"></i> Preferred Schedule:</strong> ' + scheduleParts.join(' at ') + '</p>';
                    html += '</div>';
                }
            }
            html += '</div>';
            html += '</div>';
        }
        
        // Scheduled Payment Amounts (for installment plans)
        if(invoice.installment_schedule && invoice.installment_schedule.length > 0){
            html += '<div class="card mb-3 shadow-sm">';
            html += '<div class="card-header bg-warning text-dark">';
            html += '<h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Scheduled Payment Amounts</h5>';
            html += '</div>';
            html += '<div class="card-body p-0">';
            html += '<div class="table-responsive">';
            html += '<table class="table table-sm table-bordered mb-0">';
            html += '<thead class="thead-light"><tr><th>Payment Date</th><th class="text-right">Amount</th><th class="text-center">Status</th></tr></thead><tbody>';
            invoice.installment_schedule.forEach(function(schedule){
                var dueDate = schedule.due_date ? new Date(schedule.due_date).toLocaleDateString() : 'N/A';
                var amount = parseFloat(schedule.amount_due || schedule.amount || 0);
                var penalty = parseFloat(schedule.penalty_amount || 0);
                var lateFee = parseFloat(schedule.late_fee || 0);
                var totalDue = parseFloat(schedule.total_due_with_penalties || (amount + penalty + lateFee));
                var status = schedule.status || 'pending';
                var statusClass = status === 'paid' ? 'success' : (status === 'overdue' ? 'danger' : (status === 'partial' ? 'info' : 'warning'));
                html += '<tr>';
                html += '<td><strong>' + dueDate + '</strong></td>';
                html += '<td class="text-right">';
                html += '<div><strong>₱' + amount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></div>';
                if(penalty > 0 || lateFee > 0){
                    html += '<div class="small text-danger">';
                    if(penalty > 0) html += 'Penalty: ₱' + penalty.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ';
                    if(lateFee > 0) html += 'Late Fee: ₱' + lateFee.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ';
                    html += '</div>';
                    html += '<div class="small"><strong>Total: ₱' + totalDue.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></div>';
                }
                html += '</td>';
                html += '<td class="text-center"><span class="badge badge-' + statusClass + ' badge-lg">' + status.toUpperCase() + '</span></td>';
                html += '</tr>';
            });
            html += '</tbody></table>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        html += '<div class="card mb-3 shadow-sm">';
        html += '<div class="card-body">';
        html += '<div class="row">';
        html += '<div class="col-md-6">';
        if(invoice.pickup_location){
            html += '<p class="mb-2"><strong><i class="fas fa-map-marker-alt"></i> Pickup Location:</strong><br>' + invoice.pickup_location + '</p>';
        }
        if(invoice.payment_instructions){
            html += '<p class="mb-2"><strong><i class="fas fa-info-circle"></i> Payment Instructions:</strong><br>' + invoice.payment_instructions + '</p>';
        }
        html += '</div>';
        html += '<div class="col-md-6 text-right">';
        html += '<div class="border-top pt-3">';
        html += '<p class="mb-0"><strong class="h5 text-primary">Total Amount: ₱' + parseFloat(invoice.total_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></p>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
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

    function generateStyledReceiptHTML(receipt){
        var html = '<div class="receipt-container">';
        
        // Header
        html += '<div class="receipt-header">';
        html += '<div class="receipt-icon"><i class="fas fa-check-circle"></i></div>';
        html += '<h2>PAYMENT RECEIPT</h2>';
        html += '<p><strong>Star Honda Calamba</strong></p>';
        html += '<p>National Highway Brgy. Parian, Calamba City, Laguna</p>';
        html += '<p>Phone: 0948-235-3207 | Email: starhondacalamba55@gmail.com</p>';
        html += '</div>';
        
        // Receipt Number Badge
        html += '<div class="text-center mb-4">';
        html += '<span class="receipt-badge receipt-badge-success">Receipt No: ' + (receipt.receipt_number || 'N/A') + '</span>';
        html += '</div>';
        
        // Amount Box
        html += '<div class="receipt-amount-box">';
        html += '<div class="amount-label">Amount Paid</div>';
        html += '<div class="amount-value">₱' + parseFloat(receipt.amount_paid || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</div>';
        html += '</div>';
        
        // Receipt Details Card
        html += '<div class="receipt-details-card">';
        html += '<h5><i class="fas fa-info-circle"></i> Payment Details</h5>';
        
        if(receipt.invoice_number){
            html += '<div class="receipt-detail-row">';
            html += '<span class="receipt-detail-label"><i class="fas fa-file-invoice text-primary"></i> Invoice Number:</span>';
            html += '<span class="receipt-detail-value"><strong>' + receipt.invoice_number + '</strong></span>';
            html += '</div>';
        }
        
        if(receipt.issued_at){
            var issuedDate = new Date(receipt.issued_at);
            html += '<div class="receipt-detail-row">';
            html += '<span class="receipt-detail-label"><i class="fas fa-calendar text-info"></i> Date Paid:</span>';
            html += '<span class="receipt-detail-value">' + issuedDate.toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'}) + '</span>';
            html += '</div>';
            html += '<div class="receipt-detail-row">';
            html += '<span class="receipt-detail-label"><i class="fas fa-clock text-info"></i> Time:</span>';
            html += '<span class="receipt-detail-value">' + issuedDate.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'}) + '</span>';
            html += '</div>';
        }
        
        if(receipt.payment_method){
            html += '<div class="receipt-detail-row">';
            html += '<span class="receipt-detail-label"><i class="fas fa-credit-card text-success"></i> Payment Method:</span>';
            html += '<span class="receipt-detail-value"><strong>' + (receipt.payment_method || '').toUpperCase() + '</strong></span>';
            html += '</div>';
        }
        
        if(receipt.payment_reference){
            html += '<div class="receipt-detail-row">';
            html += '<span class="receipt-detail-label"><i class="fas fa-hashtag text-warning"></i> Reference:</span>';
            html += '<span class="receipt-detail-value">' + receipt.payment_reference + '</span>';
            html += '</div>';
        }
        
        if(receipt.staff_firstname || receipt.staff_lastname){
            var staffName = (receipt.staff_firstname || '') + ' ' + (receipt.staff_lastname || '');
            staffName = staffName.trim();
            html += '<div class="receipt-detail-row">';
            html += '<span class="receipt-detail-label"><i class="fas fa-user-check text-primary"></i> Received By:</span>';
            html += '<span class="receipt-detail-value">' + staffName + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
        // Loyalty discount information (display-only). Apply 2% discount for spare parts orders when customer has a loyalty indicator
        try{
            var txnType = (receipt.transaction_type || '').toString().toLowerCase();
            var isParts = txnType.indexOf('part') !== -1 || txnType.indexOf('motorcycle_parts') !== -1 || txnType.indexOf('motorcycle_parts_purchase') !== -1;
            // check common loyalty fields returned from client_list (flexible): loyalty_card, has_loyalty, membership_card, loyalty
            var hasLoyalty = false;
            if(receipt.loyalty_card || receipt.has_loyalty || receipt.loyalty || receipt.membership_card || receipt.membership){
                // treat non-empty strings and truthy values as having loyalty
                hasLoyalty = true;
            }
            if(isParts && hasLoyalty){
                var invoiceTotal = parseFloat(receipt.invoice_total_amount || receipt.amount_paid || 0);
                var discount = +(invoiceTotal * 0.02).toFixed(2);
                var discountedTotal = +(invoiceTotal - discount).toFixed(2);
                html += '<div class="receipt-details-card" style="background:#fff7ed;border-left-color:#ff8c00;">';
                html += '<h5 style="color:#b35b00;"><i class="fas fa-gift"></i> Loyalty Discount</h5>';
                html += '<p style="margin:0;">Customer has loyalty card — a 2% discount is applied to spare parts purchases.</p>';
                html += '<div class="receipt-detail-row" style="border:none;padding-top:10px;">';
                html += '<span class="receipt-detail-label">Discount (2%):</span>';
                html += '<span class="receipt-detail-value">-₱' + discount.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + '</span>';
                html += '</div>';
                html += '<div class="receipt-detail-row" style="border:none;">';
                html += '<span class="receipt-detail-label"><strong>Total after Discount:</strong></span>';
                html += '<span class="receipt-detail-value"><strong>₱' + discountedTotal.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + '</strong></span>';
                html += '</div>';
                html += '</div>';
            }
        }catch(e){ console.error(e); }
        
        // Acknowledgment Note
        if(receipt.acknowledgment_note){
            html += '<div class="receipt-details-card" style="background: #e7f3ff; border-left-color: #0066cc;">';
            html += '<h5 style="color: #0066cc;"><i class="fas fa-comment-dots"></i> Message</h5>';
            html += '<p style="color: #333; margin: 0; font-style: italic;">' + receipt.acknowledgment_note + '</p>';
            html += '</div>';
        }
        
        // Footer
        html += '<div class="receipt-footer">';
        html += '<p><strong>Thank you for your payment!</strong></p>';
        html += '<p>This is an official receipt. Please keep it for your records.</p>';
        html += '<p style="font-size: 11px; color: #999;">Generated on ' + new Date().toLocaleString() + '</p>';
        html += '</div>';
        
        html += '</div>';
        return html;
    }
    
    function generateReceiptHTML(receipt){
        // Use the styled version
        return generateStyledReceiptHTML(receipt);
    }
    
    function printReceipt(){
        var receiptData = $('#viewReceiptModal').data('receipt-data');
        if(!receiptData){
            alert_toast('Receipt data not found', 'error');
            return;
        }
        
        var printContent = generateStyledReceiptHTML(receiptData);
        var printStyles = '<style>' +
            'body{margin:0;padding:20px;font-family:Arial,sans-serif;background:#fff;}' +
            '.receipt-container{max-width:800px;margin:0 auto;background:#fff;padding:30px;}' +
            '.receipt-header{text-align:center;border-bottom:3px solid #28a745;padding-bottom:20px;margin-bottom:30px;}' +
            '.receipt-header h2{color:#28a745;font-weight:bold;margin:0;font-size:28px;}' +
            '.receipt-header p{color:#666;margin:5px 0;font-size:14px;}' +
            '.receipt-amount-box{background:linear-gradient(135deg,#28a745 0%,#20c997 100%);color:white;padding:25px;border-radius:10px;text-align:center;margin:25px 0;}' +
            '.receipt-amount-box .amount-label{font-size:16px;opacity:0.9;margin-bottom:10px;font-weight:500;}' +
            '.receipt-amount-box .amount-value{font-size:42px;font-weight:bold;margin:0;}' +
            '.receipt-details-card{background:#f8f9fa;border-left:4px solid #28a745;padding:20px;border-radius:5px;margin:20px 0;}' +
            '.receipt-details-card h5{color:#28a745;margin-bottom:15px;font-weight:600;font-size:16px;}' +
            '.receipt-detail-row{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #e9ecef;}' +
            '.receipt-detail-row:last-child{border-bottom:none;}' +
            '.receipt-detail-label{font-weight:600;color:#495057;}' +
            '.receipt-detail-value{color:#212529;text-align:right;}' +
            '.receipt-footer{margin-top:30px;padding-top:20px;border-top:2px dashed #dee2e6;text-align:center;}' +
            '.receipt-footer p{color:#6c757d;font-size:13px;margin:5px 0;}' +
            '.receipt-badge{display:inline-block;padding:6px 12px;border-radius:20px;font-size:12px;font-weight:600;text-transform:uppercase;background:#d4edda;color:#155724;}' +
            '.receipt-icon{width:50px;height:50px;background:#28a745;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:white;font-size:24px;margin-bottom:15px;}' +
            '@media print{@page{margin:0.5cm;}body{margin:0;padding:10px;}}' +
        '</style>';
        
        var printWindow = window.open('', '_blank');
        if(!printWindow){
            alert_toast('Please allow popups to print receipt', 'warning');
            return;
        }
        
        printWindow.document.write('<html><head><title>Receipt ' + (receiptData.receipt_number || '') + '</title>' + printStyles + '</head><body>' + printContent + '</body></html>');
        printWindow.document.close();
        
        printWindow.onload = function(){
            printWindow.focus();
            printWindow.print();
            setTimeout(function(){ printWindow.close(); }, 500);
        };
        
        setTimeout(function(){
            if(printWindow && !printWindow.closed){
                printWindow.focus();
                printWindow.print();
            }
        }, 1000);
    }
});

function refreshInvoices(){
    loadInvoices();
    loadStats();
    alert_toast('Invoices refreshed successfully!', 'success');
}

// Download/Print invoice - defined globally so it can be called from onclick
function downloadInvoice(){
    var invoice_id = $('#viewInvoiceModal').data('invoice-id');
    if(invoice_id && invoice_id !== 'undefined'){
        // Get invoice details for printing
        $.ajax({
            url: _base_url_ + 'classes/Invoice.php?action=get_invoice&invoice_id=' + invoice_id,
            method: 'GET',
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success' && resp.data){
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
        alert_toast('Invoice ID not found. Please view the invoice first.', 'error');
    }
}

// Print invoice function - defined globally
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
    if(!printWindow){
        alert_toast('Please allow popups to print invoice', 'warning');
        return;
    }
    
    printWindow.document.write('<html><head><title>Invoice ' + invoice.invoice_number + '</title>' + printStyles + '</head><body>' + printContent + '</body></html>');
    printWindow.document.close();
    
    // Wait until content is fully loaded before printing - using same method as admin
    printWindow.onload = function(){
        printWindow.focus();
        printWindow.print();
        setTimeout(function(){ printWindow.close(); }, 500);
    };
    
    // Fallback if onload doesn't fire
    setTimeout(function(){
        if(printWindow && !printWindow.closed){
            printWindow.focus();
            printWindow.print();
        }
    }, 1000);
}

// Generate print invoice HTML - defined globally
function generatePrintInvoiceHTML(invoice){
    var html = '<div class="invoice-container">';
    
    // Header with dual logos - matching admin invoice style
    html += '<div class="invoice-header" style="display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #000;padding-bottom:15px;margin-bottom:20px;">';
    html += '<!-- Main Logo on the left -->';
    html += '<div style="flex:0 0 auto; margin-right:20px;">';
    html += '<img src="' + mainLogoUrl + '" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">';
    html += '</div>';
    
    html += '<!-- Centered Company Info -->';
    html += '<div class="company-info" style="text-align:center;flex:1;margin:0 20px;">';
    html += '<h2 style="text-transform:uppercase;font-weight:bold;margin:0;color:#333;">Star Honda Calamba</h2>';
    html += '<p style="margin:5px 0;color:#666;">National Highway Brgy. Parian, Calamba City, Laguna</p>';
    html += '<p style="margin:5px 0;color:#666;">Phone: 0948-235-3207 | Email: starhondacalamba55@gmail.com</p>';
    html += '<h3 style="margin:10px 0 0 0;font-size:24px;font-weight:bold;">INVOICE</h3>';
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
    var customerName = '';
    if(invoice.firstname || invoice.lastname){
        customerName = (invoice.firstname || '') + ' ' + (invoice.middlename || '') + ' ' + (invoice.lastname || '');
        customerName = customerName.trim().replace(/\s+/g, ' ');
    }
    html += '<p><strong>' + customerName + '</strong></p>';
    if(invoice.email) html += '<p>' + invoice.email + '</p>';
    if(invoice.contact) html += '<p>' + invoice.contact + '</p>';
    html += '</div>';
    html += '<div class="invoice-info">';
    html += '<h4>Invoice Details:</h4>';
    html += '<p><strong>Invoice No:</strong> ' + invoice.invoice_number + '</p>';
    html += '<p><strong>Date:</strong> ' + new Date(invoice.generated_at).toLocaleDateString() + '</p>';
    html += '<p><strong>Due Date:</strong> ' + new Date(invoice.due_date).toLocaleDateString() + '</p>';
    html += '<p><strong>Transaction Type:</strong> ' + formatTransactionType(invoice.transaction_type) + '</p>';
    if(invoice.payment_type){
        html += '<p><strong>Payment Type:</strong> ' + (invoice.payment_type || '').toUpperCase() + '</p>';
    }
    html += '</div>';
    html += '</div>';
    
    // Items Table
    html += '<table class="items-table">';
    html += '<thead><tr><th>Item</th><th class="text-center">Qty</th><th class="text-right">Unit Price</th><th class="text-right">Total</th></tr></thead>';
    html += '<tbody>';
    if(invoice.items && invoice.items.length > 0){
        for(var i = 0; i < invoice.items.length; i++){
            var item = invoice.items[i];
            var unit_price = parseFloat(item.unit_price) || 0;
            if(unit_price == 0 && parseFloat(item.quantity) > 0 && parseFloat(item.total_price) > 0){
                unit_price = parseFloat(item.total_price) / parseFloat(item.quantity);
            }
            html += '<tr>';
            html += '<td>' + item.item_name + '</td>';
            html += '<td class="text-center">' + item.quantity + '</td>';
            html += '<td class="text-right">₱' + unit_price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
            html += '<td class="text-right">₱' + parseFloat(item.total_price).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td>';
            html += '</tr>';
        }
    }
    html += '</tbody>';
    html += '</table>';
    
    // Totals
    html += '<div class="totals">';
    html += '<table class="totals-table">';
    html += '<tr><td>Subtotal:</td><td class="text-right">₱' + parseFloat(invoice.subtotal).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td></tr>';
    // Loyalty discount for spare parts orders (display-only) - 2%
    try{
        var txnTypeInv = (invoice.transaction_type || '').toString().toLowerCase();
        var isInvParts = txnTypeInv.indexOf('part') !== -1 || txnTypeInv.indexOf('motorcycle_parts') !== -1;
        var invHasLoyalty = false;
        if(invoice.loyalty_card || invoice.has_loyalty || invoice.loyalty || invoice.membership_card || invoice.membership){
            invHasLoyalty = true;
        }
        var loyaltyDiscount = 0;
        if(isInvParts && invHasLoyalty){
            var baseTotal = parseFloat(invoice.total_amount || invoice.subtotal || 0);
            loyaltyDiscount = +(baseTotal * 0.02).toFixed(2);
            html += '<tr><td>Loyalty Discount (2%):</td><td class="text-right">-₱' + loyaltyDiscount.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + '</td></tr>';
        }
    }catch(e){ console.error(e); }
    // VAT removed - no longer displayed
    if(typeof invoice.interest_amount !== 'undefined' && parseFloat(invoice.interest_amount) > 0){
        html += '<tr><td>Interest:</td><td class="text-right">₱' + parseFloat(invoice.interest_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td></tr>';
    }
    if(typeof invoice.late_fee_amount !== 'undefined' && parseFloat(invoice.late_fee_amount) > 0){
        html += '<tr><td>Late Fee:</td><td class="text-right">₱' + parseFloat(invoice.late_fee_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td></tr>';
    }
    if(typeof invoice.arrears_amount !== 'undefined' && parseFloat(invoice.arrears_amount) > 0){
        html += '<tr><td>Arrears (Penalties):</td><td class="text-right text-danger">₱' + parseFloat(invoice.arrears_amount).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td></tr>';
    }
    var displayTotal = parseFloat(invoice.total_amount || 0) - (loyaltyDiscount || 0);
    html += '<tr class="total-row"><td><strong>Total Amount:</strong></td><td class="text-right"><strong>₱' + displayTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td></tr>';
    if(typeof invoice.balance_remaining !== 'undefined' && parseFloat(invoice.balance_remaining) > 0){
        html += '<tr><td>Balance Remaining:</td><td class="text-right">₱' + parseFloat(invoice.balance_remaining).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</td></tr>';
    }
    if(typeof invoice.total_balance_due !== 'undefined' && parseFloat(invoice.total_balance_due) > 0){
        html += '<tr class="total-row"><td><strong>Monthly Due (incl. charges):</strong></td><td class="text-right"><strong class="text-danger">₱' + parseFloat(invoice.total_balance_due).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '</strong></td></tr>';
    }
    html += '</table>';
    html += '</div>';
    
    // Payment Status
    var statusClass = (invoice.payment_status == 'paid') ? 'paid' : 'unpaid';
    html += '<div class="payment-status ' + statusClass + '">';
    if(invoice.payment_status == 'paid'){
        html += '✅ PAID - Thank you for your payment!';
    } else {
        html += '⏳ PENDING PAYMENT - Payment must be completed in-store';
    }
    html += '</div>';
    
    // Footer
    html += '<div class="footer-info">';
    html += '<p style="text-align: center; margin-top: 20px; font-size: 11px; color: #999;">';
    html += 'This invoice was generated on ' + new Date(invoice.generated_at).toLocaleString();
    if(invoice.staff_firstname){
        html += ' by ' + invoice.staff_firstname + ' ' + invoice.staff_lastname;
    }
    html += '</p>';
    html += '</div>';
    
    html += '</div>';
    return html;
}

function refreshInvoices(){
    loadInvoices();
    loadStats();
    alert_toast('Invoices refreshed successfully!', 'success');
}

// Download/Print invoice - defined globally so it can be called from onclick
function downloadInvoice(){
    var invoice_id = $('#viewInvoiceModal').data('invoice-id');
    if(invoice_id && invoice_id !== 'undefined'){
        // Get invoice details for printing
        $.ajax({
            url: _base_url_ + 'classes/Invoice.php?action=get_invoice&invoice_id=' + invoice_id,
            method: 'GET',
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success' && resp.data){
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
        alert_toast('Invoice ID not found. Please view the invoice first.', 'error');
    }
}
</script>




