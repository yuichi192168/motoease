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
  <!-- Category A - Red -->
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

  <!-- Category B - Yellow -->
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

  <!-- Category C - Blue -->
  <div class="col-md-4">
    <div class="info-box bg-primary">
      <span class="info-box-icon"><i class="far fa-star"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Category C Items</span>
        <span class="info-box-number" id="category_c_count">0</span>
        <span class="info-box-text">Low Value (5% of total value)</span>
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

<style>
/* Make the View Stock Details modal larger and resizable */
#uni_modal .modal-dialog.large {
    max-width: 90% !important;
    width: 90% !important;
    height: 90vh !important;
}

#uni_modal .modal-content {
    height: 100% !important;
    display: flex;
    flex-direction: column;
}

#uni_modal .modal-body {
    flex: 1;
    overflow-y: auto;
    max-height: calc(90vh - 120px);
}

#uni_modal .modal-dialog.large .modal-content {
    resize: both;
    overflow: auto;
    min-width: 800px;
    min-height: 600px;
}

<style>
/* ABC Analysis Color Indicators */
.abc-category-a {
    background-color: #E76F51 !important;
    color: white !important;
}

.abc-category-b {
    background-color: #E9C56A !important;
    color: #333 !important;
}

.abc-category-c {
    background-color: #F4A261 !important;
    color: white !important;
}

/* Stock Status Color Indicators */
.stock-overstock {
    background-color: #E76F51 !important;
    color: white !important;
}

.stock-low {
    background-color: #E9C56A !important;
    color: #333 !important;
}

.stock-out {
    background-color: #F4A261 !important;
    color: white !important;
}

.stock-normal {
    background-color: #28a745 !important;
    color: white !important;
}

/* Info Box Color Updates - ABC Analysis Categories */
.info-box.bg-danger {
    background-color: #dc3545 !important; /* Category A - Red */
    color: white !important;
}

.info-box.bg-warning {
    background-color: #ffc107 !important; /* Category B - Yellow */
    color: #212529 !important;
}

.info-box.bg-primary {
    background-color: #17A2B8 !important; /* Category C - Teal */
    color: white !important;
}

/* Alert Color Updates */
.alert-danger {
    background-color: #E76F51 !important;
    border-color: #E76F51 !important;
    color: white !important;
}

.alert-warning {
    background-color: #E9C56A !important;
    border-color: #E9C56A !important;
    color: #333 !important;
}

.alert-info {
    background-color: #F4A261 !important;
    border-color: #F4A261 !important;
    color: white !important;
}
</style>

<style>
/* Refresh Analysis button color override */
#refresh_analysis,
#refresh_analysis:focus,
#refresh_analysis:active {
    background-color: #17A2B8 !important;
    border-color: #17A2B8 !important;
    color: #ffffff !important;
}
#refresh_analysis:hover {
    background-color: #17A2B8 !important;
    border-color: #17A2B8 !important;
    color: #ffffff !important;
}
</style>

<script>
$(document).ready(function(){
	// Load ABC analysis data on page load
	loadABCAnalysis();


	// Auto-refresh every 30 seconds to ensure real-time data
	setInterval(function(){
		loadABCAnalysis();
	}, 30000); // Refresh every 30 seconds

	// Refresh analysis button
	$('#refresh_analysis').click(function(){
		start_loader();
		loadABCAnalysis();
		setTimeout(function(){
			end_loader();
			alert_toast('ABC Analysis refreshed with latest stock data','success');
		}, 500);
	});

	// Auto classify button
	$('#auto_classify').click(function(){
		if(confirm('This will automatically classify all products based on sales value and current inventory. Continue?')){
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
				console.log('ABC Analysis Error:', err);
				alert_toast("An error occurred loading ABC analysis. Check console for details.",'error');
			},
			success: function(resp){
				console.log('ABC Analysis Response:', resp);
				if(resp && resp.status == 'success'){
					// Update last refresh time
					var now = new Date();
					var timeString = now.toLocaleTimeString();
					$('#refresh_analysis').html('<span class="fas fa-sync"></span> Refresh Analysis<br><small>Last: ' + timeString + '</small>');
					
					// Update category counts
					$('#category_a_count').text(resp.category_stats ? resp.category_stats.A || 0 : 0);
					$('#category_b_count').text(resp.category_stats ? resp.category_stats.B || 0 : 0);
					$('#category_c_count').text(resp.category_stats ? resp.category_stats.C || 0 : 0);

					// Populate table
					var table = $('#abc_analysis_table').DataTable();
					table.clear();
					
					if(!resp.data || resp.data.length === 0){
						table.draw();
						return;
					}
					
					$.each(resp.data, function(index, item){
						var stock_status_class = '';
						var stock_status_text = '';
						
						switch(item.stock_status){
							case 'OUT_OF_STOCK':
								stock_status_class = 'badge badge-danger';
								stock_status_text = 'Out of Stock';
								break;
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
							default:
								stock_status_class = 'badge badge-secondary';
								stock_status_text = 'Unknown';
						}

						var abc_class = '';
						var abc_text = '';
						switch(item.abc_category){
							case 'A':
								abc_class = 'badge badge-danger';
								abc_text = 'Category A';
								break;
							case 'B':
								abc_class = 'badge badge-warning';
								abc_text = 'Category B';
								break;
							case 'C':
								abc_class = 'badge badge-info';
								abc_text = 'Category C';
								break;
							default:
								abc_class = 'badge badge-secondary';
								abc_text = 'Unclassified';
						}

						// Ensure values are properly formatted (handle null/undefined)
						var currentStock = Math.max(0, parseFloat(item.current_stock || 0));
						var availableStock = Math.max(0, parseFloat(item.available_stock || 0));
						
						table.row.add([
							index + 1,
							item.name || 'N/A',
							'<span class="' + abc_class + '">' + abc_text + '</span>',
							'₱' + parseFloat(item.price || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}),
							currentStock.toLocaleString('en-US', {maximumFractionDigits: 0}),
							availableStock.toLocaleString('en-US', {maximumFractionDigits: 0}),
							'<span class="' + stock_status_class + '">' + stock_status_text + '</span>',
							'<a href="?page=inventory/view_stock&id=' + item.id + '" class="btn btn-sm btn-primary">View Stock</a>'
						]);
					});
					
					table.draw();
				}
			}
		});
	}



	// View stock button
	$(document).on('click', '.view_stock', function(){
		var product_id = $(this).data('id');
		uni_modal("View Stock Details", "inventory/view_stock.php?id=" + product_id, "large");
	});

	// Initialize DataTable
	$('#abc_analysis_table').DataTable({
		responsive: true,
		pageLength: 25,
		order: [[2, 'asc'], [3, 'desc']]
	});
});
</script>
