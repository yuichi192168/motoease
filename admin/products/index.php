<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Products</h3>
		<div class="card-tools">
			<a href="?page=products/manage_product" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- Filters -->
			<?php 
				$search = isset($_GET['search']) ? $_GET['search'] : '';
				$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : '';
			?>
			<div class="row mb-3">
				<div class="col-md-6">
					<label for="category_filter">Filter by Category:</label>
					<select id="category_filter" class="form-control form-control-sm">
						<option value="">All Categories</option>
						<?php 
							$cats = $conn->query("SELECT id, category FROM categories WHERE delete_flag = 0 AND status = 1 ORDER BY category");
							while($c = $cats->fetch_assoc()):
						?>
							<option value="<?= $c['id'] ?>" <?= ($category_filter == $c['id'] ? 'selected' : '') ?>><?= $c['category'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="col-md-6">
					<label for="search_filter">Search Products:</label>
					<form id="search_form">
						<div class="input-group input-group-sm">
							<input type="text" name="search" id="search_filter" value="<?= htmlspecialchars($search) ?>" class="form-control form-control-sm" placeholder="Search by product name...">
							<div class="input-group-append">
								<button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
							</div>
						</div>
					</form>
				</div>
			</div>
        <div class="container-fluid">
			<table class="table table-bordered table-stripped">
				<colgroup>
					<col width="5%">
					<col width="30%">
					<col width="25%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Name</th>
						<th>Price</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$where = " WHERE p.delete_flag = 0 ";
						if(!empty($category_filter)){
							$where .= " AND p.category_id = '".$conn->real_escape_string($category_filter)."' ";
						}
						if(!empty($search)){
							$st = $conn->real_escape_string($search);
							$where .= " AND (p.name LIKE '%{$st}%' OR p.description LIKE '%{$st}%') ";
						}
						$qry = $conn->query("SELECT p.* FROM `product_list` p {$where} ORDER BY (p.`name`) ASC");
						while($row = $qry->fetch_assoc()):
							foreach($row as $k=> $v){
								$row[$k] = trim(stripslashes($v));
							}
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo ucwords($row['name']) ?></td>
							<td class="text-right"><?= number_format($row['price'],2) ?></td>
							<td class="text-center">
                                <?php if($row['status'] == 1): ?>
                                    <span class="badge badge-success px-3 rounded-pill">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-3 rounded-pill">Inactive</span>
                                <?php endif; ?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="?page=products/view_product&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item" href="?page=products/manage_product&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
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
<script>
	$(document).ready(function(){
		// Apply filters
		$('#category_filter').change(function(){
			const params = new URLSearchParams(window.location.search);
			const val = $(this).val();
			if(val){ params.set('category_filter', val); } else { params.delete('category_filter'); }
			window.location.search = params.toString();
		});
		$('#search_form').submit(function(e){
			e.preventDefault();
			const params = new URLSearchParams(window.location.search);
			const val = $('#search_filter').val();
			if(val){ params.set('search', val); } else { params.delete('search'); }
			window.location.search = params.toString();
		});
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this product permanently?","delete_product",[$(this).attr('data-id')])
		})
        $('.table th, .table td').addClass("align-middle px-2 py-1")
		$('.table').dataTable();
		$('.table').dataTable();
	})
	function delete_product($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_product",
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