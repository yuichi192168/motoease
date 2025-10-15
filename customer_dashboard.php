<?php
if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

$client_id = $_settings->userdata('id');

// Get customer data
$customer = $conn->query("SELECT * FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();

// Get account balance and installment details
$account_balance = $conn->query("SELECT 
    COALESCE(SUM(total_amount), 0) as total_balance,
    COALESCE(SUM(CASE WHEN payment_status = 'installment' THEN total_amount ELSE 0 END), 0) as installment_balance,
    COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as paid_amount
    FROM order_list 
    WHERE client_id = '{$client_id}' AND status != 5")->fetch_assoc();

// Get installment details
$installments = $conn->query("SELECT 
    ol.ref_code,
    ol.total_amount,
    ol.payment_status,
    ol.date_created,
    ol.due_date,
    DATEDIFF(CURDATE(), ol.due_date) as days_overdue
    FROM order_list ol
    WHERE ol.client_id = '{$client_id}' 
    AND ol.payment_status = 'installment' 
    AND ol.status != 5
    ORDER BY ol.due_date ASC");

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

// Get recent notifications
$recent_notifications = $conn->query("SELECT * FROM notifications WHERE user_id = '{$client_id}' ORDER BY date_created DESC LIMIT 5");
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
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo validate_image($customer['avatar']) ?>" class="img-circle elevation-2 me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="Avatar">
                                    <div>
                                        <h3 class="mb-1">Welcome back, <?= ucwords($customer['firstname'] . ' ' . $customer['lastname']) ?>!</h3>
                                        <p class="text-muted mb-0">Manage your account, view orders, and track your services all in one place.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-right"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        Account Balance Section
        <!-- <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-success shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b><i class="fas fa-wallet"></i> Account Balance</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Balance</span>
                                        <span class="info-box-number">₱<?= number_format($account_balance['total_balance'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-credit-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Installment Balance</span>
                                        <span class="info-box-number">₱<?= number_format($account_balance['installment_balance'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Paid Amount</span>
                                        <span class="info-box-number">₱<?= number_format($account_balance['paid_amount'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

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
            <!-- Appointments temporarily disabled -->
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

        <!-- Installment Details Section -->
        <?php if($installments->num_rows > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-warning shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b><i class="fas fa-calendar-alt"></i> Installment Details</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order Reference</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Days Overdue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($installment = $installments->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <a href="./?p=view_order&id=<?= $installment['ref_code'] ?>" class="text-primary">
                                                <?= $installment['ref_code'] ?>
                                            </a>
                                        </td>
                                        <td>₱<?= number_format($installment['total_amount'], 2) ?></td>
                                        <td><?= date('M d, Y', strtotime($installment['due_date'])) ?></td>
                                        <td>
                                            <?php if($installment['days_overdue'] > 0): ?>
                                                <span class="badge badge-danger">Overdue</span>
                                            <?php elseif($installment['days_overdue'] == 0): ?>
                                                <span class="badge badge-warning">Due Today</span>
                                            <?php else: ?>
                                                <span class="badge badge-success">Up to Date</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($installment['days_overdue'] > 0): ?>
                                                <span class="text-danger"><?= $installment['days_overdue'] ?> days</span>
                                            <?php elseif($installment['days_overdue'] == 0): ?>
                                                <span class="text-warning">Due today</span>
                                            <?php else: ?>
                                                <span class="text-success"><?= abs($installment['days_overdue']) ?> days remaining</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                                            <a href="javascript:void(0)" class="text-primary view_order" data-id="<?= $order['id'] ?>">
                                                <?= $order['ref_code'] ?>
                                            </a>
                                        </td>
                                        <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $order['status'] == 0 ? 'secondary' : ($order['status'] == 1 ? 'primary' : ($order['status'] == 4 ? 'success' : 'danger')) ?>">
                                                <?= $order['status'] == 0 ? 'Pending' : ($order['status'] == 1 ? 'Ready for pickup' : ($order['status'] == 4 ? 'Delivered' : 'Cancelled')) ?>
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

            <!-- Recent Appointments temporarily disabled -->
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

        <!-- Notifications -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card card-outline card-warning shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Notifications</b></h4>
                        <div class="card-tools">
                            <?php if($unread_notifications > 0): ?>
                            <span class="badge badge-danger"><?= $unread_notifications ?> unread</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if($recent_notifications->num_rows > 0): ?>
                        <div class="list-group">
                            <?php while($notification = $recent_notifications->fetch_assoc()): ?>
                            <div class="list-group-item <?= $notification['is_read'] == 0 ? 'list-group-item-warning' : '' ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">
                                        <?php if($notification['is_read'] == 0): ?>
                                        <i class="fa fa-circle text-warning" style="font-size: 8px;"></i>
                                        <?php endif; ?>
                                        <?= $notification['title'] ?>
                                    </h6>
                                    <small><?= date('M d, Y H:i', strtotime($notification['date_created'])) ?></small>
                                </div>
                                <p class="mb-1"><?= $notification['message'] ?></p>
                                <?php if($notification['is_read'] == 0): ?>
                                <button class="btn btn-sm btn-outline-primary mark-read" data-id="<?= $notification['id'] ?>">Mark as Read</button>
                                <?php endif; ?>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <p class="text-muted text-center">No notifications yet.</p>
                        <?php endif; ?>
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
    // Handle mark as read for notifications
    $(document).on('click', '.mark-read', function(){
        var notificationId = $(this).data('id');
        var button = $(this);
        
        $.ajax({
            url: _base_url_ + 'classes/PaymentNotification.php?action=mark_read',
            method: 'POST',
            data: {
                notification_id: notificationId,
                customer_id: '<?= $client_id ?>'
            },
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    button.closest('.list-group-item').removeClass('list-group-item-warning');
                    button.closest('.list-group-item').find('.fa-circle').remove();
                    button.remove();
                    
                    // Update unread count
                    var currentCount = parseInt($('.badge-danger').text());
                    if(currentCount > 1) {
                        $('.badge-danger').text(currentCount - 1);
                    } else {
                        $('.badge-danger').remove();
                    }
                }
            }
        });
    });
    
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
    
    // Handle view order clicks
    $('.view_order').click(function(){
        var order_id = $(this).data('id');
        viewOrder(order_id);
    });
    
    function viewOrder(order_id){
        uni_modal("Order Details", "view_order.php?id=" + order_id, "modal-lg");
    }
});
</script>
