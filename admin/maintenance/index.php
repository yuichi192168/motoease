<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Packages</h3>
		<div class="card-tools">
			<a href="?page=packages/manage" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-bordered table-stripped">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="35%">
						<col width="10%">
						<col width="15%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Date Created</th>
							<th>Package</th>
							<th>Description</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						try {
							$i = 1;
							$qry = $conn->query("SELECT * from `packages` order by date(date_created) desc ");
							while($row = $qry->fetch_assoc()):
								$row['description'] = strip_tags(stripslashes(html_entity_decode($row['description'])));
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
								<td><?php echo $row['title'] ?></td>
								<td><p class="truncate-1 m-0"><?php echo $row['description'] ?></p></td>
								<td class="text-center">
									<?php if($row['status'] == 1): ?>
										<span class="badge badge-success">Active</span>
									<?php else: ?>
										<span class="badge badge-danger">Inactive</span>
									<?php endif; ?>
								</td>
								<td align="center">
									 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
					                  		Action
					                    <span class="sr-only">Toggle Dropdown</span>
					                  </button>
					                  <div class="dropdown-menu" role="menu">
					                    <a class="dropdown-item" href="?page=packages/manage&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
					                    <div class="dropdown-divider"></div>
					                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
					                  </div>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php if($qry->num_rows <= 0): ?>
						<tr>
							<td colspan="6" class="text-center">No packages found.</td>
						</tr>
						<?php endif; ?>
						<?php } catch (Exception $e) { ?>
						<tr>
							<td colspan="6" class="text-center text-danger">Error loading packages: <?php echo $e->getMessage(); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<style>
/* Fix scrolling issues */
.content-wrapper {
    overflow-y: auto !important;
    height: calc(100vh - 60px) !important;
}

.card-body {
    overflow-x: auto;
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
}

/* Ensure proper spacing */
.info-box {
    margin-bottom: 15px;
}

/* Fix modal scrolling */
.modal-body {
    max-height: 60vh;
    overflow-y: auto;
}

/* Improve table readability */
.table th {
    position: sticky;
    top: 0;
    background: #f4f6f9;
    z-index: 10;
}

/* Better table styling */
.table td {
    vertical-align: middle;
}

.dropdown-menu {
    min-width: 120px;
}
</style>

<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this package permanently?","delete_package",[$(this).attr('data-id')])
		})
		$('.table').dataTable({
			"scrollX": true,
			"scrollY": "400px",
			"scrollCollapse": true,
			"responsive": true
		});
	})
	function delete_package($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_package",
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