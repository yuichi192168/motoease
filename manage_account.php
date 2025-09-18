<?php 
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

// Account balance and transaction UI removed

// Get OR/CR documents
$documents = $conn->query("SELECT * FROM or_cr_documents WHERE client_id = '{$_settings->userdata('id')}' ORDER BY date_created DESC");
?>
<div class="content py-5 mt-3">
    <div class="container">
        <!-- Quick Actions (balance removed) -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card card-outline card-info shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Quick Actions</b></h4>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-info btn-sm" onclick="showVehicleInfo()">Update Vehicle Info</button>
                        <button class="btn btn-success btn-sm" onclick="showORCRUpload()">Upload OR/CR</button>
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