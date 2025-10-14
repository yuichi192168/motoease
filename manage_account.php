<?php 
// Updated: Fixed database query errors - removed non-existent payment_status and due_date fields
// Last updated: 2025-01-27
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

// Get account balance and order details
$client_id = $_settings->userdata('id');
$account_balance = $conn->query("SELECT 
    COALESCE(SUM(total_amount), 0) as total_balance,
    COALESCE(SUM(CASE WHEN status = 4 THEN total_amount ELSE 0 END), 0) as delivered_amount,
    COALESCE(SUM(CASE WHEN status IN (0,1,2,3) THEN total_amount ELSE 0 END), 0) as pending_amount
    FROM order_list 
    WHERE client_id = '{$client_id}' AND status != 5")->fetch_assoc();

// Get order details (using status instead of payment_status)
$installments = $conn->query("SELECT 
    ol.ref_code,
    ol.total_amount,
    ol.status,
    ol.date_created,
    ol.date_updated,
    CASE 
        WHEN ol.status = 0 THEN 'Pending'
        WHEN ol.status = 1 THEN 'Ready for Pickup'
        WHEN ol.status = 2 THEN 'For Delivery'
        WHEN ol.status = 3 THEN 'On the Way'
        WHEN ol.status = 4 THEN 'Delivered'
        ELSE 'Unknown'
    END as status_text
    FROM order_list ol
    WHERE ol.client_id = '{$client_id}' 
    AND ol.status != 5
    ORDER BY ol.date_created DESC");

// Get OR/CR documents
$documents = $conn->query("SELECT * FROM or_cr_documents WHERE client_id = '{$_settings->userdata('id')}' ORDER BY date_created DESC");
?>
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
        
        <!-- Account Balance Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-success shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b><i class="fas fa-wallet"></i> Account Balances & Payment Status</b></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-primary">
                                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Price</span>
                                        <span class="info-box-number">₱157,350</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-credit-card"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Estimated Down Payment</span>
                                        <span class="info-box-number">₱16,200</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Monthly Payment</span>
                                        <span class="info-box-number">₱7,340</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Payment Status</span>
                                        <span class="info-box-number">
                                            <span class="badge badge-success">Up-to-date</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Status Alert -->
                <div class="alert alert-success mt-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                    <strong>Order Status:</strong> Status values reflect live order updates (Pending, Ready for Pickup, For Delivery, On the Way, Delivered).
                        </div>
                        
                        <!-- Payment History Table -->
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>January 15, 2025</td>
                                        <td>₱7,340</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>December 15, 2024</td>
                                        <td>₱7,340</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>November 15, 2024</td>
                                        <td>₱7,340</td>
                                        <td><span class="badge badge-success">Paid</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                                            <a href="./?p=view_order&id=<?= $order['id'] ?? '' ?>" class="text-primary">
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
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-outline card-info shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Quick Actions</b></h4>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-info btn-sm" onclick="showVehicleInfo()">Update Vehicle Info</button>
                        <!-- <button class="btn btn-warning btn-sm" onclick="showORCRUpload()">Upload OR/CR Document</button> -->
                    </div>
                </div>
            </div>
        </div>

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
                                    <a href="<?= validate_image($doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-info">View</a>
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

<!-- Add Balance Modal removed -->

<!-- Vehicle Info Modal -->
<div class="modal fade" id="vehicleInfoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Vehicle Information</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="vehicleInfoForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Vehicle Brand</label>
                        <input type="text" name="vehicle_brand" class="form-control" value="<?= isset($vehicle_brand) ? $vehicle_brand : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Vehicle Model</label>
                        <input type="text" name="vehicle_model" class="form-control" value="<?= isset($vehicle_model) ? $vehicle_model : '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Plate Number</label>
                        <input type="text" name="vehicle_plate_number" class="form-control" value="<?= isset($vehicle_plate_number) ? $vehicle_plate_number : '' ?>">
                            </div>
                            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Vehicle Info</button>
                        </div>
                    </form>
                </div>
            </div>
</div>

<!-- OR/CR Upload Modal -->
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
                        location.reload();
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
                        location.reload();
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
                        location.reload();
                    }else{
                        alert_toast(resp.msg,'error');
                    }
                    end_loader();
                }
            });
        });
    })
    
    // showAddBalance removed
    
    function showVehicleInfo() {
        $('#vehicleInfoModal').modal('show');
    }
    
    function showORCRUpload() {
        $('#orcrUploadModal').modal('show');
    }
</script>