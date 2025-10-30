<style>
    table td, table th {
        padding: 3px !important;
        border: 1px solid #000;
    }
    /* Hide filter form & buttons when printing */
    @media print {
        #filter-form, .btn, hr:first-of-type {
            display: none !important;
        }
    }
</style>

<?php 
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] :  date("Y-m-d",strtotime(date("Y-m-d")." -7 days")) ;
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] :  date("Y-m-d") ;
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 class="card-title">Orders Report</h5>
    </div>
    <div class="card-body">
        <form id="filter-form">
            <div class="row align-items-end">
                <div class="form-group col-md-3">
                    <label for="date_start">Date Start</label>
                    <input type="date" class="form-control form-control-sm" name="date_start" value="<?php echo date("Y-m-d",strtotime($date_start)) ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="date_end">Date End</label>
                    <input type="date" class="form-control form-control-sm" name="date_end" value="<?php echo date("Y-m-d",strtotime($date_end)) ?>">
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
        <!-- Main Logo on the left -->
        <div style="flex:0 0 auto; margin-right:20px;">
            <img src="<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">
        </div>

        <!-- Centered Organization Name -->
        <div style="flex:1; text-align:center;">
            <h3 style="margin:0; text-transform:uppercase; font-weight:bold;"><?php echo $_settings->info('name') ?></h3>
            <p style="margin:0;">Order Report</p>
            <p style="margin:0;">Date Between <?php echo $date_start ?> and <?php echo $date_end ?></p>
        </div>

        <!-- Secondary Logo on the right -->
        <div style="flex:0 0 auto; margin-left:20px;">
            <img src="<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" style="width:100px; height:100px; object-fit:contain;">
        </div>
    </div>

    <table class="table table-bordered">
        <colgroup>
            <col width="5%">
            <col width="20%">
            <col width="20%">
            <col width="20%">
            <col width="15%">
            <col width="20%">
        </colgroup>
        <thead>
            <tr>
                <th>#</th>
                <th>Date Time</th>
                <th>Ref. Code</th>
                <th>Client Name</th>
                <th>Total Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1;
                $mechanic = $conn->query("SELECT * FROM mechanics_list");
                $result = $mechanic->fetch_all(MYSQLI_ASSOC);
                $mech_arr = array_column($result,'name','id');
                $where = "where date(o.date_created) between '{$date_start}' and '{$date_end}'";
                $qry = $conn->query("SELECT o.*,CONCAT(c.lastname,', ',c.firstname,' ',c.middlename) as fullname from order_list o inner join client_list c on o.client_id = c.id {$where} order by unix_timestamp(o.date_created) desc");
                while($row = $qry->fetch_assoc()):
            ?>
            <tr>
                <td class="text-center"><?php echo $i++ ?></td>
                <td><?php echo $row['date_created'] ?></td>
                <td><?php echo $row['ref_code'] ?></td>
                <td><?php echo $row['fullname'] ?></td>
                <td class="text-right"><?= number_format($row['total_amount'],2) ?></td>
                <td class='text-center'>
                    <?php if($row['status'] == 0): ?>
                        <span class="badge badge-secondary px-3 rounded-pill">Pending</span>
                    <?php elseif($row['status'] == 1): ?>
                        <span class="badge badge-primary px-3 rounded-pill">Approved Order</span>
                    <?php elseif($row['status'] == 2): ?>
                        <span class="badge badge-success px-3 rounded-pill">For Delivery</span>
                    <?php elseif($row['status'] == 3): ?>
                        <span class="badge badge-warning px-3 rounded-pill">On the Way</span>
                    <?php elseif($row['status'] == 4): ?>
                        <span class="badge badge-default bg-gradient-teal px-3 rounded-pill">Delivered</span>
                    <?php elseif($row['status'] == 6): ?>
                        <span class="badge badge-success px-3 rounded-pill">Claimed</span>
                    <?php else: ?>
                        <span class="badge badge-danger px-3 rounded-pill">Cancelled</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if($qry->num_rows <= 0): ?>
            <tr>
                <td class="text-center" colspan="6">No Data...</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(function(){
    $('#filter-form').submit(function(e){
        e.preventDefault();
        location.href = "./?page=report/orders&date_start="+$('[name="date_start"]').val()+"&date_end="+$('[name="date_end"]').val();
    });

    $('#printBTN').click(function(){
        // Clone printable content
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

        // Open new window
        var nw = window.open('', '_blank');
        nw.document.write('<html><head><title>Print Report</title></head><body>' + rep.html() + '</body></html>');
        nw.document.close();

        // Wait until content is fully loaded before printing
        nw.onload = function(){
            nw.focus();
            nw.print();
            setTimeout(function(){ nw.close(); }, 500);
        };
    });
});
</script>
