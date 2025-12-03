<?php 
require_once('./../../config.php');
$qry = $conn->query("SELECT * FROM promo_images where id = '{$_GET['id']}' ");
$promo_data = $qry->fetch_assoc();
?>
<style>
	#uni_modal .modal-footer{
		display:none
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 text-center mb-3">
			<?php if(!empty($promo_data['image_path'])): ?>
				<img src="<?php echo validate_image($promo_data['image_path']) ?>" alt="Promo Image" class="img-fluid" style="max-height: 500px;">
			<?php else: ?>
				<span class="text-muted">No image available</span>
			<?php endif; ?>
		</div>
		<div class="col-md-12">
			<dl>
				<dt><b>Title</b></dt>
				<dd class="pl-2"><?php echo htmlspecialchars($promo_data['title']) ?></dd>
				<dt><b>Description</b></dt>
				<dd class="pl-2"><?php echo !empty($promo_data['description']) ? nl2br(htmlspecialchars($promo_data['description'])) : 'N/A' ?></dd>
				<dt><b>Date Created</b></dt>
				<dd class="pl-2"><?php echo date("F d, Y h:i A", strtotime($promo_data['date_created'])) ?></dd>
			</dl>
		</div>
	</div>
	<div class="w-100 d-flex justify-content-end mx-2 mt-3">
		<div class="col-auto">
			<button class="btn btn-light btn-sm rounded-0" type="button" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>


