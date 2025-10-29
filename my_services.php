<div class="content py-5 mt-3">
    <div class="container">
        <h3><b>My Services</b></h3>
        <hr>
        
        <!-- Service Type Tabs -->
        <div class="card card-outline card-dark shadow rounded-0 mb-3">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="serviceTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">
                            <i class="fa fa-list"></i> All Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="requests-tab" data-toggle="tab" href="#requests" role="tab" aria-controls="requests" aria-selected="false">
                            <i class="fa fa-tools"></i> Service Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="appointments-tab" data-toggle="tab" href="#appointments" role="tab" aria-controls="appointments" aria-selected="false">
                            <i class="fa fa-calendar"></i> Appointments
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="serviceTabsContent">
            <!-- All Services Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
        <div class="card card-outline card-dark shadow rounded-0">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                            <?php 
                                // Get all service requests and appointments combined
                                $mechanic = $conn->query("SELECT * FROM mechanics_list where id in (SELECT mechanic_id FROM `service_requests` where client_id = '{$_settings->userdata('id')}') OR id in (SELECT mechanic_id FROM `appointments` where client_id = '{$_settings->userdata('id')}')");
                        $mechanic_arr = $mechanic && $mechanic->num_rows>0 ? array_column($mechanic->fetch_all(MYSQLI_ASSOC),'name','id') : [];
                                
                                // Get service requests
                                $service_requests = $conn->query("SELECT *, 'service_request' as type, date_created as sort_date FROM `service_requests` where client_id = '{$_settings->userdata('id')}'");
                                
                                // Get appointments
                                $appointments = $conn->query("SELECT *, 'appointment' as type, date_created as sort_date FROM `appointments` where client_id = '{$_settings->userdata('id')}'");
                                
                                // Combine and sort by date
                                $all_services = [];
                                
                                // Add service requests
                                while($row = $service_requests->fetch_assoc()) {
                                    $all_services[] = $row;
                                }
                                
                                // Add appointments
                                while($row = $appointments->fetch_assoc()) {
                                    $all_services[] = $row;
                                }
                                
                                // Sort by date (newest first)
                                usort($all_services, function($a, $b) {
                                    return strtotime($b['sort_date']) - strtotime($a['sort_date']);
                                });
                                
                                $has_services = false;
                                foreach($all_services as $row):
                                    $has_services = true;
                                    if($row['type'] == 'service_request'):
                                        // Service Request Display
                                        $status_badge = '<span class="badge badge-secondary rounded-pill px-3">Pending</span>';
                                        if($row['status'] == 1) $status_badge = '<span class="badge badge-primary rounded-pill px-3">Confirmed</span>';
                                        elseif($row['status'] == 2) $status_badge = '<span class="badge badge-warning rounded-pill px-3">On-progress</span>';
                                        elseif($row['status'] == 3) $status_badge = '<span class="badge badge-success rounded-pill px-3">Done</span>';
                                        elseif($row['status'] == 4) $status_badge = '<span class="badge badge-danger rounded-pill px-3">Cancelled</span>';
                                ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card border rounded shadow-sm h-100">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">
                                                <i class="fa fa-tools"></i> Service Request #<?= $row['id'] ?>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="text-muted small">Date Requested</div>
                                                <div class="font-weight-bold"><?= date("M d, Y h:i A", strtotime($row['date_created'])) ?></div>
                                            </div>
                                            <?php 
                                            // Fetch service description from meta
                                            $desc = $conn->query("SELECT meta_value FROM request_meta WHERE request_id = '{$row['id']}' AND meta_field = 'service_description'");
                                            $service_description_val = ($desc && $desc->num_rows) ? $desc->fetch_assoc()['meta_value'] : '';
                                            ?>
                                            <?php if(!empty($service_description_val)): ?>
                                            <div class="mb-2">
                                                <div class="text-muted small">Service Description</div>
                                                <div class="font-weight-bold"><?= htmlspecialchars($service_description_val) ?></div>
                                            </div>
                                            <?php endif; ?>
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
                                        </div>
                                        <div class="card-footer bg-light text-right">
                                            <small class="text-muted">Service ID: <?= $row['id'] ?></small>
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-primary view_request" data-id="<?= $row['id'] ?>">
                                                    <i class="fa fa-eye"></i> View Details
                                                </button>
                                                <?php if($row['status'] == 0): ?>
                                                    <button class="btn btn-sm btn-outline-danger cancel_request" data-id="<?= $row['id'] ?>">
                                                        <i class="fa fa-times"></i> Cancel
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                        <!-- Appointment Display -->
                                        <?php
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
                                        <div class="card-header bg-warning text-white">
                                            <h6 class="mb-0">
                                                <i class="fa fa-calendar"></i> Appointment #<?= $row['id'] ?>
                                            </h6>
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
                                <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if(!$has_services): ?>
                                <div class="col-12 text-center text-muted">No services found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Service Requests Tab -->
            <div class="tab-pane fade" id="requests" role="tabpanel" aria-labelledby="requests-tab">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row">
                                <?php 
                                $service_requests = $conn->query("SELECT * FROM `service_requests` where client_id = '{$_settings->userdata('id')}' order by unix_timestamp(date_created) desc ");
                                while($row = $service_requests->fetch_assoc()):
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
                                    <?php 
                                    // Fetch service description from meta
                                    $desc = $conn->query("SELECT meta_value FROM request_meta WHERE request_id = '{$row['id']}' AND meta_field = 'service_description'");
                                    $service_description_val = ($desc && $desc->num_rows) ? $desc->fetch_assoc()['meta_value'] : '';
                                    ?>
                                    <?php if(!empty($service_description_val)): ?>
                                    <div class="mb-2">
                                        <div class="text-muted small">Service Description</div>
                                        <div class="font-weight-bold"><?= htmlspecialchars($service_description_val) ?></div>
                                    </div>
                                    <?php endif; ?>
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
                                </div>
                                <div class="card-footer bg-light text-right">
                                    <small class="text-muted">Service ID: <?= $row['id'] ?></small>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary view_request" data-id="<?= $row['id'] ?>">
                                            <i class="fa fa-eye"></i> View Details
                                        </button>
                                        <?php if($row['status'] == 0): ?>
                                            <button class="btn btn-sm btn-outline-danger cancel_request" data-id="<?= $row['id'] ?>">
                                                <i class="fa fa-times"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                                <?php if($service_requests->num_rows <= 0): ?>
                        <div class="col-12 text-center text-muted">No service requests yet.</div>
                                        <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Appointments Tab -->
            <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                <div class="card card-outline card-warning shadow rounded-0">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="row">
                                <?php 
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
                                        <div class="card-header bg-warning text-white">
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
    </div>
</div>
<script>
    $(document).ready(function(){
        $('.table th, .table td').addClass("align-middle px-2 py-1");
        if($('.table').length > 0){
            $('.table').dataTable();
        }
        
        // Handle view request button clicks
        $('.view_request').click(function(){
            var req_id = $(this).data('id');
            uni_modal("Service Request Details","view_request.php?id="+req_id,'modal-lg');
        });

        // Handle cancel request button clicks
        $('.cancel_request').click(function(){
            var req_id = $(this).data('id');
            _conf("Are you sure you want to cancel this service request?","cancel_service",[req_id]);
        });
        
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