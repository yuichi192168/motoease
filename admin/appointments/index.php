<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-warning">
	<div class="card-header">
		<h3 class="card-title">
			<i class="fas fa-calendar-alt"></i> Booked Appointments
		</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_appointment" class="btn btn-flat btn-warning">
				<span class="fas fa-plus"></span> Create New Appointment
			</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-bordered table-stripped" id="appointments_table">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="15%">
						<col width="15%">
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
</div>

<script>
$(document).ready(function(){
	$('#appointments_table').dataTable();
	$('.delete_appt').click(function(){
		_conf("Are you sure to delete this appointment permanently?","delete_appointment",[$(this).attr('data-id')])
	})
	$('.view_appt').click(function(){
		uni_modal("Appointment Details","appointments/view_appointment.php?id="+$(this).attr('data-id'),'large')
	})
	$('#create_appointment').click(function(){
		uni_modal("Appointment Details","appointments/manage_appointment.php",'large')
	})
	$('.edit_appt').click(function(){
		uni_modal("Appointment Details","appointments/manage_appointment.php?id="+$(this).attr('data-id'),'large')
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
</script>
