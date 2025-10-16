<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Product Stocks</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="add_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Add New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
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
					<col width="10%">
                    <col width="25%">
					<col width="25%">
					<col width="10%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Product</th>
						<th>Quantity</th>
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
						$qry = $conn->query("SELECT p.*, c.category FROM `product_list` p INNER JOIN categories c ON p.category_id = c.id {$where} ORDER BY (p.`name`) ASC ");
						while($row = $qry->fetch_assoc()):
							$row['stocks'] = $conn->query("SELECT SUM(quantity) FROM stock_list where product_id = '{$row['id']}'")->fetch_array()[0];
							$row['out'] = $conn->query("SELECT SUM(quantity) FROM order_items where product_id = '{$row['id']}' and order_id in (SELECT id FROM order_list where `status` != 5) ")->fetch_array()[0];
							$row['stocks'] = $row['stocks'] > 0 ? $row['stocks'] : 0;
        					$row['out'] = $row['out'] > 0 ? $row['out'] : 0;
							$row['available'] = $row['stocks'] - $row['out'];
							
							// Determine stock status and color
							$stock_status = '';
							$stock_color = '';
							$min_stock = $row['min_stock'] ?? 0;
							$max_stock = $row['max_stock'] ?? 0;
							
							if($row['available'] <= 0) {
								$stock_status = 'Out of Stock';
								$stock_color = '#F4A261'; // Orange
							} elseif($row['available'] <= $min_stock) {
								$stock_status = 'Low Stock';
								$stock_color = '#E9C56A'; // Yellow
							} elseif($row['available'] >= $max_stock && $max_stock > 0) {
								$stock_status = 'Overstock';
								$stock_color = '#E76F51'; // Red
							} else {
								$stock_status = 'Normal';
								$stock_color = '#2ECC71'; // Green
							}
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td>
								<?php echo ucwords($row['name']) ?>
								<br><small class="text-muted"><?php echo $row['category'] ?></small>
							</td>
							<td class="text-right">
								<span class="badge" style="background-color: <?php echo $stock_color; ?>; color: white; padding: 4px 8px; border-radius: 4px;">
									<?= number_format($row['available']) ?>
								</span>
								<br><small class="text-muted"><?php echo $stock_status; ?></small>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item" href="?page=inventory/view_stock&id=<?php echo $row['id'] ?>"><span class="fa fa-boxes text-dark"></span> View Stock</a>
				                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="uni_modal('Add Stock','inventory/manage_stock.php?product_id=<?php echo $row['id'] ?>')"><span class="fa fa-plus text-primary"></span> Add Stock</a>
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
		$('#add_new').click(function(){
			uni_modal("Add New Stock","inventory/manage_stock.php")
		})
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this product permanently?","delete_product",[$(this).attr('data-id')])
		})
        $('.table th, .table td').addClass("align-middle px-2 py-1")
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