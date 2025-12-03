<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php 
$role_type = $_settings->userdata('role_type');
// Allow Data Admin and Service Admin to manage promo images.
// Service Receptionist can view promos but cannot edit or create them.
$can_edit = in_array($role_type, ['data_admin', 'service_admin']);
?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
			<h3 class="card-title">Promo Images Management</h3>
			<?php if($can_edit): ?>
			<a href="javascript:void(0)" id="create_new" class="btn btn-primary btn-sm">
				<span class="fas fa-plus"></span> Upload New Promo Image
			</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="container-fluid">
				<table class="table table-bordered table-stripped">
					<colgroup>
						<col width="5%">
						<col width="20%">
						<col width="15%">
						<col width="15%">
						<col width="35%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Image</th>
							<th>Title</th>
							<th>Date Created</th>
							<th>Description</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							$i = 1;
							$qry = $conn->query("SELECT * FROM promo_images WHERE delete_flag = 0 ORDER BY date_created DESC");
							while($row = $qry->fetch_assoc()):
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td class="text-center">
									<?php if(!empty($row['image_path'])): ?>
										<img src="<?php echo validate_image($row['image_path']) ?>" alt="Promo Image" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
									<?php else: ?>
										<span class="text-muted">No Image</span>
									<?php endif; ?>
								</td>
								<td><?php echo htmlspecialchars($row['title']) ?></td>
								<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
								<td><?php echo htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : '') ?></td>
								<td align="center">
									<?php if($can_edit): ?>
									<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
										Action
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
											<span class="fa fa-eye text-primary"></span> View
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
											<span class="fa fa-edit text-primary"></span> Edit
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
											<span class="fa fa-archive text-warning"></span> Archive
										</a>
									</div>
									<?php else: ?>
									<a class="btn btn-flat btn-default btn-sm view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
										<span class="fa fa-eye text-primary"></span> View
									</a>
									<?php endif; ?>
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
		<?php if($can_edit): ?>
		$('.delete_data').click(function(){
			_conf("Are you sure to archive this promo image?","delete_promo_image",[$(this).attr('data-id')])
		})
		$('.edit_data').click(function(){
			uni_modal("Manage Promo Image","promo_images/manage_promo.php?id="+$(this).attr('data-id'),'large')
		})
		$('#create_new').click(function(){
			uni_modal("Upload Promo Image","promo_images/manage_promo.php",'large')
		})
		<?php endif; ?>
		$('.view_data').click(function(){
			uni_modal("Promo Image Details","promo_images/view_promo.php?id="+$(this).attr('data-id'),'large')
		})
		$('.table').dataTable();
	})
	function delete_promo_image($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_promo_image",
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

