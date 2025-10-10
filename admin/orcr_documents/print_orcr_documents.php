<?php
require_once('../../config.php');

// Get date filters if provided
$date_start = isset($_GET['date_start']) ? $_GET['date_start'] : date("Y-m-d", strtotime("-30 days"));
$date_end = isset($_GET['date_end']) ? $_GET['date_end'] : date("Y-m-d");
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build where clause
$where_conditions = ["d.status != 'expired'"];
if (!empty($date_start) && !empty($date_end)) {
    $where_conditions[] = "DATE(d.date_created) BETWEEN '{$date_start}' AND '{$date_end}'";
}
if (!empty($status_filter)) {
    $where_conditions[] = "d.status = '{$status_filter}'";
}
$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Get documents data
$qry = $conn->query("SELECT d.*, 
                    CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) as customer_name,
                    c.email, c.contact
                    FROM `or_cr_documents` d 
                    INNER JOIN client_list c ON d.client_id = c.id 
                    {$where_clause}
                    ORDER BY d.date_created DESC");

// Get statistics
$stats = [
    'total' => $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status != 'expired'")->fetch_assoc()['total'],
    'released' => $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'released' AND status != 'expired'")->fetch_assoc()['total'],
    'pending' => $conn->query("SELECT COUNT(*) as total FROM or_cr_documents WHERE status = 'pending' AND status != 'expired'")->fetch_assoc()['total']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OR/CR Documents Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
            font-size: 12px;
        }
        .report-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
        }
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .header-center {
            flex: 1;
            text-align: center;
        }
        .header-center h2 {
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
            color: #333;
        }
        .header-center h4 {
            margin: 5px 0;
            color: #666;
        }
        .header-center p {
            margin: 2px 0;
            color: #888;
            font-size: 11px;
        }
        .stats-section {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        .report-info {
            margin: 15px 0;
            padding: 10px;
            background-color: #e9ecef;
            border-left: 4px solid #007bff;
        }
        .report-info p {
            margin: 2px 0;
            font-size: 11px;
        }
        .documents-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .documents-table th, .documents-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        .documents-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .documents-table .text-center {
            text-align: center;
        }
        .status-badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-released {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
        }
        .doc-type-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .doc-type-or {
            background-color: #cce5ff;
            color: #004085;
        }
        .doc-type-cr {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .footer-info {
            margin-top: 30px;
            padding: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .report-container { border: none; }
        }
    </style>
</head>
<body>
    <div class="report-container">
        <!-- Header with dual logos -->
        <div class="report-header">
            <!-- Main Logo -->
            <div>
                <img src="<?= validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" class="logo">
            </div>
            
            <!-- Center Header -->
            <div class="header-center">
                <h2><?= $_settings->info('name') ?></h2>
                <h4>OR/CR Documents Management Report</h4>
                <p>Date Range: <?= date('M d, Y', strtotime($date_start)) ?> - <?= date('M d, Y', strtotime($date_end)) ?></p>
                <p>Generated on: <?= date('F d, Y \a\t H:i A') ?></p>
            </div>
            
            <!-- Secondary Logo -->
            <div>
                <img src="<?= validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" class="logo">
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <div class="stat-item">
                <div class="stat-number"><?= number_format($stats['total']) ?></div>
                <div class="stat-label">Total Documents</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= number_format($stats['released']) ?></div>
                <div class="stat-label">Released</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= number_format($stats['pending']) ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <!-- Report Information -->
        <div class="report-info">
            <p><strong>Report Period:</strong> <?= date('M d, Y', strtotime($date_start)) ?> to <?= date('M d, Y', strtotime($date_end)) ?></p>
            <p><strong>Filter Status:</strong> <?= !empty($status_filter) ? ucfirst($status_filter) : 'All Statuses' ?></p>
            <p><strong>Total Records:</strong> <?= $qry->num_rows ?> documents</p>
        </div>

        <!-- Documents Table -->
        <table class="documents-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">Customer</th>
                    <th width="10%">Document Type</th>
                    <th width="15%">Document Number</th>
                    <th width="12%">Plate Number</th>
                    <th width="12%">Release Date</th>
                    <th width="10%">Status</th>
                    <th width="16%">Date Uploaded</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                while($row = $qry->fetch_assoc()): 
                    foreach($row as $k => $v){
                        $row[$k] = trim(stripslashes($v));
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $i++ ?></td>
                    <td><?= ucwords($row['customer_name']) ?></td>
                    <td class="text-center">
                        <span class="doc-type-badge doc-type-<?= $row['document_type'] ?>">
                            <?= strtoupper($row['document_type']) ?>
                        </span>
                    </td>
                    <td><?= $row['document_number'] ?></td>
                    <td><?= $row['plate_number'] ?: 'N/A' ?></td>
                    <td class="text-center">
                        <?php 
                        if($row['release_date']){
                            echo date("M d, Y", strtotime($row['release_date']));
                        } else {
                            echo '<span style="color: #999;">Not set</span>';
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <span class="status-badge status-<?= $row['status'] ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td class="text-center"><?= date("M d, Y", strtotime($row['date_created'])) ?></td>
                </tr>
                <?php endwhile; ?>
                
                <?php if($qry->num_rows <= 0): ?>
                <tr>
                    <td colspan="8" class="text-center">No documents found for the selected criteria.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer Information -->
        <div class="footer-info">
            <p><strong>Company Information:</strong></p>
            <p>📍 National Highway Brgy. Parian, Calamba City, Laguna</p>
            <p>📞 0948-235-3207 | ✉️ starhondacalamba55@gmail.com</p>
            <p>📘 Facebook: @starhondacalambabranch</p>
            <hr style="margin: 10px 0;">
            <p>This report was generated on <?= date('F d, Y \a\t H:i A') ?> by <?= ucwords($_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname')) ?></p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
