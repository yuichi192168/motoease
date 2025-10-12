<div class="content py-5 mt-3">
    <div class="container">
        <h3><b>My Service Requests</b></h3>
        <hr>
        <div class="card card-outline card-dark shadow rounded-0">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="row">
                            <?php 
                            $mechanic = $conn->query("SELECT * FROM mechanics_list where id in (SELECT mechanic_id FROM `service_requests` where client_id = '{$_settings->userdata('id')}')");
                        $mechanic_arr = $mechanic && $mechanic->num_rows>0 ? array_column($mechanic->fetch_all(MYSQLI_ASSOC),'name','id') : [];
                            $orders = $conn->query("SELECT * FROM `service_requests` where client_id = '{$_settings->userdata('id')}' order by unix_timestamp(date_created) desc ");
                            while($row = $orders->fetch_assoc()):
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
                                </div>
                                <div class="card-footer bg-light text-right">
                                    <small class="text-muted">Service ID: <?= $row['id'] ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if($orders->num_rows <= 0): ?>
                        <div class="col-12 text-center text-muted">No service requests yet.</div>
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
    });
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