<?php 
require_once('config.php');

// Get available time slots
$time_slots = [
    '08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '13:00', '13:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00'
];

// Get available mechanics
$mechanics = $conn->query("SELECT * FROM mechanics_list WHERE status = 1 ORDER BY name ASC");

// Get user's existing appointments for the next 30 days
$user_appointments = $conn->query("SELECT appointment_date, appointment_time FROM appointments WHERE client_id = '{$_settings->userdata('id')}' AND appointment_date >= CURDATE() AND appointment_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$booked_slots = [];
while($appointment = $user_appointments->fetch_assoc()) {
    $booked_slots[] = $appointment['appointment_date'] . ' ' . $appointment['appointment_time'];
}
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
    
    /* Red and Black Theme for Appointment Form */
    .modal-header {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        border-bottom: 3px solid #dc3545;
    }
    
    .modal-title {
        color: #dc3545;
        font-weight: 700;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    
    .modal-body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .control-label {
        color: #1a1a1a;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .form-control, .form-select, .date-picker {
        border: 2px solid #dc3545;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus, .form-select:focus, .date-picker:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        background: #fff;
    }
    
    .form-control.is-valid {
        border-color: #28a745;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.94-.94 1.44-1.44'/%3e%3c/svg%3e");
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 1.4 1.4 1.4-1.4'/%3e%3c/svg%3e");
    }
    
    span.select2-selection.select2-selection--single,span.select2-selection.select2-selection--multiple {
        padding: 0.5rem 0.75rem;
        min-height: calc(1.5em + 1rem + 4px);
        height:auto !important;
        max-height:calc(3.5em + 1rem + 4px);
        font-size: 0.95rem;
        border-radius: 10px;
        border: 2px solid #dc3545 !important;
    }
    
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--multiple:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .error-msg {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        font-weight: 500;
    }
    
    .form-text {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    
    /* Time Slots Styling */
    .time-slot {
        display: inline-block;
        margin: 3px;
        padding: 10px 15px;
        border: 2px solid #dc3545;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
        color: #dc3545;
        font-weight: 500;
        text-align: center;
        min-width: 80px;
    }
    
    .time-slot:hover {
        border-color: #c82333;
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
    }
    
    .time-slot.selected {
        border-color: #dc3545;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }
    
    .time-slot.disabled {
        background: #f8f9fa;
        color: #6c757d;
        cursor: not-allowed;
        border-color: #dee2e6;
        opacity: 0.6;
    }
    
    .time-slot.disabled:hover {
        transform: none;
        box-shadow: none;
    }
    
    /* Submit Button */
    .btn-primary {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }
    
    .btn-dark {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-dark:hover {
        background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        transform: translateY(-2px);
    }
    
    /* Loading spinner */
    .fa-spinner {
        color: #dc3545;
    }
    
    /* Form sections */
    .form-section {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 2px solid #e9ecef;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .form-section h6 {
        color: #dc3545;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #dc3545;
    }
</style>
<div class="container-fluid">
    <form action="" id="request_form">
        <input type="hidden" name="id">
    <div class="col-12">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="vehicle_type" class="control-label">Vehicle Type</label>
                    <input type="text" name="vehicle_type" id="vehicle_type" class="form-control form-control-sm rounded-0" required>
                </div>
                <div class="form-group">
                    <label for="vehicle_name" class="control-label">Vehicle Name</label>
                    <input type="text" name="vehicle_name" id="vehicle_name" class="form-control form-control-sm rounded-0" required>
                </div>
                <div class="form-group">
                    <label for="vehicle_registration_number" class="control-label">Vehicle Registration Number *</label>
                    <input type="text" name="vehicle_registration_number" id="vehicle_registration_number" class="form-control form-control-sm rounded-0" required 
                           pattern="[A-Z]{3}[0-9]{3}|[A-Z]{2}[0-9]{3}[A-Z]{2}|[A-Z]{1}[0-9]{3}[A-Z]{3}" 
                           placeholder="e.g., ABC123, AB123CD, A123BCD" maxlength="7">
                    <div class="error-msg" id="vehicle_registration_number_error"></div>
                    <small class="form-text text-muted">Format: ABC123, AB123CD, or A123BCD</small>
                </div>
                <div class="form-group">
                    <label for="vehicle_model" class="control-label">Vehicle Model *</label>
                    <input type="text" name="vehicle_model" id="vehicle_model" class="form-control form-control-sm rounded-0" required 
                           pattern="[A-Za-z0-9\s\-\.]+" minlength="2" maxlength="50">
                    <div class="error-msg" id="vehicle_model_error"></div>
                    <small class="form-text text-muted">Enter the exact model name (e.g., Honda Click 160, Honda Dio)</small>
                </div>
                <div class="form-group">
                    <label for="service_id" class="control-label">Service *</label>
                    <select name="service_id" id="service_id" class="form-select form-select-sm select2 rounded-0" required>
                        <option value="">Select a service</option>
                        <?php 
                        $service = $conn->query("SELECT * FROM `service_list` where status = 1 and delete_flag = 0 order by `service` asc");
                        $selected_service_id = isset($_GET['service_id']) ? $_GET['service_id'] : '';
                        while($row = $service->fetch_assoc()):
                        ?>
                        <option value="<?php echo $row['id'] ?>" <?php echo ($selected_service_id == $row['id']) ? 'selected' : ''; ?>><?php echo  $row['service'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="mechanic_id" class="control-label">Preferred Mechanic</label>
                    <select name="mechanic_id" id="mechanic_id" class="form-select form-select-sm select2 rounded-0">
                        <option value="">Any Available Mechanic</option>
                        <?php 
                        while($mechanic = $mechanics->fetch_assoc()):
                        ?>
                        <option value="<?php echo $mechanic['id'] ?>"><?php echo $mechanic['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appointment_date" class="control-label">Preferred Date *</label>
                    <input type="date" name="appointment_date" id="appointment_date" class="date-picker" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                    <small class="form-text text-muted">Select a date within the next 30 days</small>
                </div>
                
                <div class="form-group">
                    <label class="control-label">Available Time Slots *</label>
                    <div id="time_slots_container" class="mt-2">
                        <p class="text-muted">Please select a date first to see available time slots.</p>
                    </div>
                    <input type="hidden" name="appointment_time" id="appointment_time" required>
                </div>
                
                <div class="form-group">
                    <label for="notes" class="control-label">Additional Notes</label>
                    <textarea name="notes" id="notes" class="form-control form-control-sm rounded-0" rows="3" placeholder="Any specific requirements or additional information..."></textarea>
                </div>
                <div class="form-group" style="display:none">
                    <label for="pickup_address" class="control-label">Pick up Address</label>
                    <textarea rows="3" name="pickup_address" id="pickup_address" class="form-control form-control-sm rounded-0" style="resize:none"></textarea>
                </div>
            </div>
        </div>
    </div>
        <div class="w-100 d-flex justify-content-end mx-2">
            <div class="col-auto">
                <button class="btn btn-primary btn-sm rounded-0">Book Appointment</button>
                <button class="btn btn-dark btn-sm rounded-0" type="button" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>
<script>
    var timeSlots = <?php echo json_encode($time_slots); ?>;
    var bookedSlots = <?php echo json_encode($booked_slots); ?>;
    
    $(function(){
        $('.select2').select2({
            placeholder:"Please Select Here",
            dropdownParent: $('#uni_modal')
        })
        
        // Date change handler
        $('#appointment_date').on('change', function() {
            loadTimeSlots();
        });
        
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
            
            // Check if time slot is selected
            if (!$('#appointment_time').val()) {
                alert_toast('Please select a time slot.', 'error');
                isValid = false;
            }
            
            if (!isValid) {
                alert_toast('Please correct the validation errors before submitting.', 'error');
                return false;
            }
            
            start_loader();
            $.ajax({
                url:'classes/Master.php?f=book_appointment',
                method:'POST',
                data:$(this).serialize(),
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occurred",'error');
                    end_loader()
                },
                success:function(resp){
                    end_loader()
                    if(resp.status == 'success'){
                        alert_toast('Appointment booked successfully!', 'success');
                        setTimeout(function(){
                            location.href= "./?p=my_services"
                        }, 2000);
                    }else if(!!resp.msg){
                        alert_toast(resp.msg,'error')
                    }else{
                        alert_toast("An error occurred",'error');
                    }
                }
            })
        })
    })
    
    function loadTimeSlots() {
        var selectedDate = $('#appointment_date').val();
        if (!selectedDate) {
            $('#time_slots_container').html('<p class="text-muted">Please select a date first to see available time slots.</p>');
            return;
        }
        
        var container = $('#time_slots_container');
        container.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading available slots...</div>');
        
        // Check availability for selected date
        $.ajax({
            url: 'classes/Master.php?f=check_appointment_availability',
            method: 'POST',
            data: { date: selectedDate },
            dataType: 'json',
            success: function(resp) {
                if (resp.status == 'success') {
                    var availableSlots = resp.available_slots;
                    var html = '';
                    
                    if (availableSlots.length > 0) {
                        html = '<div class="row">';
                        timeSlots.forEach(function(time) {
                            var isAvailable = availableSlots.includes(time);
                            var slotClass = isAvailable ? 'time-slot' : 'time-slot disabled';
                            var slotText = time;
                            
                            if (!isAvailable) {
                                slotText += ' (Booked)';
                            }
                            
                            html += '<div class="col-3 mb-2">';
                            html += '<span class="' + slotClass + '" data-time="' + time + '">' + slotText + '</span>';
                            html += '</div>';
                        });
                        html += '</div>';
                    } else {
                        html = '<p class="text-danger">No available time slots for this date. Please select another date.</p>';
                    }
                    
                    container.html(html);
                    
                    // Add click handlers for available slots
                    $('.time-slot:not(.disabled)').on('click', function() {
                        $('.time-slot').removeClass('selected');
                        $(this).addClass('selected');
                        $('#appointment_time').val($(this).data('time'));
                    });
                } else {
                    container.html('<p class="text-danger">Error loading time slots. Please try again.</p>');
                }
            },
            error: function() {
                container.html('<p class="text-danger">Error loading time slots. Please try again.</p>');
            }
        });
    }
</script>