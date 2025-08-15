<?php
if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

$client_id = $_settings->userdata('id');

// Get customer data
$customer = $conn->query("SELECT * FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();

// Get account balance
$balance = $conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}'")->fetch_assoc()['account_balance'];
$balance = $balance ? $balance : 0.00;

// Get recent transactions
$transactions = $conn->query("SELECT * FROM customer_transactions WHERE client_id = '{$client_id}' ORDER BY date_created DESC LIMIT 5");

// Get recent orders
$orders = $conn->query("SELECT * FROM order_list WHERE client_id = '{$client_id}' ORDER BY date_created DESC LIMIT 5");

// Get recent service requests
$services = $conn->query("SELECT * FROM service_requests WHERE client_id = '{$client_id}' ORDER BY date_created DESC LIMIT 5");

// Get recent appointments
$appointments = $conn->query("SELECT * FROM appointments WHERE client_id = '{$client_id}' ORDER BY appointment_date DESC LIMIT 5");

// Get OR/CR documents
$documents = $conn->query("SELECT * FROM or_cr_documents WHERE client_id = '{$client_id}' ORDER BY date_created DESC LIMIT 5");

// Get unread notifications count
$unread_notifications = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = '{$client_id}' AND is_read = 0")->fetch_assoc()['count'];
?>

<div class="content py-5 mt-3">
    <div class="container">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h3 class="mb-1">Welcome back, <?= ucwords($customer['firstname'] . ' ' . $customer['lastname']) ?>!</h3>
                                <p class="text-muted mb-0">Manage your account, view orders, and track your services all in one place.</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="h2 text-primary mb-0">₱<?= number_format($balance, 2) ?></div>
                                <small class="text-muted">Account Balance</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Orders</span>
                        <span class="info-box-number">
                            <?= $conn->query("SELECT COUNT(*) as count FROM order_list WHERE client_id = '{$client_id}'")->fetch_assoc()['count'] ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-tools"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Service Requests</span>
                        <span class="info-box-number">
                            <?= $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE client_id = '{$client_id}'")->fetch_assoc()['count'] ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Appointments</span>
                        <span class="info-box-number">
                            <?= $conn->query("SELECT COUNT(*) as count FROM appointments WHERE client_id = '{$client_id}'")->fetch_assoc()['count'] ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-bell"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Notifications</span>
                        <span class="info-box-number"><?= $unread_notifications ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-md-6">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Recent Orders</b></h4>
                        <div class="card-tools">
                            <a href="./?p=my_orders" class="btn btn-sm btn-primary">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($orders->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Ref Code</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="./?p=view_order&id=<?= $order['id'] ?>" class="text-primary">
                                                <?= $order['ref_code'] ?>
                                            </a>
                                        </td>
                                        <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $order['status'] == 0 ? 'secondary' : ($order['status'] == 1 ? 'primary' : ($order['status'] == 4 ? 'success' : 'danger')) ?>">
                                                <?= $order['status'] == 0 ? 'Pending' : ($order['status'] == 1 ? 'Packed' : ($order['status'] == 4 ? 'Delivered' : 'Cancelled')) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($order['date_created'])) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center">No orders found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Service Requests -->
            <div class="col-md-6">
                <div class="card card-outline card-success shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Recent Service Requests</b></h4>
                        <div class="card-tools">
                            <a href="./?p=my_services" class="btn btn-sm btn-success">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($services->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Service Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($service = $services->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="./?p=view_request&id=<?= $service['id'] ?>" class="text-success">
                                                #<?= $service['id'] ?>
                                            </a>
                                        </td>
                                        <td><?= $service['service_type'] ?></td>
                                        <td>
                                            <span class="badge badge-<?= $service['status'] == 0 ? 'secondary' : ($service['status'] == 1 ? 'primary' : ($service['status'] == 2 ? 'warning' : ($service['status'] == 3 ? 'success' : 'danger'))) ?>">
                                                <?= $service['status'] == 0 ? 'Pending' : ($service['status'] == 1 ? 'Confirmed' : ($service['status'] == 2 ? 'On Progress' : ($service['status'] == 3 ? 'Done' : 'Cancelled'))) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($service['date_created'])) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center">No service requests found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Recent Transactions -->
            <div class="col-md-6">
                <div class="card card-outline card-info shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Recent Transactions</b></h4>
                        <div class="card-tools">
                            <a href="./?p=manage_account" class="btn btn-sm btn-info">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($transactions->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($trans = $transactions->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($trans['date_created'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $trans['transaction_type'] == 'payment' ? 'success' : ($trans['transaction_type'] == 'refund' ? 'warning' : 'info') ?>">
                                                <?= ucfirst($trans['transaction_type']) ?>
                                            </span>
                                        </td>
                                        <td><?= $trans['description'] ?></td>
                                        <td class="text-right">₱<?= number_format($trans['amount'], 2) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center">No transactions found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="col-md-6">
                <div class="card card-outline card-warning shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Recent Appointments</b></h4>
                        <div class="card-tools">
                            <a href="./?p=appointments" class="btn btn-sm btn-warning">View All</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($appointments->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($appointment = $appointments->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($appointment['appointment_date'])) ?></td>
                                        <td><?= $appointment['appointment_time'] ?></td>
                                        <td>
                                            <?php 
                                            $service = $conn->query("SELECT service FROM service_list WHERE id = '{$appointment['service_type']}'")->fetch_assoc();
                                            echo $service ? $service['service'] : 'N/A';
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $appointment['status'] == 'confirmed' ? 'success' : ($appointment['status'] == 'pending' ? 'warning' : 'secondary') ?>">
                                                <?= ucfirst($appointment['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center">No appointments found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-outline card-dark shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Quick Actions</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="./?p=products" class="btn btn-primary btn-block">
                                    <i class="fa fa-shopping-cart fa-2x mb-2"></i><br>
                                    Browse Products
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="./?p=appointments" class="btn btn-warning btn-block">
                                    <i class="fa fa-calendar fa-2x mb-2"></i><br>
                                    Book Appointment
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="./?p=send_request" class="btn btn-success btn-block">
                                    <i class="fa fa-tools fa-2x mb-2"></i><br>
                                    Request Service
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="./?p=manage_account" class="btn btn-info btn-block">
                                    <i class="fa fa-user fa-2x mb-2"></i><br>
                                    Manage Account
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OR/CR Documents -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-outline card-secondary shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>OR/CR Documents</b></h4>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-secondary" onclick="showORCRUpload()">Upload New</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($documents->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Document Number</th>
                                        <th>Plate Number</th>
                                        <th>Status</th>
                                        <th>Upload Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($doc = $documents->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= strtoupper($doc['document_type']) ?></td>
                                        <td><?= $doc['document_number'] ?></td>
                                        <td><?= $doc['plate_number'] ?: 'N/A' ?></td>
                                        <td>
                                            <span class="badge badge-<?= $doc['status'] == 'pending' ? 'warning' : ($doc['status'] == 'released' ? 'success' : 'danger') ?>">
                                                <?= ucfirst($doc['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($doc['date_created'])) ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center">No documents uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Auto-refresh dashboard data every 30 seconds
    setInterval(function(){
        refreshDashboardData();
    }, 30000);
    
    function refreshDashboardData(){
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=get_customer_dashboard_data",
            method: "POST",
            dataType: "json",
            success: function(resp){
                if(resp.status == 'success'){
                    // Update balance
                    $('.text-primary').text('₱' + parseFloat(resp.data.balance).toFixed(2));
                    
                    // Update notification count
                    // You can implement real-time updates here
                }
            }
        });
    }
});
</script>
