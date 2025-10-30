<?php
if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

$client_id = $_settings->userdata('id');

// Get customer data
$customer = $conn->query("SELECT * FROM client_list WHERE id = '{$client_id}'")->fetch_assoc();

// Get account balance and amounts per status, strictly for this client
$account_balance = $conn->query("SELECT 
    COALESCE(SUM(total_amount), 0) as total_balance,
    COALESCE(SUM(CASE WHEN status IN (4,6) THEN total_amount ELSE 0 END), 0) as paid_amount,
    COALESCE(SUM(CASE WHEN status IN (0,1,2,3) THEN total_amount ELSE 0 END), 0) as pending_amount
    FROM order_list 
    WHERE client_id = '{$client_id}' AND status != 5")->fetch_assoc();

// Reflect account balance from client_list (single source of truth)
$client_balance_row = $conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0")->fetch_assoc();
$client_account_balance = $client_balance_row ? (float)$client_balance_row['account_balance'] : 0;

// Installment schedule with due dates and delay/penalty computed from invoices or 30-day rule
$installments = $conn->query("SELECT 
    ol.id,
    ol.ref_code,
    ol.total_amount,
    ol.status as order_status,
    ol.date_created,
    COALESCE(i.due_date, DATE_ADD(ol.date_created, INTERVAL 30 DAY)) as due_date,
    CASE 
        WHEN COALESCE(i.payment_status, CASE WHEN ol.status IN (4,6) THEN 'paid' ELSE 'unpaid' END) = 'paid' THEN 0
        ELSE DATEDIFF(CURDATE(), COALESCE(i.due_date, DATE_ADD(ol.date_created, INTERVAL 30 DAY)))
    END as days_overdue,
    COALESCE(i.payment_status, CASE WHEN ol.status IN (4,6) THEN 'paid' ELSE 'unpaid' END) as payment_status
    FROM order_list ol
    LEFT JOIN invoices i ON i.order_id = ol.id
    WHERE ol.client_id = '{$client_id}' 
    AND ol.status != 5
    ORDER BY ol.date_created DESC");

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
        <div class="row mb-4">
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
                                        <span class="info-box-number" id="client_account_balance_value">₱<?= number_format($client_account_balance, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-credit-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Installment Balance</span>
                                        <span class="info-box-number">₱<?= number_format($account_balance['pending_amount'], 2) ?></span>
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
                                        <th>Delay</th>
                                        <th>Late Fee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $late_fee_rate = 0.03; // 3% per month overdue
                                    while($installment = $installments->fetch_assoc()): 
                                        $days_overdue = (int)$installment['days_overdue'];
                                        $is_paid = ($installment['payment_status'] === 'paid');
                                        $months_overdue = $days_overdue > 0 ? floor($days_overdue / 30) : 0;
                                        $late_fee_amount = (!$is_paid && $months_overdue > 0) ? ($installment['total_amount'] * $late_fee_rate * $months_overdue) : 0;
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="./?p=view_order&id=<?= $installment['ref_code'] ?>" class="text-primary">
                                                <?= $installment['ref_code'] ?>
                                            </a>
                                        </td>
                                        <td>₱<?= number_format($installment['total_amount'], 2) ?></td>
                                        <td><?= date('M d, Y', strtotime($installment['due_date'])) ?></td>
                                        <td>
                                            <?php if($is_paid): ?>
                                                <span class="badge badge-success">🟢 Paid</span>
                                            <?php elseif($days_overdue > 0): ?>
                                                <span class="badge badge-danger">🔴 Late Payment</span>
                                            <?php elseif($days_overdue == 0 || $days_overdue > -3): ?>
                                                <span class="badge badge-warning">🟡 Pending / Due Soon</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">On Track</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($is_paid): ?>
                                                <span class="text-success">No delay</span>
                                            <?php elseif($days_overdue > 0): ?>
                                                <span class="text-danger"><?= $days_overdue ?> day(s) overdue</span>
                                            <?php elseif($days_overdue == 0): ?>
                                                <span class="text-warning">Due today</span>
                                            <?php else: ?>
                                                <span class="text-success"><?= abs($days_overdue) ?> day(s) remaining</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(!$is_paid && $months_overdue > 0): ?>
                                                <span class="text-danger">+<?= ($late_fee_rate * 100) ?>%/mo = ₱<?= number_format($late_fee_amount, 2) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
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
                                                <?= $order['status'] == 0 ? 'Pending' : ($order['status'] == 1 ? 'Approved Order' : ($order['status'] == 4 ? 'Delivered' : 'Cancelled')) ?>
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
            <!-- Recent Appointments -->
            <div class="col-md-6">
                <div class="card card-outline card-warning shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Recent Appointments</b></h4>
                        <div class="card-tools">
                            <a href="./?p=my_appointments" class="btn btn-sm btn-warning">View All</a>
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
                                <a href="./?p=my_services" class="btn btn-info btn-block">
                                    <i class="fa fa-cogs fa-2x mb-2"></i><br>
                                    My Services
                                </a>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6 mb-3">
                                <a href="./?p=my_orders" class="btn btn-secondary btn-block">
                                    <i class="fa fa-shopping-bag fa-2x mb-2"></i><br>
                                    My Orders
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="./?p=manage_account" class="btn btn-dark btn-block">
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
        
        <!-- OR/CR Upload Modal (Client) -->
        <div class="modal fade" id="orcrUploadModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload OR/CR Document</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="orcrUploadForm" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Document Type</label>
                                <select name="document_type" class="form-control" required>
                                    <option value="">Select Document Type</option>
                                    <option value="or">Original Receipt (OR)</option>
                                    <option value="cr">Certificate of Registration (CR)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Document Number</label>
                                <input type="text" name="document_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Plate Number</label>
                                <input type="text" name="plate_number" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Release Date</label>
                                <input type="date" name="release_date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Document File</label>
                                <input type="file" name="document_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="text-muted">Accepted formats: PDF, JPG, JPEG, PNG</small>
                            </div>
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Upload Document</button>
                        </div>
                    </form>
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
                    $('#client_account_balance_value').text('₱' + parseFloat(resp.data.balance).toFixed(2));
                    
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
    
    // OR/CR upload (client)
    window.showORCRUpload = function(){
        $('#orcrUploadModal').modal('show');
    }
    
    $('#orcrUploadForm').submit(function(e){
        e.preventDefault();
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=upload_orcr_document",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            dataType: 'json',
            success:function(resp){
                if(resp.status == 'success'){
                    $('#orcrUploadModal').modal('hide');
                    location.reload();
                }else{
                    alert_toast(resp.msg,'error');
                }
                end_loader();
            }
        });
    });
});
</script>
