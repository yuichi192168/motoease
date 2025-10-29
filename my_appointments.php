<?php
if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}
?>

<div class="content py-5 mt-3">
    <div class="container">
        <h3><b>My Booked Appointments</b></h3>
        <hr>
        <div class="card card-outline card-dark shadow rounded-0">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                        <?php 
                        // Get mechanic names
                        $mechanic = $conn->query("SELECT * FROM mechanics_list where id in (SELECT mechanic_id FROM `appointments` where client_id = '{$_settings->userdata('id')}')");
                        $mechanic_arr = $mechanic && $mechanic->num_rows>0 ? array_column($mechanic->fetch_all(MYSQLI_ASSOC),'name','id') : [];
                        
                        // Get user's appointments
                        $appointments = $conn->query("SELECT * FROM `appointments` where client_id = '{$_settings->userdata('id')}' order by unix_timestamp(appointment_date) desc ");
                        while($row = $appointments->fetch_assoc()):
                            $status_badge = '<span class="badge badge-secondary rounded-pill px-3">Pending</span>';
                            if($row['status'] == 'confirmed') $status_badge = '<span class="badge badge-primary rounded-pill px-3">Confirmed</span>';
                            elseif($row['status'] == 'in_progress') $status_badge = '<span class="badge badge-warning rounded-pill px-3">In Progress</span>';
                            elseif($row['status'] == 'completed') $status_badge = '<span class="badge badge-success rounded-pill px-3">Completed</span>';
                            elseif($row['status'] == 'cancelled') $status_badge = '<span class="badge badge-danger rounded-pill px-3">Cancelled</span>';
                            
                            // Get service name
                            $service = $conn->query("SELECT service FROM service_list WHERE id = '{$row['service_type']}'")->fetch_assoc();
                            $service_name = $service ? $service['service'] : 'N/A';
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border rounded shadow-sm h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Appointment #<?= $row['id'] ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="text-muted small">Appointment Date</div>
                                        <div class="font-weight-bold"><?= date("M d, Y", strtotime($row['appointment_date'])) ?></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="text-muted small">Appointment Time</div>
                                        <div class="font-weight-bold"><?= $row['appointment_time'] ?></div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="text-muted small">Service Type</div>
                                        <div class="font-weight-bold"><?= htmlspecialchars($service_name) ?></div>
                                    </div>
                                    <?php if(!empty($row['vehicle_info'])): ?>
                                    <div class="mb-2">
                                        <div class="text-muted small">Vehicle Information</div>
                                        <div><?= htmlspecialchars($row['vehicle_info']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if(!empty($row['notes'])): ?>
                                    <div class="mb-2">
                                        <div class="text-muted small">Notes</div>
                                        <div><?= htmlspecialchars($row['notes']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="mb-2">
                                        <div class="text-muted small">Assigned Mechanic</div>
                                        <div><?= isset($mechanic_arr[$row['mechanic_id']]) ? $mechanic_arr[$row['mechanic_id']] : 'Not Assigned' ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="text-muted small">Status</div>
                                        <div><?= $status_badge ?></div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light text-right">
                                    <small class="text-muted">Appointment ID: <?= $row['id'] ?></small>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary view_appointment" data-id="<?= $row['id'] ?>">
                                            <i class="fa fa-eye"></i> View Details
                                        </button>
                                        <?php if($row['status'] == 'pending'): ?>
                                            <button class="btn btn-sm btn-outline-danger cancel_appointment" data-id="<?= $row['id'] ?>">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if($appointments->num_rows <= 0): ?>
                        <div class="col-12 text-center text-muted">No appointments booked yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('.table th, .table td').addClass("align-middle px-2 py-1");
        if($('.table').length > 0){
            $('.table').dataTable();
        }
        
        // Handle view appointment button clicks
        $('.view_appointment').click(function(){
            var appt_id = $(this).data('id');
            uni_modal("Appointment Details","view_appointment.php?id="+appt_id,'modal-lg');
        });

        // Handle cancel appointment button clicks
        $('.cancel_appointment').click(function(){
            var appt_id = parseInt($(this).data('id'), 10) || 0;
            _conf("Are you sure you want to cancel this appointment?","cancel_appointment",[appt_id]);
        });
    });
    
    function cancel_appointment($appt_id){
        $appt_id = parseInt($appt_id, 10) || 0;
        if($appt_id <= 0){
            alert_toast("Invalid appointment id.", 'error');
            return;
        }
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=cancel_appointment",
            method: "POST",
            data: {id: $appt_id},
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
    }
</script>

<!-- Modal Structure -->
<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog rounded-0 modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
