<?php
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : '2025-01-01';
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date("Y-m-d");
?>
<style>
table td, table th {
    padding: 3px !important;
    border: 1px solid #000;
}
@media print {
    #filter-form, .btn, hr:first-of-type { display: none !important; }
}
</style>
<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 class="card-title">Invoice Management Report</h5>
    </div>
    <div class="card-body">
        <form id="filter-form">
            <div class="row align-items-end">
                <div class="form-group col-md-3">
                    <label for="date_start">Date Start</label>
                    <input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo $date_start ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="date_end">Date End</label>
                    <input type="date" class="form-control form-control-sm" name="date_end" value="<?php echo $date_end ?>">
                </div>
                <div class="form-group col-md-1">
                    <button class="btn btn-flat btn-block btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                </div>
                <div class="form-group col-md-1">
                    <button class="btn btn-flat btn-block btn-success btn-sm" type="button" id="printBTN"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
        </form>
        <hr>
        <div id="printable">
            <div class="report-header" style="display:flex; justify-content: space-between; align-items: center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;">
                <div style="flex:0 0 auto; margin-right:20px;">
                    <img src="<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">
                </div>
                <div style="flex:1; text-align:center;">
                    <h3 style="margin:0; text-transform:uppercase; font-weight:bold;"><?php echo $_settings->info('name') ?></h3>
                    <p style="margin:0;">Invoice Management Report</p>
                    <p style="margin:0;">Date Between <?php echo $date_start ?> and <?php echo $date_end ?></p>
                </div>
                <div style="flex:0 0 auto; margin-left:20px;">
                    <img src="<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" style="width:100px; height:100px; object-fit:contain;">
                </div>
            </div>
            <table class="table table-bordered">
                <colgroup>
                    <col width="5%">
                    <col width="12%">
                    <col width="20%">
                    <col width="16%">
                    <col width="12%">
                    <col width="13%">
                    <col width="11%">
                    <col width="11%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Invoice No.</th>
                        <th>Customer</th>
                        <th>Transaction Type</th>
                        <th>Total Amount</th>
                        <th>Payment Status</th>
                        <th>Date Created</th>
                        <th>Receipt Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $where = "WHERE DATE(i.generated_at) BETWEEN '{$date_start}' AND '{$date_end}'";
                    $qry = $conn->query("SELECT i.*, c.lastname, c.firstname, c.middlename, c.email FROM invoices i INNER JOIN client_list c ON i.customer_id = c.id $where ORDER BY i.generated_at DESC");
                    while($row = $qry->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++ ?></td>
                        <td><strong><?php echo htmlspecialchars($row['invoice_number']) ?></strong></td>
                        <td><?php echo ucwords($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']) ?><br><small><?php echo htmlspecialchars($row['email']) ?></small></td>
                        <td><?php echo htmlspecialchars(strtoupper($row['transaction_type'])) ?></td>
                        <td class="text-right">₱<?php echo number_format($row['total_amount'],2) ?></td>
                        <td class="text-center">
                            <?php $ps = strtolower($row['payment_status']); ?>
                            <span class="badge badge-<?php echo $ps=='paid' ? 'success': ($ps=='late' ? 'danger' : ($ps=='partial' ? 'info':'warning')) ?>">
                            <?php echo ucwords($ps); ?></span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($row['generated_at'])) ?></td>
                        <td><?php echo !empty($row['payment_date']) ? date('Y-m-d', strtotime($row['payment_date'])) : '-' ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($qry->num_rows <= 0): ?>
                    <tr>
                        <td class="text-center" colspan="8">No Data...</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(function(){
    $('#filter-form').submit(function(e){
        e.preventDefault();
        location.href = "./?page=report/invoices&date_start="+$('[name="date_start"]').val()+"&date_end="+$('[name="date_end"]').val();
    });
    $('#printBTN').click(function(){
        var rep = $('#printable').clone();
        var ns = '<style>' +
            'body{margin:40px;font-size:14px;}' +
            'table{border-collapse:collapse;width:100%;}' +
            'table th, table td{border:1px solid #000;padding:5px;}' +
            '.text-center{text-align:center;}' +
            '.text-right{text-align:right;}' +
            '.report-header{display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;}' +
            '.report-header h3{text-transform:uppercase;font-weight:bold;margin:0;}' +
            '.report-header p{margin:0;}' +
            '@media print { #filter-form, #printBTN { display:none !important; } }' +
            '</style>';
        rep.prepend(ns);
        var nw = window.open('', '_blank');
        nw.document.write('<html><head><title>Print Invoice Management Report</title></head><body>' + rep.html() + '</body></html>');
        nw.document.close();
        nw.onload = function(){
            nw.focus();
            nw.print();
            setTimeout(function(){ nw.close(); }, 500);
        };
    });
});
</script>
