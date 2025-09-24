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
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="text-muted small">Date Requested</div>
                                    <div class="font-weight-bold"><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></div>
                                </div>
                                <div class="mb-1">
                                    <div class="text-muted small">Mechanic</div>
                                    <div><?= isset($mechanic_arr[$row['mechanic_id']]) ? $mechanic_arr[$row['mechanic_id']] : 'N/A' ?></div>
                                </div>
                                <div class="mb-2">
                                    <div class="text-muted small">Status</div>
                                    <div><?= $status_badge ?></div>
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-sm btn-primary view_data" type="button" data-id="<?= $row['id'] ?>"><i class="fa fa-eye"></i> View</button>
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
    $(function(){
        $('.view_data').click(function(){
            uni_modal("Service Request Details","view_request.php?id="+$(this).attr('data-id'),"mid-large")
        })

        $('.table th, .table td').addClass("align-middle px-2 py-1")
		$('.table').dataTable();
		$('.table').dataTable();
    })
</script>