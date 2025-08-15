<?php
if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

// Get available time slots
$time_slots = [
    '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'
];

// Get user's existing appointments
$user_appointments = $conn->query("SELECT * FROM appointments WHERE client_id = '{$_settings->userdata('id')}' ORDER BY appointment_date DESC");

// Get available services
$services = $conn->query("SELECT * FROM service_list WHERE status = 1 ORDER BY service ASC");

// Get available mechanics
$mechanics = $conn->query("SELECT * FROM mechanics_list WHERE status = 1 ORDER BY name ASC");
?>

<div class="content py-5 mt-3">
    <div class="container">
        <div class="row">
            <!-- Appointment Booking Form -->
            <div class="col-md-8">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>Book an Appointment</b></h4>
                    </div>
                    <div class="card-body">
                        <form id="appointment-form">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="service_type">Service Type *</label>
                                    <select name="service_type" id="service_type" class="form-control" required>
                                        <option value="">Select Service</option>
                                        <?php while($service = $services->fetch_assoc()): ?>
                                        <option value="<?= $service['id'] ?>"><?= $service['service'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="mechanic_id">Preferred Mechanic</label>
                                    <select name="mechanic_id" id="mechanic_id" class="form-control">
                                        <option value="">Any Available Mechanic</option>
                                        <?php while($mechanic = $mechanics->fetch_assoc()): ?>
                                        <option value="<?= $mechanic['id'] ?>"><?= $mechanic['name'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="appointment_date">Preferred Date *</label>
                                    <input type="date" name="appointment_date" id="appointment_date" class="form-control" required min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="appointment_time">Preferred Time *</label>
                                    <select name="appointment_time" id="appointment_time" class="form-control" required>
                                        <option value="">Select Time</option>
                                        <?php foreach($time_slots as $time): ?>
                                        <option value="<?= $time ?>"><?= $time ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="vehicle_info">Vehicle Information</label>
                                    <textarea name="vehicle_info" id="vehicle_info" class="form-control" rows="3" placeholder="Please provide details about your vehicle (brand, model, plate number, issues)"></textarea>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="notes">Additional Notes</label>
                                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Any additional information or special requests"></textarea>
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">Book Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- My Appointments -->
            <div class="col-md-4">
                <div class="card card-outline card-info shadow rounded-0">
                    <div class="card-header">
                        <h4 class="card-title"><b>My Appointments</b></h4>
                    </div>
                    <div class="card-body">
                        <?php if($user_appointments->num_rows > 0): ?>
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
                                    <?php while($appointment = $user_appointments->fetch_assoc()): ?>
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
                
                <!-- Quick Actions -->
                <div class="card card-outline card-success shadow rounded-0 mt-3">
                    <div class="card-header">
                        <h4 class="card-title"><b>Quick Actions</b></h4>
                    </div>
                    <div class="card-body">
                        <a href="./?p=my_services" class="btn btn-info btn-sm btn-block mb-2">
                            <i class="fa fa-tools"></i> View My Services
                        </a>
                        <a href="./?p=send_request" class="btn btn-warning btn-sm btn-block mb-2">
                            <i class="fa fa-plus"></i> Request Service
                        </a>
                        <a href="./?p=manage_account" class="btn btn-primary btn-sm btn-block">
                            <i class="fa fa-user"></i> Manage Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Set minimum date to today
    var today = new Date().toISOString().split('T')[0];
    $('#appointment_date').attr('min', today);
    
    // Handle form submission
    $('#appointment-form').submit(function(e){
        e.preventDefault();
        
        var formData = new FormData($(this)[0]);
        formData.append('client_id', '<?= $_settings->userdata('id') ?>');
        
        start_loader();
        
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=book_appointment",
            method: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("An error occurred", 'error');
                end_loader();
            },
            success: function(resp){
                if(resp.status == 'success'){
                    alert_toast(resp.msg, 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 2000);
                } else {
                    alert_toast(resp.msg, 'error');
                }
                end_loader();
            }
        });
    });
    
    // Check availability when date/time changes
    $('#appointment_date, #appointment_time').change(function(){
        checkAvailability();
    });
    
    function checkAvailability(){
        var date = $('#appointment_date').val();
        var time = $('#appointment_time').val();
        
        if(date && time){
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=check_appointment_availability",
                method: "POST",
                data: {
                    appointment_date: date,
                    appointment_time: time
                },
                dataType: "json",
                success: function(resp){
                    if(resp.status == 'success'){
                        if(resp.available){
                            $('#appointment_time').removeClass('is-invalid').addClass('is-valid');
                            $('.availability-message').remove();
                            $('#appointment_time').after('<small class="text-success availability-message">This time slot is available!</small>');
                        } else {
                            $('#appointment_time').removeClass('is-valid').addClass('is-invalid');
                            $('.availability-message').remove();
                            $('#appointment_time').after('<small class="text-danger availability-message">This time slot is not available. Please choose another time.</small>');
                        }
                    }
                }
            });
        }
    }
});
</script>
