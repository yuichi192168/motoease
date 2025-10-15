<?php 
require_once('./../../config.php');
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM appointments WHERE id = '{$_GET['id']}' ");
$appointment = $qry->fetch_assoc();
if($appointment){
    foreach($appointment as $k => $v){ $$k = $v; }
}
}
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
    span.select2-selection.select2-selection--single {
        padding: 0.25rem 0.5rem;
        min-height: calc(1.5em + 0.5rem + 2px);
        height:auto !important;
        font-size: 0.875rem;
        border-radius: 0;
    }
    .form-text { font-size: 0.8rem; }
    .error-msg { color:#dc3545; font-size:0.8rem; margin-top:0.25rem; }
    .select2-container { width:100% !important; }
</style>
<div class="container-fluid">
    <form action="" id="appointment_form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="client_id" class="control-label">Client</label>
                <select name="client_id" id="client_id" class="form-select form-select-sm select2 rounded-0" required>
                    <option value="" disabled <?php echo !isset($client_id) ? 'selected' : '' ?>></option>
                    <?php 
                    $clients = $conn->query("SELECT id, CONCAT(lastname, ', ', firstname, ' ', middlename) AS fullname FROM client_list ORDER BY lastname ASC");
                    while($c = $clients->fetch_assoc()):
                    ?>
                    <option value="<?php echo $c['id'] ?>" <?php echo (isset($client_id) && $client_id == $c['id']) ? 'selected' : '' ?>><?php echo ucwords($c['fullname']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="service_type" class="control-label">Service</label>
                <select name="service_type" id="service_type" class="form-select form-select-sm select2 rounded-0" required>
                    <option value="" disabled <?php echo !isset($service_type) ? 'selected' : '' ?>></option>
                    <?php 
                    $services = $conn->query("SELECT id, service FROM service_list WHERE status = 1 AND delete_flag = 0 ORDER BY service ASC");
                    while($s = $services->fetch_assoc()):
                    ?>
                    <option value="<?php echo $s['id'] ?>" <?php echo (isset($service_type) && $service_type == $s['id']) ? 'selected' : '' ?>><?php echo $s['service'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="mechanic_id" class="control-label">Mechanic</label>
                <select name="mechanic_id" id="mechanic_id" class="form-select form-select-sm select2 rounded-0">
                    <option value="" <?php echo empty($mechanic_id) ? 'selected' : '' ?>></option>
                    <?php 
                    $mechanics = $conn->query("SELECT id, name FROM mechanics_list WHERE status = 1 ORDER BY name ASC");
                    while($m = $mechanics->fetch_assoc()):
                    ?>
                    <option value="<?php echo $m['id'] ?>" <?php echo (isset($mechanic_id) && $mechanic_id == $m['id']) ? 'selected' : '' ?>><?php echo $m['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="appointment_date" class="control-label">Date</label>
                <input type="date" name="appointment_date" id="appointment_date" class="form-control form-control-sm rounded-0" value="<?php echo isset($appointment_date) ? $appointment_date : '' ?>" required>
            </div>
            <div class="form-group col-md-3">
                <label for="appointment_time" class="control-label">Time</label>
                <input type="time" name="appointment_time" id="appointment_time" class="form-control form-control-sm rounded-0" value="<?php echo isset($appointment_time) ? $appointment_time : '' ?>" required>
            </div>
            <div class="form-group col-md-12">
                <label for="vehicle_info" class="control-label">Vehicle Info</label>
                <textarea name="vehicle_info" id="vehicle_info" rows="2" class="form-control form-control-sm rounded-0" placeholder="Optional vehicle details"><?php echo isset($vehicle_info) ? $vehicle_info : '' ?></textarea>
            </div>
            <div class="form-group col-md-12">
                <label for="notes" class="control-label">Notes</label>
                <textarea name="notes" id="notes" rows="2" class="form-control form-control-sm rounded-0" placeholder="Optional notes"><?php echo isset($notes) ? $notes : '' ?></textarea>
            </div>
            <div class="form-group col-md-6">
                <label for="status" class="control-label">Status</label>
                <select name="status" id="status" class="custom-select custom-select-sm rounded-0" required>
                    <?php $st = isset($status) ? $status : 'pending'; ?>
                    <option value="pending" <?php echo $st == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="confirmed" <?php echo $st == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                    <option value="cancelled" <?php echo $st == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    <option value="completed" <?php echo $st == 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
        </div>
        <div class="w-100 d-flex justify-content-end mx-2">
            <div class="col-auto">
                <button class="btn btn-primary btn-sm rounded-0">Save</button>
                <button class="btn btn-light btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>
<script>
$(function(){
    $('.select2').select2({ dropdownParent: $('#uni_modal') });
    $('#appointment_form').submit(function(e){
        e.preventDefault();
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=save_appointment',
            method:'POST',
            data: $(this).serialize(),
            dataType:'json',
            error: err => {
                console.log(err);
                alert_toast('An error occured','error');
                end_loader();
            },
            success: function(resp){
                end_loader();
                if(resp.status == 'success'){
                    alert_toast('Appointment saved','success');
                    setTimeout(()=>{ location.reload(); }, 800);
                }else{
                    alert_toast(resp.msg || 'An error occured','error');
                }
            }
        })
    })
})
</script>
