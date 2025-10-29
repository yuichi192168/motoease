<?php 
require_once('./../../config.php');
$qry = $conn->query("SELECT a.*, CONCAT(c.lastname,', ', c.firstname,' ',c.middlename) as fullname, c.email, c.contact FROM `appointments` a INNER JOIN client_list c ON a.client_id = c.id WHERE a.id = '{$_GET['id']}' ");
foreach($qry->fetch_array() as $k => $v){
    $$k = $v;
}
$service = $conn->query("SELECT service FROM service_list WHERE id = '{$service_type}' ")->fetch_assoc();
$mechanic = !empty($mechanic_id) ? $conn->query("SELECT name FROM mechanics_list WHERE id = '{$mechanic_id}' ")->fetch_assoc() : null;
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6">
            <dl>
                <dt><b>Client Name</b></dt>
                <dd class="pl-2"><?php echo ucwords($fullname) ?></dd>
                <dt><b>Contact</b></dt>
                <dd class="pl-2"><?php echo $contact ?></dd>
                <dt><b>Email</b></dt>
                <dd class="pl-2"><?php echo $email ?></dd>
                
                <dt><b>Service</b></dt>
                <dd class="pl-2"><?php echo isset($service['service']) ? $service['service'] : 'N/A' ?></dd>
            </dl>
        </div>
        <div class="col-sm-6">
            <dl>
                <dt><b>Mechanic</b></dt>
                <dd class="pl-2"><?php echo $mechanic ? $mechanic['name'] : 'Unassigned' ?></dd>
                <dt><b>Appointment Date</b></dt>
                <dd class="pl-2"><?php echo date("Y-m-d", strtotime($appointment_date)) ?></dd>
                <dt><b>Appointment Time</b></dt>
                <dd class="pl-2"><?php echo date("H:i", strtotime($appointment_time)) ?></dd>
                <?php if(!empty($vehicle_info)): ?>
                <dt><b>Vehicle Info</b></dt>
                <dd class="pl-2"><?php echo nl2br($vehicle_info) ?></dd>
                <?php endif; ?>
                <?php if(!empty($notes)): ?>
                <dt><b>Notes</b></dt>
                <dd class="pl-2"><?php echo nl2br($notes) ?></dd>
                <?php endif; ?>
                <dt><b>Status</b></dt>
                <dd class="pl-2">
                    <?php 
                    $status_badge = '<span class="badge badge-secondary">Pending</span>';
                    if($status == 'confirmed') $status_badge = '<span class="badge badge-primary">Confirmed</span>';
                    elseif($status == 'cancelled') $status_badge = '<span class="badge badge-danger">Cancelled</span>';
                    elseif($status == 'completed') $status_badge = '<span class="badge badge-success">Completed</span>';
                    echo $status_badge;
                    ?>
                </dd>
            </dl>
        </div>
    </div>
    <div class="w-100 d-flex justify-content-end mx-2">
        <div class="col-auto">
            <button class="btn btn-light btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
