<?php 
require_once('../../config.php');
?>
<html>
<head>
    <title>Customer Accounts Report</title>
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/adminlte.min.css">
    <style>
        body { background: #fff; }
        .table th, .table td { padding: 6px 8px; border: 1px solid #000; }
        .totals { margin-top: 10px; }
        .report-header{display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #000; padding-bottom:10px; margin-bottom:15px;}
        .report-header h3{ text-transform:uppercase; font-weight:bold; margin:0; }
        .report-header p{ margin:0; }
        @media print { .no-print { display: none !important; } }
    </style>
    <script>
        function validate_image(src){ return src; }
    </script>
    
</head>
<body>
<div class="container-fluid mt-3">
    <div class="no-print text-right mb-2">
        <button class="btn btn-primary btn-sm" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
        <button class="btn btn-default btn-sm" onclick="window.close()">Close</button>
    </div>
    <div class="report-header">
        <div style="flex:0 0 auto; margin-right:20px;">
            <img src="<?php echo validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" style="width:100px; height:100px; object-fit:contain;">
        </div>
        <div style="flex:1; text-align:center;">
            <h3><?php echo $_settings->info('name') ?></h3>
            <p>Customer Account Balances</p>
            <p>Generated: <?php echo date('Y-m-d H:i'); ?></p>
        </div>
        <div style="flex:0 0 auto; margin-left:20px;">
            <img src="<?php echo validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" style="width:100px; height:100px; object-fit:contain;">
        </div>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th class="text-right">Total Balance</th>
                <th class="text-right">Paid Amount</th>
                <th class="text-right">Unpaid Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $i = 1; 
            $total = 0; $paid = 0; $unpaid = 0;
            $qry = $conn->query("SELECT c.*, 
                                    COALESCE(SUM(o.total_amount), 0) as total_balance,
                                    COALESCE(SUM(CASE WHEN o.status IN (4,6) THEN o.total_amount ELSE 0 END), 0) as paid_amount,
                                    COALESCE(SUM(CASE WHEN o.status IN (0,1,2,3) THEN o.total_amount ELSE 0 END), 0) as unpaid_amount
                                  FROM `client_list` c 
                                  LEFT JOIN order_list o ON c.id = o.client_id 
                                  WHERE c.delete_flag = 0 
                                  GROUP BY c.id 
                                  ORDER BY c.lastname, c.firstname");
            while($row = $qry->fetch_assoc()):
                $total += (float)$row['total_balance'];
                $paid += (float)$row['paid_amount'];
                $unpaid += (float)$row['unpaid_amount'];
            ?>
            <tr>
                <td class="text-center"><?php echo $i++; ?></td>
                <td><?php echo ucwords($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']) ?></td>
                <td class="text-right">₱<?php echo number_format($row['total_balance'], 2) ?></td>
                <td class="text-right text-success">₱<?php echo number_format($row['paid_amount'], 2) ?></td>
                <td class="text-right text-danger">₱<?php echo number_format($row['unpaid_amount'], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="totals">
        <strong>Totals:</strong>
        <span class="ml-3">Total Balance: ₱<?php echo number_format($total,2) ?></span>
        <span class="ml-3 text-success">Paid: ₱<?php echo number_format($paid,2) ?></span>
        <span class="ml-3 text-danger">Unpaid: ₱<?php echo number_format($unpaid,2) ?></span>
    </div>
</div>
</body>
</html>


