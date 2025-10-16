<?php 
require_once('config.php');

// Check if this is being called as a standalone page or as modal content
$is_standalone = isset($_GET['p']) && $_GET['p'] == 'send_request';

if ($is_standalone) {
    // This is a standalone page request, include full HTML structure
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <?php require_once('inc/header.php') ?>
    <body>
    <?php require_once('inc/topBarNav.php') ?>

    <!-- Header-->
    <!-- <header class="bg-dark py-5" id="main-header">
        <div class="container h-100 d-flex align-items-end justify-content-center w-100">
            <div class="text-center text-white w-100">
                <h1 class="display-4 fw-bolder">Service Request</h1>
                <p class="lead fw-normal text-white-50 mb-0">Request service for your vehicle</p>
            </div>
        </div>
    </header> -->

    <!-- Section-->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card card-outline card-primary shadow rounded-0">
                        <div class="card-header">
                            <h4 class="card-title"><b>Service Request Form</b></h4>
                        </div>
                        <div class="card-body">
    <?php
}

// Get available services
$services = $conn->query("SELECT * FROM service_list WHERE status = 1 ORDER BY service ASC");

// Get user's existing service requests
$user_requests = $conn->query("SELECT * FROM service_requests WHERE client_id = '{$_settings->userdata('id')}' ORDER BY date_created DESC");
?>

<style>
    #uni_modal .modal-footer{
        display:none
    }
    span.select2-selection.select2-selection--single,span.select2-selection.select2-selection--multiple {
        padding: 0.25rem 0.5rem;
        min-height: calc(1.5em + 0.5rem + 2px);
        height:auto !important;
        max-height:calc(3.5em + 0.5rem + 2px);
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0;
    }
    .error-msg {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.25rem;
    }
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    .form-control.is-valid {
        border-color: #28a745;
    }
</style>

<?php if (!$is_standalone): ?>
<div class="container-fluid">
<?php endif; ?>
    <form action="" id="request_form">
        <input type="hidden" name="id">
        <input type="hidden" name="client_id" value="<?= $_settings->userdata('id') ?>">
        
        <div class="row">
            <div class="form-group col-md-6">
                <label for="vehicle_type" class="control-label">Vehicle Type *</label>
                <select name="vehicle_type" id="vehicle_type" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" required>
                    <option value="">Select Vehicle Type</option>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                </select>
                <div class="error-msg" id="vehicle_type_error"></div>
            </div>
            <div class="form-group col-md-6">
                <label for="vehicle_registration_number" class="control-label">Vehicle Registration Number *</label>
                <input type="text" name="vehicle_registration_number" id="vehicle_registration_number" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" required 
                       pattern="[A-Z]{3}[0-9]{3}|[A-Z]{2}[0-9]{3}[A-Z]{2}|[A-Z]{1}[0-9]{3}[A-Z]{3}" 
                       placeholder="e.g., ABC123, AB123CD, A123BCD" maxlength="7">
                <div class="error-msg" id="vehicle_registration_number_error"></div>
                <small class="form-text text-muted">Format: ABC123, AB123CD, or A123BCD</small>
            </div>
            <div class="form-group col-md-6">
                <label for="vehicle_model" class="control-label">Vehicle Model *</label>
                <select name="vehicle_model" id="vehicle_model" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" required>
                    <option value="">Select Vehicle Model</option>
                    <option value="Honda Click 125i">Honda Click 125i</option>
                    <option value="Honda Click 160">Honda Click 160</option>
                    <option value="Honda RS125">Honda RS125</option>
                    <option value="Honda Scoopy Slant">Honda Scoopy Slant</option>
                    <option value="Honda PCX160">Honda PCX160</option>
                    <option value="Honda ADV160">Honda ADV160</option>
                    <option value="Honda CRF150L">Honda CRF150L</option>
                    <option value="Honda CRF250L">Honda CRF250L</option>
                    <option value="Honda Wave 110i">Honda Wave 110i</option>
                    <option value="Honda Wave 125i">Honda Wave 125i</option>
                    <option value="Honda XRM 125">Honda XRM 125</option>
                    <option value="Other">Other (Specify in notes)</option>
                </select>
                <div class="error-msg" id="vehicle_model_error"></div>
            </div>
            <div class="form-group col-md-12">
                <label for="service_id" class="control-label">Services Required *</label>
                <select name="service_id[]" id="service_id" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?> select2" multiple required>
                    <option value="">Select Services</option>
                    <?php 
                    while($service = $services->fetch_assoc()):
                    ?>
                    <option value="<?php echo $service['id'] ?>"><?php echo $service['service'] ?></option>
                    <?php endwhile; ?>
                </select>
                <div class="error-msg" id="service_id_error"></div>
                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple services</small>
            </div>
            <div class="form-group col-md-12">
                <label for="vehicle_info" class="control-label">Vehicle Information</label>
                <textarea name="vehicle_info" id="vehicle_info" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" rows="3" 
                          placeholder="Please provide details about your vehicle (year, mileage, previous issues, etc.)"></textarea>
            </div>
            <div class="form-group col-md-12">
                <label for="service_description" class="control-label">Service Description *</label>
                <textarea name="service_description" id="service_description" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" rows="3" required
                          placeholder="Please describe the service you need or the problem you're experiencing"></textarea>
                <div class="error-msg" id="service_description_error"></div>
            </div>
            <!-- Preferences -->
            <div class="form-group col-md-4">
                <label for="preferred_mechanic" class="control-label">Preferred Mechanic</label>
                <select id="preferred_mechanic" name="preferred_mechanic" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>">
                    <option value="">No preference</option>
                    <?php 
                    $pref_mechs = $conn->query("SELECT id, name FROM mechanics_list WHERE status = 1 ORDER BY name ASC");
                    while($pm = $pref_mechs->fetch_assoc()): ?>
                        <option value="<?= $pm['id'] ?>"><?= htmlspecialchars($pm['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="preferred_date" class="control-label">Preferred Date</label>
                <input type="date" id="preferred_date" name="preferred_date" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="preferred_time" class="control-label">Preferred Time</label>
                <input type="time" id="preferred_time" name="preferred_time" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>">
            </div>
            
            <!-- Terms and Conditions -->
            <div class="form-group col-md-12">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fa fa-file-text"></i> Terms and Conditions</h6>
                    </div>
                    <div class="card-body" id="terms-content" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa;">
                        <h6>Service Terms and Conditions</h6>
                        <p><strong>1. Service Agreement:</strong> By submitting this service request, you agree to the following terms and conditions.</p>
                        
                        <p><strong>2. Service Scope:</strong> Our mechanics will perform the requested services as specified. Additional work may be required and will be discussed with you before proceeding.</p>
                        
                        <p><strong>3. Pricing:</strong> Service costs will be provided after inspection. All prices are subject to change based on actual work required.</p>
                        
                        <p><strong>4. Payment Terms:</strong> Payment is due upon completion of service. We accept cash, credit cards, and other approved payment methods.</p>
                        
                        <p><strong>5. Warranty:</strong> All service work comes with a 30-day warranty on parts and labor, unless otherwise specified.</p>
                        
                        <p><strong>6. Liability:</strong> We are not responsible for pre-existing damage or wear and tear that may be discovered during service.</p>
                        
                        <p><strong>7. Appointment Scheduling:</strong> Preferred dates and times are subject to availability. We will confirm your appointment within 24 hours.</p>
                        
                        <p><strong>8. Cancellation Policy:</strong> Please provide at least 24 hours notice for appointment cancellations.</p>
                        
                        <p><strong>9. Vehicle Safety:</strong> We reserve the right to refuse service if the vehicle poses a safety risk to our staff or equipment.</p>
                        
                        <p><strong>10. Data Privacy:</strong> Your personal and vehicle information will be kept confidential and used only for service purposes.</p>
                        
                        <p><strong>11. Force Majeure:</strong> We are not liable for delays or cancellations due to circumstances beyond our control.</p>
                        
                        <p><strong>12. Agreement:</strong> By checking the box below, you acknowledge that you have read, understood, and agree to these terms and conditions.</p>
                    </div>
                </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" required disabled>
                    <label class="form-check-label" for="terms_accepted">
                        I have read and agree to the Terms and Conditions above *
                    </label>
                    <div class="error-msg" id="terms_accepted_error"></div>
                </div>
            </div>
            
        </div>
        
        <?php if (!$is_standalone): ?>
        <div class="w-100 d-flex justify-content-end mx-2">
            <div class="col-auto">
                <button class="btn btn-primary btn-sm rounded-0">Submit Request</button>
                <button class="btn btn-dark btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
        <?php else: ?>
        <div class="text-right">
            <button type="button" class="btn btn-info" id="send_service_appointment">Send Service Appointment</button>
            <a href="./" class="btn btn-secondary">Cancel</a>
        </div>
        <?php endif; ?>
    </form>
    
    <?php if ($is_standalone): ?>
    <!-- My Service Requests -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-outline card-info shadow rounded-0">
                <div class="card-header">
                    <h4 class="card-title"><b>My Service Requests</b></h4>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                                        <?php 
                            // Get mechanic information for the user's service requests
                            $mechanic = $conn->query("SELECT * FROM mechanics_list where id in (SELECT mechanic_id FROM `service_requests` where client_id = '{$_settings->userdata('id')}')");
                            $mechanic_arr = $mechanic && $mechanic->num_rows>0 ? array_column($mechanic->fetch_all(MYSQLI_ASSOC),'name','id') : [];
                            
                            // Reset the query result pointer
                            $user_requests->data_seek(0);
                            
                            while($row = $user_requests->fetch_assoc()):
                                // Status badge logic
                                $status_badge = '<span class="badge badge-secondary rounded-pill px-3">Pending</span>';
                                if($row['status'] == 1) $status_badge = '<span class="badge badge-primary rounded-pill px-3">Confirmed</span>';
                                elseif($row['status'] == 2) $status_badge = '<span class="badge badge-warning rounded-pill px-3">On-progress</span>';
                                elseif($row['status'] == 3) $status_badge = '<span class="badge badge-success rounded-pill px-3">Done</span>';
                                elseif($row['status'] == 4) $status_badge = '<span class="badge badge-danger rounded-pill px-3">Cancelled</span>';
                            ?>
                            <div class="col-md-6 mb-3">
                                <div class="card border rounded shadow-sm h-100">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">Request #<?= $row['id'] ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="text-muted small">Date Requested</div>
                                            <div class="font-weight-bold"><?= date("M d, Y h:i A", strtotime($row['date_created'])) ?></div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="text-muted small">Service Type</div>
                                            <div class="font-weight-bold"><?= isset($row['service_type']) ? $row['service_type'] : 'N/A' ?></div>
                                        </div>
                                        <?php if(!empty($row['vehicle_name'])): ?>
                                        <div class="mb-2">
                                            <div class="text-muted small">Vehicle</div>
                                            <div><?= $row['vehicle_name'] ?></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if(!empty($row['vehicle_registration_number'])): ?>
                                        <div class="mb-2">
                                            <div class="text-muted small">Plate Number</div>
                                            <div><?= $row['vehicle_registration_number'] ?></div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="mb-2">
                                            <div class="text-muted small">Mechanic</div>
                                            <div><?= isset($mechanic_arr[$row['mechanic_id']]) ? $mechanic_arr[$row['mechanic_id']] : 'Not Assigned' ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-muted small">Status</div>
                                            <div><?= $status_badge ?></div>
                                        </div>
                                        <?php if($row['status'] == 0): ?>
                                        <div class="text-center">
                                            <button class="btn btn-sm btn-outline-danger cancel_service_request" data-id="<?= $row['id'] ?>">
                                                <i class="fa fa-times"></i> Cancel Request
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                                <?php endwhile; ?>
                            <?php if($user_requests->num_rows <= 0): ?>
                            <div class="col-12 text-center text-muted">No service requests yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

<?php if (!$is_standalone): ?>
</div>
<?php endif; ?>
<script>
    $(function(){
        $('.select2').select2({
            placeholder:"Please Select Services",
            <?php if (!$is_standalone): ?>
            dropdownParent: $('#uni_modal')
            <?php else: ?>
            width: '100%'
            <?php endif; ?>
        });

        // Form validation functions
        function validateVehicleType() {
            var vehicleType = $('#vehicle_type').val();
            if (!vehicleType) {
                $('#vehicle_type').addClass('is-invalid');
                $('#vehicle_type_error').text('Please select a vehicle type');
                return false;
            } else {
                $('#vehicle_type').removeClass('is-invalid').addClass('is-valid');
                $('#vehicle_type_error').text('');
                return true;
            }
        }


        function validateRegistrationNumber() {
            var regNumber = $('#vehicle_registration_number').val().trim().toUpperCase();
            var pattern = /^[A-Z]{3}[0-9]{3}$|^[A-Z]{2}[0-9]{3}[A-Z]{2}$|^[A-Z]{1}[0-9]{3}[A-Z]{3}$/;
            
            if (!regNumber) {
                $('#vehicle_registration_number').addClass('is-invalid');
                $('#vehicle_registration_number_error').text('Registration number is required');
                return false;
            } else if (!pattern.test(regNumber)) {
                $('#vehicle_registration_number').addClass('is-invalid');
                $('#vehicle_registration_number_error').text('Please enter a valid registration number format');
                return false;
            } else {
                $('#vehicle_registration_number').removeClass('is-invalid').addClass('is-valid');
                $('#vehicle_registration_number_error').text('');
                return true;
            }
        }

        function validateVehicleModel() {
            var model = $('#vehicle_model').val().trim();
            if (model.length < 2) {
                $('#vehicle_model').addClass('is-invalid');
                $('#vehicle_model_error').text('Vehicle model must be at least 2 characters long');
                return false;
            } else if (model.length > 50) {
                $('#vehicle_model').addClass('is-invalid');
                $('#vehicle_model_error').text('Vehicle model must not exceed 50 characters');
                return false;
            } else {
                $('#vehicle_model').removeClass('is-invalid').addClass('is-valid');
                $('#vehicle_model_error').text('');
                return true;
            }
        }

        function validateServices() {
            var services = $('#service_id').val();
            if (!services || services.length === 0) {
                $('#service_id').addClass('is-invalid');
                $('#service_id_error').text('Please select at least one service');
                return false;
            } else {
                $('#service_id').removeClass('is-invalid').addClass('is-valid');
                $('#service_id_error').text('');
                return true;
            }
        }

        function validateServiceDescription() {
            var description = $('#service_description').val().trim();
            if (description.length < 10) {
                $('#service_description').addClass('is-invalid');
                $('#service_description_error').text('Service description must be at least 10 characters long');
                return false;
            } else {
                $('#service_description').removeClass('is-invalid').addClass('is-valid');
                $('#service_description_error').text('');
                return true;
            }
        }

        function validateTermsAccepted() {
            var termsAccepted = $('#terms_accepted').is(':checked');
            if (!termsAccepted) {
                $('#terms_accepted').addClass('is-invalid');
                $('#terms_accepted_error').text('You must accept the terms and conditions');
                return false;
            } else {
                $('#terms_accepted').removeClass('is-invalid').addClass('is-valid');
                $('#terms_accepted_error').text('');
                return true;
            }
        }

        // Terms and conditions scroll validation
        let hasScrolledToBottom = false;
        $('#terms-content').on('scroll', function() {
            const element = $(this);
            const scrollTop = element.scrollTop();
            const scrollHeight = element[0].scrollHeight;
            const clientHeight = element.height();
            
            // Check if user has scrolled to bottom (with 10px tolerance)
            if (scrollTop + clientHeight >= scrollHeight - 10) {
                if (!hasScrolledToBottom) {
                    hasScrolledToBottom = true;
                    $('#terms_accepted').prop('disabled', false);
                    alert_toast('Terms and conditions checkbox is now enabled. Please read and accept to continue.', 'info');
                }
            }
        });

        // Real-time validation
        $('#vehicle_type').change(validateVehicleType);
        $('#vehicle_registration_number').on('input', function() {
            $(this).val($(this).val().toUpperCase());
            validateRegistrationNumber();
        });
        $('#vehicle_model').change(validateVehicleModel);
        $('#service_id').change(validateServices);
        $('#service_description').on('input', validateServiceDescription);
        $('#terms_accepted').change(validateTermsAccepted);

        // Form submission
        $('#request_form').submit(function(e){
            e.preventDefault();
            
            // Validate all fields
            var isValid = true;
            isValid &= validateVehicleType();
            isValid &= validateRegistrationNumber();
            isValid &= validateVehicleModel();
            isValid &= validateServices();
            isValid &= validateServiceDescription();
            isValid &= validateTermsAccepted();
            
            if (!isValid) {
                alert_toast('Please correct the validation errors before submitting.', 'error');
                return false;
            }
            
            start_loader();
            
            var formData = new FormData($(this)[0]);
            
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=save_request',
                method: 'POST',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp){
                    end_loader();
                    if(resp.status == 'success'){
                        alert_toast(resp.msg || 'Service request submitted successfully!', 'success');
                        <?php if ($is_standalone): ?>
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                        <?php else: ?>
                        setTimeout(function(){
                            location.href = "./?p=my_services";
                        }, 2000);
                        <?php endif; ?>
                    } else {
                        alert_toast(resp.msg || 'An error occurred', 'error');
                    }
                }
            });
        });

        // Send Service Appointment button
        $('#send_service_appointment').click(function(){
            // Collect preferred schedule data
            var preferredMechanic = $('#preferred_mechanic').val() || '';
            var preferredDate = $('#preferred_date').val() || '';
            var preferredTime = $('#preferred_time').val() || '';

            if(!preferredDate || !preferredTime){
                alert_toast('Please select preferred date and time.','warning');
                return;
            }

            start_loader();
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=save_appointment',
                method: 'POST',
                data: {
                    client_id: '<?= $_settings->userdata('id') ?>',
                    service_type: ($('#service_id').val()||[])[0] || '',
                    mechanic_id: preferredMechanic,
                    appointment_date: preferredDate,
                    appointment_time: preferredTime,
                    vehicle_info: $('#vehicle_info').val()||'',
                    notes: $('#service_description').val()||''
                },
                dataType: 'json',
                success: function(resp){
                    end_loader();
                    if(resp.status == 'success'){
                        alert_toast('Service appointment sent successfully!','success');
                    } else {
                        alert_toast(resp.msg || 'Failed to send appointment.','error');
                    }
                },
                error: function(){
                    end_loader();
                    alert_toast('An error occurred.','error');
                }
            });
        });
        
        // Handle cancel service request button clicks
        $('.cancel_service_request').click(function(){
            var request_id = $(this).data('id');
            cancelServiceRequest(request_id);
        });

    });
    
    function cancelServiceRequest(request_id){
        _conf("Are you sure you want to cancel this service request?","cancel_service_request",[request_id]);
    }
    
    function cancel_service_request(request_id){
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=cancel_service",
            method: "POST",
            data: {id: request_id},
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    alert_toast("Service request cancelled successfully.", 'success');
                    location.reload();
                } else {
                    alert_toast(resp.msg || "An error occurred.", 'error');
                }
                end_loader();
            }
        });
    }
</script>

<?php if ($is_standalone): ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .select2-container {
        width: 100% !important;
    }
    </style>

    <?php require_once('inc/footer.php') ?>
    </body>
    </html>
<?php endif; ?>