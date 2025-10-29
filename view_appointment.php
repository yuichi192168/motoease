<?php
require_once('./config.php');

$appointment = null;
$service = null;
$mechanic = null;
$client = null;

if(isset($_GET['id'])){
	$id = $conn->real_escape_string($_GET['id']);
	$qry = $conn->query("SELECT * FROM `appointments` WHERE id = '{$id}'");
	if($qry && $qry->num_rows > 0){
		$appointment = $qry->fetch_assoc();
		if(!empty($appointment['service_type'])){
			$service = $conn->query("SELECT * FROM `service_list` WHERE id = '{$appointment['service_type']}'");
			$service = $service && $service->num_rows ? $service->fetch_assoc() : null;
		}
		if(!empty($appointment['mechanic_id'])){
			$mechanic = $conn->query("SELECT * FROM `mechanics_list` WHERE id = '{$appointment['mechanic_id']}'");
			$mechanic = $mechanic && $mechanic->num_rows ? $mechanic->fetch_assoc() : null;
		}
		if(!empty($appointment['client_id'])){
			$client = $conn->query("SELECT * FROM `client_list` WHERE id = '{$appointment['client_id']}'");
			$client = $client && $client->num_rows ? $client->fetch_assoc() : null;
		}
	}
}
?>
<style>
	#uni_modal .modal-footer{
		display:none;
	}
</style>
<div class="container-fluid">
	<?php if(!$appointment): ?>
	<div class="text-center text-muted py-3">Appointment not found.</div>
	<?php else: ?>
	<div class="row">
		<div class="col-md-6">
			<label class="text-muted">Appointment ID</label>
			<div class="ml-3"><b>#<?= $appointment['id'] ?></b></div>
		</div>
		<div class="col-md-6">
			<label class="text-muted">Status</label>
			<div class="ml-3">
				<?php 
					$status = strtolower((string)$appointment['status']);
					if($status === 'confirmed'){ echo '<span class="badge badge-primary rounded-pill px-3">Confirmed</span>'; }
					elseif($status === 'in_progress'){ echo '<span class="badge badge-warning rounded-pill px-3">In Progress</span>'; }
					elseif($status === 'completed'){ echo '<span class="badge badge-success rounded-pill px-3">Completed</span>'; }
					elseif($status === 'cancelled'){ echo '<span class="badge badge-danger rounded-pill px-3">Cancelled</span>'; }
					else { echo '<span class="badge badge-secondary rounded-pill px-3">Pending</span>'; }
				?>
			</div>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-md-6">
			<label class="text-muted">Appointment Date</label>
			<div class="ml-3"><b><?= isset($appointment['appointment_date']) ? date('M d, Y', strtotime($appointment['appointment_date'])) : 'N/A' ?></b></div>
		</div>
		<div class="col-md-6">
			<label class="text-muted">Appointment Time</label>
			<div class="ml-3"><b><?= isset($appointment['appointment_time']) ? $appointment['appointment_time'] : 'N/A' ?></b></div>
		</div>
	</div>
	<div class="row mt-2">
		<div class="col-md-6">
			<label class="text-muted">Service Type</label>
			<div class="ml-3"><b><?= $service ? $service['service'] : 'N/A' ?></b></div>
		</div>
		<div class="col-md-6">
			<label class="text-muted">Assigned Mechanic</label>
			<div class="ml-3"><b><?= $mechanic ? $mechanic['name'] : 'Not Assigned' ?></b></div>
		</div>
	</div>
	<?php if(!empty($appointment['vehicle_info'])): ?>
	<div class="row mt-2">
		<div class="col-md-12">
			<label class="text-muted">Vehicle Information</label>
			<div class="ml-3"><b><?= htmlspecialchars($appointment['vehicle_info']) ?></b></div>
		</div>
	</div>
	<?php endif; ?>
	<?php if(!empty($appointment['notes'])): ?>
	<div class="row mt-2">
		<div class="col-md-12">
			<label class="text-muted">Notes</label>
			<div class="ml-3"><b><?= htmlspecialchars($appointment['notes']) ?></b></div>
		</div>
	</div>
	<?php endif; ?>
	<div class="row mt-2">
		<div class="col-md-6">
			<label class="text-muted">Date Created</label>
			<div class="ml-3"><b><?= isset($appointment['date_created']) ? date('M d, Y h:i A', strtotime($appointment['date_created'])) : 'N/A' ?></b></div>
		</div>
		<div class="col-md-6">
			<label class="text-muted">Last Updated</label>
			<div class="ml-3"><b><?= !empty($appointment['date_updated']) ? date('M d, Y h:i A', strtotime($appointment['date_updated'])) : 'N/A' ?></b></div>
		</div>
	</div>
	<?php if($client): ?>
	<div class="row mt-3">
		<div class="col-md-6">
			<label class="text-muted">Client</label>
			<div class="ml-3"><b><?= ucwords($client['firstname'].' '.$client['lastname']) ?></b></div>
		</div>
		<div class="col-md-6">
			<label class="text-muted">Contact</label>
			<div class="ml-3"><b><?= !empty($client['contact']) ? $client['contact'] : 'N/A' ?></b></div>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>
</div>
