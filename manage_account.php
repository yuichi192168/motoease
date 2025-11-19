<?php 
// Updated: Fixed database query errors - removed non-existent payment_status and due_date fields
// Last updated: 2025-01-27
if($_settings->userdata('id') > 0 && $_settings->userdata('login_type') == 2){
    // Always fetch fresh client record so Admin updates reflect instantly
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

// Get account balance and order details
$client_id = $_settings->userdata('id');
$current_balance_row = $conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0")->fetch_assoc();
$current_balance = $current_balance_row ? (float)$current_balance_row['account_balance'] : 0;
// Align with report logic: delivered and claimed count as paid
$account_balance = $conn->query("SELECT 
    COALESCE(SUM(total_amount), 0) as total_balance,
    COALESCE(SUM(CASE WHEN status IN (4,6) THEN total_amount ELSE 0 END), 0) as delivered_amount,
    COALESCE(SUM(CASE WHEN status IN (0,1,2,3) THEN total_amount ELSE 0 END), 0) as pending_amount
    FROM order_list 
    WHERE client_id = '{$client_id}' AND status != 5")->fetch_assoc();

// Get order details (using status instead of payment_status)
$installments = $conn->query("SELECT 
    ol.id,
    ol.ref_code,
    ol.total_amount,
    ol.status,
    ol.date_created,
    ol.date_updated,
    CASE 
        WHEN ol.status = 0 THEN 'Pending'
        WHEN ol.status = 1 THEN 'Approved Order'
        WHEN ol.status = 2 THEN 'For Delivery'
        WHEN ol.status = 3 THEN 'On the Way'
        WHEN ol.status = 4 THEN 'Delivered'
        WHEN ol.status = 6 THEN 'Claimed'
        WHEN ol.status = 5 THEN 'Cancelled'
        ELSE 'Unknown'
    END as status_text
    FROM order_list ol
    WHERE ol.client_id = '{$client_id}' 
    ORDER BY ol.date_created DESC");

// Get OR/CR documents
$documents = $conn->query("SELECT * FROM or_cr_documents WHERE client_id = '{$_settings->userdata('id')}' ORDER BY date_created DESC");

// Determine if client has installment-based orders
$has_installments = false;
$installment_check = $conn->query("SELECT COUNT(*) as cnt FROM order_list WHERE client_id = '{$client_id}' AND requires_credit = 1");
if($installment_check){
    $installment_row = $installment_check->fetch_assoc();
    $has_installments = isset($installment_row['cnt']) && (int)$installment_row['cnt'] > 0;
}

// Load customer account balances / payment schedules if available
$customer_accounts = [];
$account_schedule_map = [];
$account_balance_summary = [
    'total' => 0,
    'paid' => 0,
    'pending' => 0
];
$customer_accounts_helper = null;
if(file_exists(base_app.'classes/CustomerAccountBalance.php')){
    require_once(base_app.'classes/CustomerAccountBalance.php');
}
if(class_exists('CustomerAccountBalance')){
    $customer_accounts_helper = new CustomerAccountBalance($conn);
    $customer_accounts = $customer_accounts_helper->getCustomerAccounts($client_id);
    if(!empty($customer_accounts)){
        $has_installments = true;
        foreach($customer_accounts as $account_row){
            $account_balance_summary['total'] += (float)($account_row['total_price'] ?? 0);
            $account_balance_summary['paid'] += (float)($account_row['paid_amount'] ?? 0);
            $account_balance_summary['pending'] += (float)($account_row['remaining_balance'] ?? 0);
            $account_schedule_map[$account_row['id']] = $customer_accounts_helper->getPaymentSchedule($account_row['id']);
        }
    }
}
$has_customer_payment_schedule = !empty($customer_accounts);
$summary_total_amount = $has_customer_payment_schedule ? $account_balance_summary['total'] : ($account_balance['total_balance'] ?? 0);
$summary_pending_amount = $has_customer_payment_schedule ? $account_balance_summary['pending'] : ($account_balance['pending_amount'] ?? 0);
$summary_paid_amount = $has_customer_payment_schedule ? $account_balance_summary['paid'] : ($account_balance['delivered_amount'] ?? 0);
$summary_installment_plan = null;
$summary_installment_monthly = null;
if($has_customer_payment_schedule){
    $primary_account = $customer_accounts[0];
    if(isset($primary_account['installment_plan_months']) && (int)$primary_account['installment_plan_months'] > 0){
        $summary_installment_plan = (int)$primary_account['installment_plan_months'];
    }
    if(isset($primary_account['monthly_payment_amount']) && (float)$primary_account['monthly_payment_amount'] > 0){
        $summary_installment_monthly = (float)$primary_account['monthly_payment_amount'];
    }
}
?>
<style>
/* Ensure customer info boxes use the requested teal color */
.info-box.bg-info {
	background-color: #17A2B8 !important;
	color: #ffffff !important;
}
</style>
<div class="content py-5 mt-3">
    <div class="container">
        <!-- Welcome Section with Avatar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="<?php echo validate_image($_settings->userdata('avatar')) ?>" class="img-circle elevation-2 me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="Avatar">
                            <div>
                                <h3 class="mb-1">Account Management</h3>
                                <p class="text-muted mb-0">Welcome, <?= ucwords($_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname')) ?>! Manage your profile and account settings.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Account Balance Section (shown only when client has installment plans) -->
        <?php if($has_installments): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-success shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b><i class="fas fa-wallet"></i> Account Balances & Payment Status</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-primary" style="min-height: 80px; display: flex; align-items: center;">
                                    <span class="info-box-icon" style="display: flex; align-items: center; justify-content: center;"><i class="fas fa-money-bill-wave"></i></span>
                                    <div class="info-box-content" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                        <span class="info-box-text" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 5px;">Total Price</span>
                                        <span class="info-box-number" style="display: block; font-size: 24px; font-weight: bold;">₱<?= number_format($summary_total_amount, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-warning" style="min-height: 80px; display: flex; align-items: center;">
                                    <span class="info-box-icon" style="display: flex; align-items: center; justify-content: center;"><i class="fas fa-credit-card"></i></span>
                                    <div class="info-box-content" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                        <span class="info-box-text" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 5px;">Paid Amount</span>
                                        <span class="info-box-number" style="display: block; font-size: 24px; font-weight: bold;">₱<?= number_format($summary_paid_amount, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-info" style="min-height: 80px; display: flex; align-items: center;">
                                    <span class="info-box-icon" style="display: flex; align-items: center; justify-content: center;"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                        <span class="info-box-text" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 5px;">Remaining Balance</span>
                                        <span class="info-box-number" style="display: block; font-size: 24px; font-weight: bold;">₱<?= number_format($summary_pending_amount, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-secondary" style="min-height: 80px; display: flex; align-items: center;">
                                    <span class="info-box-icon" style="display: flex; align-items: center; justify-content: center;"><i class="fas fa-stream"></i></span>
                                    <div class="info-box-content" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                                        <span class="info-box-text" style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 5px;">Installment Plan</span>
                                        <span class="info-box-number" style="display: block; font-size: 24px; font-weight: bold;">
                                            <?= $summary_installment_plan ? $summary_installment_plan . ' mos' : '—' ?>
                                        </span>
                                        <?php if($summary_installment_monthly): ?>
                                            <small class="text-light" style="font-size: 12px;">₱<?= number_format($summary_installment_monthly, 2) ?>/month</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Schedule replicated from Admin Account Details -->
                        <?php if($has_customer_payment_schedule): ?>
                            <?php foreach($customer_accounts as $client_account): 
                                $schedule_items = $account_schedule_map[$client_account['id']] ?? [];
                                $account_status = strtolower($client_account['status'] ?? 'active');
                                $account_status_badge = 'secondary';
                                switch($account_status){
                                    case 'paid': $account_status_badge = 'success'; break;
                                    case 'active': $account_status_badge = 'primary'; break;
                                    case 'defaulted': $account_status_badge = 'danger'; break;
                                }
                            ?>
                            <div class="border rounded p-3 mt-3 account-payment-schedule">
                                <div class="d-flex flex-wrap justify-content-between align-items-start">
                                    <div class="mb-2">
                                        <h5 class="mb-1 text-dark"><?= htmlspecialchars($client_account['item_purchased'] ?? 'Account #'.$client_account['id']) ?></h5>
                                        <p class="text-muted mb-0">Order #<?= htmlspecialchars($client_account['order_id'] ?? 'N/A') ?></p>
                                    </div>
                                    <div class="text-right">
                                        <small class="text-muted d-block">Status</small>
                                        <span class="badge badge-<?= $account_status_badge ?>"><?= ucfirst($account_status) ?></span>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-sm-3 mb-2">
                                        <small class="text-muted d-block">Total Price</small>
                                        <span class="h5 mb-0 text-dark">₱<?= number_format($client_account['total_price'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="col-sm-3 mb-2">
                                        <small class="text-muted d-block">Paid Amount</small>
                                        <span class="h5 mb-0 text-success">₱<?= number_format($client_account['paid_amount'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="col-sm-3 mb-2">
                                        <small class="text-muted d-block">Remaining Balance</small>
                                        <span class="h5 mb-0 text-danger">₱<?= number_format($client_account['remaining_balance'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="col-sm-3 mb-2">
                                        <small class="text-muted d-block">Installment Plan</small>
                                        <span class="h5 mb-0 text-dark">
                                            <?= !empty($client_account['installment_plan_months']) ? intval($client_account['installment_plan_months']).' mos' : '—' ?>
                                        </span>
                                        <?php if(!empty($client_account['monthly_payment_amount'])): ?>
                                            <div class="text-muted small">Monthly: ₱<?= number_format($client_account['monthly_payment_amount'], 2) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if(!empty($schedule_items)): ?>
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered table-striped table-sm mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Installment</th>
                                                <th>Due Date</th>
                                                <th>Amount Due</th>
                                                <th>Paid Amount</th>
                                                <th>Late Fee</th>
                                                <th>Remaining Balance</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($schedule_items as $item): 
                                                $schedule_status = $item['payment_status'] ?? 'Unpaid';
                                                $schedule_badge = 'info';
                                                switch($schedule_status){
                                                    case 'Paid': $schedule_badge = 'success'; break;
                                                    case 'Late': $schedule_badge = 'danger'; break;
                                                    case 'Partial': $schedule_badge = 'warning'; break;
                                                    case 'Unpaid': $schedule_badge = 'info'; break;
                                                    default: $schedule_badge = 'secondary'; break;
                                                }
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['installment_number']) ?></td>
                                                <td><?= date('M d, Y', strtotime($item['due_date'])) ?></td>
                                                <td class="text-right">₱<?= number_format($item['amount_due'], 2) ?></td>
                                                <td class="text-right text-success">₱<?= number_format($item['paid_amount'], 2) ?></td>
                                                <td class="text-right text-danger">₱<?= number_format($item['late_fee'], 2) ?></td>
                                                <td class="text-right">₱<?= number_format($item['remaining_balance'], 2) ?></td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $schedule_badge ?>"><?= $schedule_status ?></span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                    <p class="text-muted mb-0 mt-3">No payment schedule entries yet for this account.</p>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info mt-3 mb-0">
                                No payment schedule is available yet. Please check back after your account has been enrolled.
                            </div>
                        <?php endif; ?>

                        <?php 
                        // Balance adjustments/payment transactions (admin changes reflected here)
                        $tx_rs = $conn->query("SELECT date_created, transaction_type, amount, description, reference_id
                                                FROM customer_transactions 
                                                WHERE client_id = '{$client_id}'
                                                ORDER BY date_created DESC LIMIT 50");
                        ?>
                        <!-- Balance Adjustments & Payments -->
                        <!-- <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($tx_rs && $tx_rs->num_rows > 0): ?>
                                        <?php while($tx = $tx_rs->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('M d, Y H:i', strtotime($tx['date_created'])) ?></td>
                                            <td>
                                                <span class="badge badge-<?= $tx['transaction_type'] === 'payment' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst($tx['transaction_type']) ?>
                                                </span>
                                            </td>
                                            <td class="<?= $tx['transaction_type'] === 'payment' ? 'text-success' : 'text-danger' ?>">
                                                <?= $tx['transaction_type'] === 'payment' ? '+' : '-' ?>₱<?= number_format((float)$tx['amount'], 2) ?>
                                            </td>
                                            <td><?= htmlspecialchars($tx['description']) ?></td>
                                            <td><?= htmlspecialchars($tx['reference_id']) ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center text-muted">No balance adjustments or payment entries.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Order Details Section -->
        <?php if($installments->num_rows > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-warning shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b><i class="fas fa-shopping-cart"></i> Order History</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Order Reference</th>
                                        <th>Amount</th>
                                        <th>Date Ordered</th>
                                        <th>Status</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Reset the result pointer to ensure we can loop through results
                                    $installments->data_seek(0);
                                    while($order = $installments->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="javascript:void(0)" class="text-primary view_order" data-id="<?= $order['id'] ?? '' ?>">
                                                <?= $order['ref_code'] ?>
                                            </a>
                                        </td>
                                        <td>₱<?= number_format($order['total_amount'], 2) ?></td>
                                        <td><?= date('M d, Y', strtotime($order['date_created'])) ?></td>
                                        <td>
                                            <?php 
                                            $status_class = '';
                                            switch($order['status']) {
                                                case 0: $status_class = 'badge-secondary'; break;
                                                case 1: $status_class = 'badge-primary'; break;
                                                case 2: $status_class = 'badge-info'; break;
                                                case 3: $status_class = 'badge-warning'; break;
                                                case 4: $status_class = 'badge-success'; break;
                                                default: $status_class = 'badge-secondary'; break;
                                            }
                                            ?>
                                            <span class="badge <?= $status_class ?>"><?= $order['status_text'] ?></span>
                                        </td>
                                        <td>
                                            <?php if(isset($order['date_updated']) && $order['date_updated']): ?>
                                                <?= date('M d, Y H:i', strtotime($order['date_updated'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not updated</span>
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

        <!-- Quick Actions -->
        <!-- <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-outline card-info shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Quick Actions</b></h4>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-info btn-sm" onclick="showVehicleInfo()">Update Vehicle Info</button>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Main Account Management -->
        <div class="card card-outline card-dark shadow rounded-0">
            <div class="card-header">
                <h4 class="card-title"><b>Manage Account Details/Credentials</b></h4>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <form id="register-frm" action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= isset($id) ? $id : "" ?>">
                        
                        <!-- Personal Information -->
                        <h5 class="text-primary mb-3">Personal Information</h5>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <input type="text" name="firstname" id="firstname" placeholder="Enter First Name" autofocus class="form-control form-control-sm form-control-border" value="<?= isset($firstname) ? $firstname : "" ?>" required>
                                <small class="ml-3">First Name</small>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="middlename" id="middlename" placeholder="Enter Middle Name (optional)" class="form-control form-control-sm form-control-border" value="<?= isset($middlename) ? $middlename : "" ?>">
                                <small class="ml-3">Middle Name</small>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="lastname" id="lastname" placeholder="Enter Last Name" class="form-control form-control-sm form-control-border" required value="<?= isset($lastname) ? $lastname : "" ?>">
                                <small class="ml-3">Last Name</small>
                            </div>
                            <div class="form-group col-md-6">
                                <select name="gender" id="gender" class="form-control form-control-sm form-control-border" required>
                                    <option value="Male" <?= isset($gender) && $gender == 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= isset($gender) && $gender == 'Female' ? 'selected' : '' ?>>Female</option>
                                </select>
                                <small class="ml-3">Gender</small>
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <h5 class="text-primary mb-3 mt-4">Contact Information</h5>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <input type="text" name="contact" id="contact" placeholder="Enter Contact Number" class="form-control form-control-sm form-control-border" required value="<?= isset($contact) ? $contact : "" ?>">
                                <small class="ml-3">Contact Number</small>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="email" name="email" id="email" placeholder="Enter Email Address" class="form-control form-control-sm form-control-border" required value="<?= isset($email) ? $email : "" ?>">
                                <small class="ml-3">Email Address</small>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="avatar" name="img" onchange="displayImg(this,$(this))" accept="image/*">
                                    <label class="custom-file-label" for="avatar">Choose Profile Picture</label>
                                </div>
                                <small class="ml-3">Profile Picture (Optional)</small>
                            </div>
                            <div class="form-group col-md-6 d-flex justify-content-center">
                                <img src="<?php echo validate_image(isset($avatar) ? $avatar :'') ?>" alt="Avatar Preview" id="cimg" class="img-fluid img-thumbnail" style="height: 100px; width: 100px; object-fit: cover; border-radius: 50%;">
                            </div>
                            <div class="form-group col-md-12">
                                <textarea name="address" id="address" rows="3" placeholder="Enter Complete Address" class="form-control form-control-sm form-control-border" required><?= isset($address) ? $address : "" ?></textarea>
                                <small class="ml-3">Complete Address</small>
                            </div>
                        </div>
                        
                        <!-- Vehicle Information -->
                        <h5 class="text-primary mb-3 mt-4">Vehicle Information</h5>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <input type="text" name="vehicle_plate_number" id="vehicle_plate_number" placeholder="Enter Plate Number" class="form-control form-control-sm form-control-border" value="<?= isset($vehicle_plate_number) ? $vehicle_plate_number : "" ?>">
                                <small class="ml-3">Vehicle Plate Number</small>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="or_cr_number" id="or_cr_number" placeholder="Enter OR/CR Number" class="form-control form-control-sm form-control-border" value="<?= isset($or_cr_number) ? $or_cr_number : "" ?>">
                                <small class="ml-3">OR/CR Number</small>
                            </div>
                        </div>
                        
                        <!-- Password Change -->
                        <h5 class="text-primary mb-3 mt-4">Change Password</h5>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <input type="password" name="oldpassword" id="oldpassword" placeholder="Enter Current Password" class="form-control form-control-sm form-control-border">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-eye-slash pass_type" data-type="password"></i></span>
                                    </div>
                                </div>
                                <small class="ml-3">Current Password</small>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <input type="password" name="password" id="password" placeholder="Enter New Password" class="form-control form-control-sm form-control-border">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-eye-slash pass_type" data-type="password"></i></span>
                                </div>
                                </div>
                                <small class="ml-3">New Password</small>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="input-group">
                                    <input type="password" id="cpassword" placeholder="Confirm New Password" class="form-control form-control-sm form-control-border">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-eye-slash pass_type" data-type="password"></i></span>
                                </div>
                                </div>
                                <small class="ml-3">Confirm New Password</small>
                            </div>
                        </div>
                        
                        <div class="text-right mt-4">
                            <button type="submit" class="btn btn-primary btn-sm px-4">Update Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions removed -->
        
        <!-- Service History -->
        <div class="card card-outline card-success shadow rounded-0 mt-4">
            <div class="card-header">
                <h4 class="card-title"><b>Service History</b></h4>
            </div>
            <div class="card-body">
                <?php 
                $service_history = $conn->query("SELECT s.*, s.service_type, s.vehicle_name, s.vehicle_registration_number FROM service_requests s WHERE s.client_id = '{$_settings->userdata('id')}' ORDER BY s.date_created DESC");
                if($service_history && $service_history->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Request #</th>
                                <th>Services</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($sh = $service_history->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $sh['id'] ?></td>
                                <td>
                                    <?php 
                                    $service_display = '';
                                    if (!empty($sh['service_type'])) {
                                        $service_display = $sh['service_type'];
                                        if (!empty($sh['vehicle_name'])) {
                                            $service_display .= ' - ' . $sh['vehicle_name'];
                                        }
                                        if (!empty($sh['vehicle_registration_number'])) {
                                            $service_display .= ' (' . $sh['vehicle_registration_number'] . ')';
                                        }
                                    } else {
                                        // Fallback: show request ID if no service type
                                        $service_display = 'Service Request #' . $sh['id'];
                                    }
                                    echo $service_display;
                                    ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $sh['status'] == 3 ? 'success' : ($sh['status'] == 2 ? 'warning' : ($sh['status'] == 1 ? 'primary' : ($sh['status'] == 4 ? 'danger' : 'secondary'))) ?>">
                                        <?= $sh['status'] == 3 ? 'Done' : ($sh['status'] == 2 ? 'On Progress' : ($sh['status'] == 1 ? 'Confirmed' : ($sh['status'] == 4 ? 'Cancelled' : 'Pending'))) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($sh['date_created'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No service history yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- OR/CR Documents -->
        <div class="card card-outline card-warning shadow rounded-0 mt-4">
            <div class="card-header">
                <h4 class="card-title"><b>OR/CR Documents</b></h4>
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
                                <th>Release Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($doc = $documents->fetch_assoc()): ?>
                            <tr>
                                <td><?= strtoupper($doc['document_type']) ?></td>
                                <td><?= $doc['document_number'] ?></td>
                                <td><?= $doc['plate_number'] ?: 'N/A' ?></td>
                                <td><?= $doc['release_date'] ? date('M d, Y', strtotime($doc['release_date'])) : 'N/A' ?></td>
                                <td>
                                    <span class="badge badge-<?= $doc['status'] == 'released' ? 'success' : ($doc['status'] == 'expired' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($doc['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($doc['file_path']): ?>
                                    <button type="button" class="btn btn-sm btn-info btn-view-orcr" data-file="<?= $doc['file_path'] ?>">View</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No OR/CR documents uploaded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- OR/CR File Viewer Modal (Client) -->
<div class="modal fade" id="orcrFileViewerClient" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Document</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="orcr_viewer_container_client"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function displayImg(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
        	$('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

    $(function(){
        $('.pass_type').click(function(){
            var type = $(this).attr('data-type')
            if(type == 'password'){
                $(this).attr('data-type','text')
                $(this).closest('.input-group').find('input').attr('type',"text")
                $(this).removeClass("fa-eye-slash")
                $(this).addClass("fa-eye")
            }else{
                $(this).attr('data-type','password')
                $(this).closest('.input-group').find('input').attr('type',"password")
                $(this).removeClass("fa-eye")
                $(this).addClass("fa-eye-slash")
            }
        })
        
        $('#register-frm').submit(function(e){
            e.preventDefault()
            var _this = $(this)
                    $('.err-msg').remove();
            var el = $('<div>')
                    el.hide()
            if($('#password').val() != $('#cpassword').val()){
                el.addClass('alert alert-danger err-msg').text('Password does not match.');
                _this.prepend(el)
                el.show('slow')
                return false;
            }
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Users.php?f=save_client",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured",'error');
                    end_loader();
                },
                success:function(resp){
                    if(typeof resp =='object' && resp.status == 'success'){
                        alert_toast(resp.msg || "Account updated successfully.", 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }else if(resp.status == 'failed' && !!resp.msg){   
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                    }else{
                        alert_toast("An error occured",'error');
                        end_loader();
                        console.log(resp)
                    }
                    end_loader();
                    $('html, body').scrollTop(0)
                }
            })
        })
        
        // Add Balance removed
        
        // Vehicle Info Form
        $('#vehicleInfoForm').submit(function(e){
            e.preventDefault();
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=update_vehicle_info",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                success:function(resp){
                    if(resp.status == 'success'){
                        $('#vehicleInfoModal').modal('hide');
                        alert_toast(resp.msg || "Vehicle information saved successfully.", 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }else{
                        alert_toast(resp.msg,'error');
                    }
                    end_loader();
                }
            });
        });
        
        // OR/CR Upload Form
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
                        alert_toast(resp.msg || "OR/CR document uploaded successfully.", 'success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }else{
                        alert_toast(resp.msg,'error');
                    }
                    end_loader();
                }
            });
        });

        // View document in modal (client)
        $(document).on('click', '.btn-view-orcr', function(){
            var file = $(this).attr('data-file') || '';
            var pathOnly = file.split('?')[0] || file;
            var ext = pathOnly.split('.').pop().toLowerCase();
            var html = '';
            if(file){
                if(ext === 'pdf'){
                    html = '<iframe src="'+file+'" width="100%" height="500" style="border:1px solid #ddd"></iframe>';
                } else {
                    html = '<img src="'+file+'" class="img-fluid" style="max-height:500px;border:1px solid #ddd">';
                }
            } else {
                html = '<div class="alert alert-secondary">No file to display.</div>';
            }
            $('#orcr_viewer_container_client').html(html);
            $('#orcrFileViewerClient').modal('show');
        });
    })
    
    // showAddBalance removed
    
    function showVehicleInfo() {
        $('#vehicleInfoModal').modal('show');
    }
    
    function showORCRUpload() {
        $('#orcrUploadModal').modal('show');
    }
    
    // Handle view order clicks
    $('.view_order').click(function(){
        var order_id = $(this).data('id');
        viewOrder(order_id);
    });
    
    function viewOrder(order_id){
        uni_modal("Order Details", "view_order.php?id=" + order_id, "modal-lg");
    }
</script>