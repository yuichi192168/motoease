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
    <header class="bg-dark py-5" id="main-header">
        <div class="container h-100 d-flex align-items-end justify-content-center w-100">
            <div class="text-center text-white w-100">
                <h1 class="display-4 fw-bolder">Service Request</h1>
                <p class="lead fw-normal text-white-50 mb-0">Request service for your vehicle</p>
            </div>
        </div>
    </header>

    <!-- Section-->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Service Request Form</h3>
                        </div>
                        <div class="card-body">
    <?php
}
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
</style>
<?php if (!$is_standalone): ?>
<div class="container-fluid">
<?php endif; ?>
    <form action="" id="request_form">
        <input type="hidden" name="id">
    <?php if (!$is_standalone): ?>
    <div class="col-12">
        <div class="row">
            <div class="col-md-12">
    <?php else: ?>
    <div class="row">
        <div class="col-md-6">
    <?php endif; ?>
                <div class="form-group mb-3">
                    <label for="vehicle_type" class="control-label">Vehicle Type</label>
                    <input type="text" name="vehicle_type" id="vehicle_type" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="vehicle_name" class="control-label">Vehicle Name</label>
                    <input type="text" name="vehicle_name" id="vehicle_name" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="vehicle_registration_number" class="control-label">Vehicle Registration Number *</label>
                    <input type="text" name="vehicle_registration_number" id="vehicle_registration_number" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" required 
                           pattern="[A-Z]{3}[0-9]{3}|[A-Z]{2}[0-9]{3}[A-Z]{2}|[A-Z]{1}[0-9]{3}[A-Z]{3}" 
                           placeholder="e.g., ABC123, AB123CD, A123BCD" maxlength="7">
                    <div class="error-msg" id="vehicle_registration_number_error"></div>
                    <small class="form-text text-muted">Format: ABC123, AB123CD, or A123BCD</small>
                </div>
                <div class="form-group mb-3">
                    <label for="vehicle_model" class="control-label">Vehicle Model *</label>
                    <input type="text" name="vehicle_model" id="vehicle_model" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" required 
                           pattern="[A-Za-z0-9\s\-\.]+" minlength="2" maxlength="50">
                    <div class="error-msg" id="vehicle_model_error"></div>
                    <small class="form-text text-muted">Enter the exact model name (e.g., Honda Click 160, Yamaha NMAX 155)</small>
                </div>
                <div class="form-group mb-3">
                    <label for="service_id" class="control-label">Services</label>
                    <select name="service_id[]" id="service_id" class="form-select <?php echo $is_standalone ? '' : 'form-select-sm rounded-0'; ?> select2" multiple required>
                        <option disabled></option>
                        <?php 
                        $service = $conn->query("SELECT * FROM `service_list` where status = 1 and delete_flag = 0 order by `service` asc");
                        while($row = $service->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo  $row['service'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group mb-3" style="display:none">
                    <label for="pickup_address" class="control-label">Pick up Address</label>
                    <textarea rows="3" name="pickup_address" id="pickup_address" class="form-control <?php echo $is_standalone ? '' : 'form-control-sm rounded-0'; ?>" style="resize:none"></textarea>
                </div>
    <?php if (!$is_standalone): ?>
            </div>
        </div>
    </div>
        <div class="w-100 d-flex justify-content-end mx-2">
            <div class="col-auto">
                <button class="btn btn-primary btn-sm rounded-0">Submit Request</button>
                <button class="btn btn-dark btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    <?php else: ?>
        </div>
    </div>
        <div class="form-group text-center">
            <button class="btn btn-primary btn-lg">Submit Request</button>
            <a href="./" class="btn btn-secondary btn-lg">Cancel</a>
        </div>
    <?php endif; ?>
    </form>
<?php if (!$is_standalone): ?>
</div>
<?php endif; ?>
<script>
    $(function(){
        $('.select2').select2({
            placeholder:"Please Select Here",
            <?php if (!$is_standalone): ?>
            dropdownParent: $('#uni_modal')
            <?php else: ?>
            width: '100%'
            <?php endif; ?>
        })
        // request type removed

        // Vehicle registration number validation
        $('#vehicle_registration_number').on('input', function() {
            var value = $(this).val();
            var pattern = /^[A-Z]{3}[0-9]{3}$|^[A-Z]{2}[0-9]{3}[A-Z]{2}$|^[A-Z]{1}[0-9]{3}[A-Z]{3}$/;
            
            if (value && !pattern.test(value)) {
                $(this).addClass('is-invalid');
                $('#vehicle_registration_number_error').text('Please enter a valid registration number format');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $('#vehicle_registration_number_error').text('');
            }
        });

        // Vehicle model validation
        $('#vehicle_model').on('input', function() {
            var value = $(this).val();
            
            if (value.length < 2) {
                $(this).addClass('is-invalid');
                $('#vehicle_model_error').text('Vehicle model must be at least 2 characters long');
            } else if (value.length > 50) {
                $(this).addClass('is-invalid');
                $('#vehicle_model_error').text('Vehicle model must not exceed 50 characters');
            } else {
                $(this).removeClass('is-invalid').addClass('is-valid');
                $('#vehicle_model_error').text('');
            }
        });

        $('#request_form').submit(function(e){
            e.preventDefault()
            
            // Validate before submission
            var isValid = true;
            
            // Check vehicle registration number
            var regNumber = $('#vehicle_registration_number').val();
            var regPattern = /^[A-Z]{3}[0-9]{3}$|^[A-Z]{2}[0-9]{3}[A-Z]{2}$|^[A-Z]{1}[0-9]{3}[A-Z]{3}$/;
            if (!regPattern.test(regNumber)) {
                $('#vehicle_registration_number').addClass('is-invalid');
                $('#vehicle_registration_number_error').text('Please enter a valid registration number format');
                isValid = false;
            }
            
            // Check vehicle model
            var model = $('#vehicle_model').val();
            if (model.length < 2 || model.length > 50) {
                $('#vehicle_model').addClass('is-invalid');
                $('#vehicle_model_error').text('Vehicle model must be between 2 and 50 characters');
                isValid = false;
            }
            
            if (!isValid) {
                alert_toast('Please correct the validation errors before submitting.', 'error');
                return false;
            }
            
            start_loader();
            $.ajax({
                url:'classes/Master.php?f=save_request',
                method:'POST',
                data:$(this).serialize(),
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured",'error');
                    end_loader()
                },
                success:function(resp){
                    end_loader()
                    if(resp.status == 'success'){
                        <?php if ($is_standalone): ?>
                        alert_toast('Service request submitted successfully!', 'success');
                        setTimeout(function(){
                            location.href = "./?p=my_services";
                        }, 2000);
                        <?php else: ?>
                        location.href= "./?p=my_services"
                        <?php endif; ?>
                    }else if(!!resp.msg){
                        alert_toast(resp.msg,'error')
                    }else{
                        alert_toast("An error occured",'error');
                    }
                }
            })
        })
    })
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