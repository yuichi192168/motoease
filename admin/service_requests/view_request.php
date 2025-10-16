<?php 
require_once('./../../config.php');
$qry = $conn->query("SELECT s.*,concat(c.lastname,', ', c.firstname,' ',c.middlename) as fullname,c.email,c.contact FROM `service_requests` s inner join client_list c on s.client_id = c.id where s.id = '{$_GET['id']}' ");
foreach($qry->fetch_array() as $k => $v){
    $$k = $v;
}
$meta = $conn->query("SELECT * FROM `request_meta` where request_id = '{$id}'");
while($row = $meta->fetch_assoc()){
    ${$row['meta_field']} = $row['meta_value'];
}
$services = null;
if(isset($service_id) && !empty($service_id) && $service_id !== '0'){
    $service_ids = array_filter(array_map('intval', explode(',', $service_id)));
    if(!empty($service_ids)){
        $service_ids_str = implode(',', $service_ids);
        $services = $conn->query("SELECT * FROM service_list where id in ({$service_ids_str}) ");
    }
}
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
                <dt><b>Vehicle Type</b></dt>
                <dd class="pl-2"><?php echo isset($vehicle_type) ? $vehicle_type : 'N/A' ?></dd>
                <dt><b>Client Name</b></dt>
                <dd class="pl-2"><?php echo $fullname ?></dd>
                <dt><b>Owner Contact</b></dt>
                <dd class="pl-2"><?php echo $contact ?></dd>
                <dt><b>Owner Email</b></dt>
                <dd class="pl-2"><?php echo $email ?></dd>
                
                <dt><b>Status</b></dt>
                <dd class="pl-2">
                    <?php if($status == 1): ?>
                        <span class="badge badge-primary">Confirmed</span>
                    <?php elseif($status == 2): ?>
                        <span class="badge badge-warning">On-progress</span>
                    <?php elseif($status == 3): ?>
                        <span class="badge badge-success">Done</span>
                    <?php elseif($status == 4): ?>
                        <span class="badge badge-danger">Cancelled</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">Pending</span>
                    <?php endif; ?>
                </dd>
            </dl>
        </div>
        <div class="col-sm-6">
            <dl>
                <dt><b>Vehicle Name</b></dt>
                <dd class="pl-2"><?php echo $vehicle_name ?></dd>
                <dt><b>Vehicle Registration Number</b></dt>
                <dd class="pl-2"><?php echo $vehicle_registration_number ?></dd>
                <dt><b>Vehicle Model</b></dt>
                <dd class="pl-2"><?php echo $vehicle_model ?></dd>
                <?php if(isset($service_description) && !empty($service_description)): ?>
                <dt><b>Service Description</b></dt>
                <dd class="pl-2"><?php echo nl2br($service_description) ?></dd>
                <?php endif; ?>
                <dt><b>Service/s:</b></dt>
                <dd class="pl-2">
                    <?php if($services && $services->num_rows > 0): ?>
                    <ul>
                        <?php 
                        while($srow= $services->fetch_assoc()):
                         ?>
                        <li><?php echo $srow['service'] ?></li>
                        <?php endwhile; ?>
                    </ul>
                    <?php else: ?>
                    <span class="text-muted">No services found</span>
                    <?php endif; ?>
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