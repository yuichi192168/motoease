<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Service Requests</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="35%">
					<col width="25%">
					<col width="25%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Client Name</th>
						<th>Service</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						$i = 1;
						$qry = $conn->query("SELECT s.*,concat(c.lastname,', ', c.firstname,' ',c.middlename) as fullname from service_requests s inner join client_list c on s.client_id = c.id order by unix_timestamp(s.date_created) desc");
						while($row = $qry->fetch_assoc()):
							// Get service IDs safely
							$sids_result = $conn->query("SELECT meta_value FROM request_meta where request_id = '{$row['id']}' and meta_field = 'service_id'");
							$sids = '';
							if($sids_result && $sids_result->num_rows > 0) {
								$sids_row = $sids_result->fetch_assoc();
								$sids = $sids_row['meta_value'] ?? '';
							}
							
							// Only query services if we have valid service IDs
							$services = null;
							if(!empty($sids) && $sids !== '' && $sids !== '0') {
								// Validate and sanitize service IDs
								$service_ids = array_filter(array_map('intval', explode(',', $sids)));
								if(!empty($service_ids)) {
									$service_ids_str = implode(',', $service_ids);
									$services = $conn->query("SELECT * FROM service_list where id in ({$service_ids_str}) ");
								}
							}
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo ucwords($row['fullname']) ?></td>
							<td>
								<p class="m-0 truncate-3">
								<?php 
									if($services && $services->num_rows > 0) {
										$s = 0;
										while($srow = $services->fetch_assoc()){
											$s++;
											if($s != 1) echo ", ";
											echo $srow['service'];
										}
									} else {
										echo "No services found";
									}
								?>	
								</p>
							</td>
							<td class="text-center">
								<?php if($row['status'] == 1): ?>
									<span class="badge badge-primary rounded-pill px-3">Confirmed</span>
								<?php elseif($row['status'] == 2): ?>
									<span class="badge badge-warning rounded-pill px-3">On-progress</span>
								<?php elseif($row['status'] == 3): ?>
									<span class="badge badge-success rounded-pill px-3">Done</span>
								<?php elseif($row['status'] == 4): ?>
									<span class="badge badge-danger rounded-pill px-3">Cancelled</span>
								<?php else: ?>
									<span class="badge badge-secondary rounded-pill px-3">Pending</span>
								<?php endif; ?>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<div class="card card-outline card-info mt-3">
    <div class="card-header">
        <h3 class="card-title">Booked Appointments</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-bordered table-stripped" id="appointments_table">
                <colgroup>
                    <col width="5%">
                    <col width="20%">
                    <col width="25%">
                    <col width="20%">
                    <col width="15%">
                    <col width="15%">
                </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date Created</th>
                        <th>Client Name</th>
                        <th>Service</th>
                        <th>Mechanic</th>
                        <th>Schedule</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $i = 1;
                        $appt_qry = $conn->query("SELECT a.*, CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) AS fullname FROM appointments a INNER JOIN client_list c ON a.client_id = c.id ORDER BY a.date_created DESC");
                        // Preload services and mechanics maps
                        $services_map = [];
                        $svc_rs = $conn->query("SELECT id, service FROM service_list");
                        while($svc = $svc_rs->fetch_assoc()){ $services_map[$svc['id']] = $svc['service']; }
                        $mech_map = [];
                        $mech_rs = $conn->query("SELECT id, name FROM mechanics_list");
                        while($mm = $mech_rs->fetch_assoc()){ $mech_map[$mm['id']] = $mm['name']; }
                        while($row = $appt_qry->fetch_assoc()):
                            $service_label = isset($services_map[$row['service_type']]) ? $services_map[$row['service_type']] : 'N/A';
                            $mechanic_label = isset($mech_map[$row['mechanic_id']]) ? $mech_map[$row['mechanic_id']] : 'Unassigned';
                            $schedule = date("Y-m-d", strtotime($row['appointment_date'])) . ' ' . date("H:i", strtotime($row['appointment_time']));
                            $status_badge = '<span class="badge badge-secondary rounded-pill px-3">Pending</span>';
                            if($row['status'] == 'confirmed') $status_badge = '<span class="badge badge-primary rounded-pill px-3">Confirmed</span>';
                            elseif($row['status'] == 'cancelled') $status_badge = '<span class="badge badge-danger rounded-pill px-3">Cancelled</span>';
                            elseif($row['status'] == 'completed') $status_badge = '<span class="badge badge-success rounded-pill px-3">Completed</span>';
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                        <td><?php echo ucwords($row['fullname']) ?></td>
                        <td><?php echo $service_label ?></td>
                        <td><?php echo $mechanic_label ?></td>
                        <td><?php echo $schedule ?></td>
                        <td class="text-center"><?php echo $status_badge ?></td>
                        <td align="center">
                            <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                Action
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item view_appt" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item edit_appt" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item delete_appt" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this service request permanently?","delete_service_request",[$(this).attr('data-id')])
		})
		$('.view_data').click(function(){
			uni_modal("Service Request Details","service_requests/view_request.php?id="+$(this).attr('data-id'),'large')
		})
		$('#create_new').click(function(){
			uni_modal("Service Request Details","service_requests/manage_request.php",'large')
		})
		$('.edit_data').click(function(){
			uni_modal("Service Request Details","service_requests/manage_request.php?id="+$(this).attr('data-id'),'large')
		})
		$('.table').dataTable();
		$('#appointments_table').dataTable();
		$('.delete_appt').click(function(){
			_conf("Are you sure to delete this appointment permanently?","delete_appointment",[$(this).attr('data-id')])
		})
		$('.view_appt').click(function(){
			uni_modal("Appointment Details","service_requests/view_appointment.php?id="+$(this).attr('data-id'),'large')
		})
		$('.edit_appt').click(function(){
			uni_modal("Appointment Details","service_requests/manage_appointment.php?id="+$(this).attr('data-id'),'large')
		})
	})
	function delete_appointment($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_appointment",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
	function delete_service_request($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_request",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>