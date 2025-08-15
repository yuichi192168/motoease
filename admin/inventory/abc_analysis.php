<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">ABC Inventory Analysis</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-info" id="refresh_analysis"><span class="fas fa-sync"></span> Refresh Analysis</button>
			<button class="btn btn-flat btn-warning" id="auto_classify"><span class="fas fa-magic"></span> Auto Classify</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- ABC Category Summary -->
			<div class="row mb-3">
				<div class="col-md-4">
					<div class="info-box bg-danger">
						<span class="info-box-icon"><i class="fas fa-star"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Category A Items</span>
							<span class="info-box-number" id="category_a_count">0</span>
							<span class="info-box-text">High Value (80% of total value)</span>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="info-box bg-warning">
						<span class="info-box-icon"><i class="fas fa-star-half-alt"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Category B Items</span>
							<span class="info-box-number" id="category_b_count">0</span>
							<span class="info-box-text">Medium Value (15% of total value)</span>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="info-box bg-info">
						<span class="info-box-icon"><i class="far fa-star"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Category C Items</span>
							<span class="info-box-number" id="category_c_count">0</span>
							<span class="info-box-text">Low Value (5% of total value)</span>
						</div>
					</div>
				</div>
			</div>

			<!-- Stock Alerts -->
			<div class="row mb-3">
				<div class="col-12">
					<div class="card card-outline card-danger">
						<div class="card-header">
							<h3 class="card-title">Stock Alerts</h3>
						</div>
						<div class="card-body">
							<div id="stock_alerts_container">
								<!-- Stock alerts will be loaded here -->
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- ABC Analysis Table -->
			<div class="row">
				<div class="col-12">
					<div class="card card-outline card-secondary">
						<div class="card-header">
							<h3 class="card-title">ABC Analysis Details</h3>
							<div class="card-tools">
								<select class="form-control form-control-sm" id="category_filter">
									<option value="">All Categories</option>
									<option value="A">Category A</option>
									<option value="B">Category B</option>
									<option value="C">Category C</option>
								</select>
							</div>
						</div>
						<div class="card-body">
							<table class="table table-bordered table-striped" id="abc_analysis_table">
								<thead>
									<tr>
										<th>#</th>
										<th>Product Name</th>
										<th>ABC Category</th>
										<th>Price</th>
										<th>Current Stock</th>
										<th>Available Stock</th>
										<th>Reorder Point</th>
										<th>Max Stock</th>
										<th>Stock Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<!-- Data will be loaded via AJAX -->
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// Load ABC analysis data
	loadABCAnalysis();
	loadStockAlerts();

	// Refresh analysis button
	$('#refresh_analysis').click(function(){
		loadABCAnalysis();
		loadStockAlerts();
	});

	// Auto classify button
	$('#auto_classify').click(function(){
		if(confirm('This will automatically classify all products based on sales value. Continue?')){
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=auto_classify_abc",
				method: "POST",
				dataType: "json",
				error: err => {
					console.log(err);
					alert_toast("An error occurred.",'error');
					end_loader();
				},
				success: function(resp){
					if(resp.status == 'success'){
						alert_toast(resp.msg,'success');
						loadABCAnalysis();
					} else {
						alert_toast(resp.msg,'error');
					}
					end_loader();
				}
			});
		}
	});

	// Category filter
	$('#category_filter').change(function(){
		var category = $(this).val();
		$('#abc_analysis_table').DataTable().column(2).search(category).draw();
	});

	function loadABCAnalysis(){
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=get_abc_analysis",
			method: "POST",
			dataType: "json",
			error: err => {
				console.log(err);
				alert_toast("An error occurred loading ABC analysis.",'error');
			},
			success: function(resp){
				if(resp.status == 'success'){
					// Update category counts
					$('#category_a_count').text(resp.category_stats.A || 0);
					$('#category_b_count').text(resp.category_stats.B || 0);
					$('#category_c_count').text(resp.category_stats.C || 0);

					// Populate table
					var table = $('#abc_analysis_table').DataTable();
					table.clear();
					
					$.each(resp.data, function(index, item){
						var stock_status_class = '';
						var stock_status_text = '';
						
						switch(item.stock_status){
							case 'LOW_STOCK':
								stock_status_class = 'badge badge-warning';
								stock_status_text = 'Low Stock';
								break;
							case 'OVERSTOCK':
								stock_status_class = 'badge badge-info';
								stock_status_text = 'Overstock';
								break;
							case 'NORMAL':
								stock_status_class = 'badge badge-success';
								stock_status_text = 'Normal';
								break;
						}

						var abc_class = '';
						switch(item.abc_category){
							case 'A':
								abc_class = 'badge badge-danger';
								break;
							case 'B':
								abc_class = 'badge badge-warning';
								break;
							case 'C':
								abc_class = 'badge badge-info';
								break;
						}

						table.row.add([
							index + 1,
							item.name,
							'<span class="' + abc_class + '">Category ' + item.abc_category + '</span>',
							'₱' + parseFloat(item.price).toLocaleString(),
							item.current_stock,
							item.available_stock,
							item.reorder_point,
							item.max_stock,
							'<span class="' + stock_status_class + '">' + stock_status_text + '</span>',
							'<button class="btn btn-sm btn-primary view_stock" data-id="' + item.id + '">View Stock</button>'
						]);
					});
					
					table.draw();
				}
			}
		});
	}

	function loadStockAlerts(){
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=get_stock_alerts",
			method: "POST",
			dataType: "json",
			error: err => {
				console.log(err);
			},
			success: function(resp){
				if(resp.status == 'success'){
					var html = '';
					if(resp.alerts.length > 0){
						$.each(resp.alerts, function(index, alert){
							var alert_class = '';
							switch(alert.alert_type){
								case 'LOW_STOCK':
									alert_class = 'alert-warning';
									break;
								case 'OUT_OF_STOCK':
									alert_class = 'alert-danger';
									break;
								case 'OVERSTOCK':
									alert_class = 'alert-info';
									break;
							}
							
							html += '<div class="alert ' + alert_class + ' alert-dismissible">';
							html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
							html += '<h5><i class="icon fas fa-exclamation-triangle"></i> ' + alert.alert_type.replace('_', ' ') + '</h5>';
							html += '<p>' + alert.message + '</p>';
							html += '<small>Product: ' + alert.product_name + ' | Category: ' + alert.abc_category + '</small>';
							html += '<button class="btn btn-sm btn-primary float-right resolve_alert" data-id="' + alert.id + '">Resolve</button>';
							html += '</div>';
						});
					} else {
						html = '<div class="alert alert-success">No stock alerts at this time.</div>';
					}
					$('#stock_alerts_container').html(html);
				}
			}
		});
	}

	// Resolve alert
	$(document).on('click', '.resolve_alert', function(){
		var alert_id = $(this).data('id');
		if(confirm('Mark this alert as resolved?')){
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=resolve_stock_alert",
				method: "POST",
				data: {alert_id: alert_id},
				dataType: "json",
				error: err => {
					console.log(err);
					alert_toast("An error occurred.",'error');
				},
				success: function(resp){
					if(resp.status == 'success'){
						alert_toast(resp.msg,'success');
						loadStockAlerts();
					} else {
						alert_toast(resp.msg,'error');
					}
				}
			});
		}
	});

	// View stock button
	$(document).on('click', '.view_stock', function(){
		var product_id = $(this).data('id');
		uni_modal("View Stock Details", "inventory/view_stock.php?id=" + product_id);
	});

	// Initialize DataTable
	$('#abc_analysis_table').DataTable({
		responsive: true,
		pageLength: 25,
		order: [[2, 'asc'], [3, 'desc']]
	});
});
</script>
