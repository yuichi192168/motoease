<?php 
require_once('./../../config.php');
$qry = $conn->query("SELECT ci.*, CONCAT(c.lastname, ', ', c.firstname, ' ', c.middlename) as customer_name 
	FROM customer_images ci 
	LEFT JOIN client_list c ON ci.customer_id = c.id 
	WHERE ci.id = '{$_GET['id']}' ");
$customer_data = $qry->fetch_assoc();
?>
<style>
	#uni_modal .modal-footer{
		display:none
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 text-center mb-3">
			<?php if(!empty($customer_data['image_path'])): ?>
				<img src="<?php echo validate_image($customer_data['image_path']) ?>" alt="Customer Image" class="img-fluid" style="max-height: 500px;">
			<?php else: ?>
				<span class="text-muted">No image available</span>
			<?php endif; ?>
		</div>
		<div class="col-md-12">
			<dl>
				<dt><b>Customer Name</b></dt>
				<dd class="pl-2"><?php echo htmlspecialchars($customer_data['customer_name'] ?: 'N/A') ?></dd>
				<dt><b>Description</b></dt>
				<dd class="pl-2"><?php echo !empty($customer_data['description']) ? nl2br(htmlspecialchars($customer_data['description'])) : 'N/A' ?></dd>
				<dt><b>Date Created</b></dt>
				<dd class="pl-2"><?php echo date("F d, Y h:i A", strtotime($customer_data['date_created'])) ?></dd>
			</dl>
		</div>
	</div>
	<div class="w-100 d-flex justify-content-end mx-2 mt-3">
		<div class="col-auto">
			<button class="btn btn-light btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>


