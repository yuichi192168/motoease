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
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] :  '2025-01-01' ;
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] :  date("Y-m-d");
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h5 class="card-title">Service Requests Report</h5>
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
            <!-- Legal-document style header with dual logos -->
            <div style="display:flex; justify-content: space-between; align-items: center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;">
                <!-- Main Logo on the left -->
                <div style="flex:0 0 auto; margin-right:20px;">
                    <img src="<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">
                </div>

                <!-- Centered Organization Name -->
                <div style="flex:1; text-align:center;">
                    <h2 class="m-0" style="text-transform:uppercase; font-weight:bold;"><?php echo $_settings->info('name') ?></h2>
                    <h4 class="m-0"><b>Service Requests Report</b></h4>
                    <p class="m-0">Date Between <?php echo $date_start ?> and <?php echo $date_end ?></p>
                </div>

                <!-- Secondary Logo on the right -->
                <div style="flex:0 0 auto; margin-left:20px;">
                    <img src="<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" style="width:100px; height:100px; object-fit:contain;">
                </div>
            </div>

            <table class="table table-bordered">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Time</th>
                        <th>Owner Name</th>
                        <th>Vehicle Name</th>
                        <th>Vehicle Reg. No.</th>
                        <th>Assigned To</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $mechanic = $conn->query("SELECT * FROM mechanics_list");
                    $result = $mechanic->fetch_all(MYSQLI_ASSOC);
                    $mech_arr = array_column($result,'name','id');
                    $where = "where date(s.date_created) between '{$date_start}' and '{$date_end}'";
                    $qry = $conn->query("SELECT s.*,CONCAT(c.lastname,', ',c.firstname,' ',c.middlename) as fullname from service_requests s inner join client_list c on s.client_id = c.id {$where} order by unix_timestamp(s.date_created) desc");

                    while($row = $qry->fetch_assoc()):
                        $meta = $conn->query("SELECT * FROM request_meta where request_id = '{$row['id']}'");
                        while($mrow = $meta->fetch_assoc()){
                            $row[$mrow['meta_field']] =$mrow['meta_value'];
                        }
                        $services  = $conn->query("SELECT * FROM service_list where id in ({$row['service_id']}) ");
                        while($srow = $services->fetch_assoc()):
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++ ?></td>
                        <td><?php echo $row['date_created'] ?></td>
                        <td><?php echo $row['fullname'] ?></td>
                        <td><?php echo $row['vehicle_name'] ?></td>
                        <td><?php echo $row['vehicle_registration_number'] ?></td>
                        <td><?php echo !empty($row['mechanic_id']) && isset($mech_arr[$row['mechanic_id']]) ? $mech_arr[$row['mechanic_id']] : "N/A" ?></td>
                        <td><?php echo $srow['service'] ?></td>
                        <td class='text-center'>
                            <?php if($row['status'] == 1): ?>
                                <span class="badge badge-primary rounded-pill px-3">Confirmed</span>
                            <?php elseif($row['status'] == 2): ?>
                                <span class="badge badge-warning rounded-pill px-3">On-progress</span>
                            <?php elseif($row['status'] == 3): ?>
                                <span class="badge badge-success rounded-pill px-3">Done</span>
                            <?php elseif($row['status'] == 4): ?>
                                <span class="badge badge-danger rounded-pill px-3">Cancelled</span>
                            <?php else: ?>
                                <span class="badge badge-secondary rounded-pill px-3">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; endwhile; ?>
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

<noscript>
    <style>
        .m-0{ margin:0; }
        .text-center{ text-align:center; }
        .text-right{ text-align:right; }
        .table{ border-collapse:collapse; width: 100%; }
        .table tr,.table td,.table th{ border:1px solid gray; padding:3px; }
    </style>
</noscript>

<script>
$(function(){
    $('#filter-form').submit(function(e){
        e.preventDefault();
        location.href = "./?page=report/service_requests&date_start="+$('[name="date_start"]').val()+"&date_end="+$('[name="date_end"]').val();
    });

    $('#printBTN').click(function(){
        // Clone the printable content
        var rep = $('#printable').clone();

        // Get noscript styles
        var ns = $('noscript').html();

        // Prepend styles directly to the cloned content
        rep.prepend('<style>'+
            'body{margin:40px; font-size:14px;}' +
            'table{border-collapse:collapse; width:100%;}' +
            'table th, table td{border:1px solid #000; padding:5px;}' +
            '.text-center{text-align:center;}' +
            '.text-right{text-align:right;}' +
            '.report-header{display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;}' +
            '.report-header h2, .report-header h4{text-transform:uppercase; margin:0;}' +
            '.report-header p{margin:0;}' +
            '@media print { #filter-form, #printBTN { display:none !important; } }' +
            ns +
        '</style>');

        // Open new window for printing
        var nw = window.open('', '_blank');
        nw.document.write('<html><head><title>Print Report</title></head><body>' + rep.html() + '</body></html>');
        nw.document.close();

        // Wait until content fully loads before printing
        nw.onload = function(){
            nw.focus();
            nw.print();
            setTimeout(function(){ nw.close(); }, 500); // Close after print
        };
    });
});
</script>
