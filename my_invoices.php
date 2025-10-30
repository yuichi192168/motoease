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
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- View Receipt Modal -->
<div class="modal fade" id="viewReceiptModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Receipt Details</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="receipt_details">
                <!-- Receipt details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

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
            url: _base_url_ + 'classes/Invoice.php?action=get_receipt&invoice_id=' + invoice_id,
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if(resp.status === 'success' && resp.data) {
                    var r = resp.data;
                    var html = '<div class="receipt-preview">'
                        + '<h5>Receipt No.: <strong>' + r.receipt_number + '</strong></h5>'
                        + '<p><strong>Amount Paid:</strong> ₱' + parseFloat(r.amount_paid).toLocaleString() + '</p>'
                        + '<p><strong>Payment Method:</strong> ' + (r.payment_method || '').toUpperCase() + '</p>'
                        + (r.issued_at ? '<p><strong>Date Paid:</strong> ' + new Date(r.issued_at).toLocaleDateString() + '</p>' : '')
                        + (r.staff_firstname ? '<p><strong>Received By:</strong> ' + r.staff_firstname + ' ' + r.staff_lastname + '</p>' : '')
                        + (r.payment_reference ? '<p><strong>Reference:</strong> ' + r.payment_reference + '</p>' : '')
                        + (r.acknowledgment_note ? '<p><strong>Acknowledgment Note:</strong><br>' + r.acknowledgment_note + '</p>' : '')
                        + '</div>';
                    $('#receipt_details').html(html);
                    $('#viewReceiptModal').modal('show');
                } else {
                    $('#receipt_details').html('<div class="alert alert-danger">Receipt not found.</div>');
                    $('#viewReceiptModal').modal('show');
                }
            },
            error: function(){
                $('#receipt_details').html('<div class="alert alert-danger">Error loading receipt details. Please try again.</div>');
                $('#viewReceiptModal').modal('show');
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
                        html += '<td>' + invoice.transaction_type.replace('_', ' ').toUpperCase() + '</td>';
                        html += '<td class="text-right">₱' + parseFloat(invoice.total_amount).toLocaleString() + '</td>';
                        html += '<td><span class="' + status_class + '">' + status_text + '</span></td>';
                        html += '<td>';
                        html += '<button class="btn btn-sm btn-primary view_invoice" data-id="' + invoice.id + '">View Invoice</button>';
                        if(invoice.receipt_date){
                            html += ' <button class="btn btn-sm btn-success view_receipt" data-id="' + invoice.id + '">View Receipt</button>';
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
        var html = '<div class="invoice-preview">';
        html += '<div class="row mb-3">';
        html += '<div class="col-md-6">';
        html += '<h4>Invoice: ' + invoice.invoice_number + '</h4>';
        html += '<p><strong>Date:</strong> ' + new Date(invoice.generated_at).toLocaleDateString() + '</p>';
        html += '<p><strong>Due Date:</strong> ' + new Date(invoice.due_date).toLocaleDateString() + '</p>';
        html += '<p><strong>Transaction Type:</strong> ' + invoice.transaction_type.replace('_', ' ').toUpperCase() + '</p>';
        html += '</div>';
        var statusClass = (invoice.payment_status == 'paid') ? 'success' : (invoice.payment_status == 'late' ? 'danger' : 'warning');
        html += '<div class="col-md-6 text-right">';
        html += '<p><strong>Status:</strong> <span class="badge badge-' + statusClass + '">' + (invoice.payment_status || '').toUpperCase() + '</span></p>';
        html += '<p><strong>Total Amount:</strong> ₱' + parseFloat(invoice.total_amount).toLocaleString() + '</p>';
        if(typeof invoice.balance_remaining !== 'undefined'){
            html += '<p><strong>Balance Remaining:</strong> ₱' + parseFloat(invoice.balance_remaining).toLocaleString() + '</p>';
        }
        if(typeof invoice.late_fee_amount !== 'undefined' && parseFloat(invoice.late_fee_amount) > 0){
            html += '<p><strong>Late Fee:</strong> ₱' + parseFloat(invoice.late_fee_amount).toLocaleString() + '</p>';
        }
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

    function generateReceiptHTML(receipt){
        var html = '<div class="receipt-preview">';
        html += '<div class="row mb-3">';
        html += '<div class="col-md-6">';
        html += '<h4>Receipt: ' + receipt.receipt_number + '</h4>';
        html += '<p><strong>Date:</strong> ' + new Date(receipt.issued_at).toLocaleDateString() + '</p>';
        html += '<p><strong>Invoice No:</strong> ' + receipt.invoice_number + '</p>';
        html += '<p><strong>Amount Paid:</strong> ₱' + parseFloat(receipt.amount_paid).toLocaleString() + '</p>';
        html += '<p><strong>Payment Method:</strong> ' + receipt.payment_method.toUpperCase() + '</p>';
        html += '<p><strong>Issued By:</strong> ' + receipt.issued_by + '</p>';
        html += '</div>';
        html += '<div class="col-md-6 text-right">';
        html += '<p><strong>Total Amount:</strong> ₱' + parseFloat(receipt.total_amount).toLocaleString() + '</p>';
        html += '</div>';
        html += '</div>';

        html += '<div class="table-responsive">';
        html += '<table class="table table-bordered">';
        html += '<thead><tr><th>Item</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>';
        html += '<tbody>';
        $.each(receipt.items, function(index, item){
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




