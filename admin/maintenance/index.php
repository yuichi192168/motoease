<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Maintenance Dashboard</h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="row">
				<!-- <div class="col-md-3">
					<div class="info-box bg-primary">
						<span class="info-box-icon"><i class="fas fa-copyright"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Brands</span>
							<span class="info-box-number">
								<?php 
									$brands = $conn->query("SELECT COUNT(id) as total FROM brand_list WHERE delete_flag = 0")->fetch_assoc()['total'];
									echo number_format($brands);
								?>
							</span>
						</div>
					</div>
				</div> -->
				<div class="col-md-3">
					<div class="info-box bg-success">
						<span class="info-box-icon"><i class="fas fa-th-list"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Categories</span>
							<span class="info-box-number">
								<?php 
									$categories = $conn->query("SELECT COUNT(id) as total FROM categories WHERE delete_flag = 0")->fetch_assoc()['total'];
									echo number_format($categories);
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-warning">
						<span class="info-box-icon"><i class="fas fa-tools"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Services</span>
							<span class="info-box-number">
								<?php 
									$services = $conn->query("SELECT COUNT(id) as total FROM service_list WHERE delete_flag = 0")->fetch_assoc()['total'];
									echo number_format($services);
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-info">
						<span class="info-box-icon"><i class="fas fa-users-cog"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Mechanics</span>
							<span class="info-box-number">
								<?php 
									$mechanics = $conn->query("SELECT COUNT(id) as total FROM mechanics_list WHERE delete_flag = 0")->fetch_assoc()['total'];
									echo number_format($mechanics);
								?>
							</span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="row mt-4">
				<div class="col-md-6">
					<div class="card card-outline card-primary">
						<div class="card-header">
							<h3 class="card-title">Quick Actions</h3>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<a href="?page=maintenance/brands" class="btn btn-primary btn-block mb-2">
										<i class="fas fa-copyright"></i> Manage Brands
									</a>
								</div>
								<div class="col-md-6">
									<a href="?page=maintenance/category" class="btn btn-success btn-block mb-2">
										<i class="fas fa-th-list"></i> Manage Categories
									</a>
								</div>
								<div class="col-md-6">
									<a href="?page=maintenance/services" class="btn btn-warning btn-block mb-2">
										<i class="fas fa-tools"></i> Manage Services
									</a>
								</div>
								<div class="col-md-6">
									<a href="?page=mechanics" class="btn btn-info btn-block mb-2">
										<i class="fas fa-users-cog"></i> Manage Mechanics
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="card card-outline card-secondary">
						<div class="card-header">
							<h3 class="card-title">Recent Activity</h3>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-sm">
									<thead>
										<tr>
											<th>Type</th>
											<th>Name</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$recent_brands = $conn->query("SELECT name, status FROM brand_list WHERE delete_flag = 0 ORDER BY date_created DESC LIMIT 3");
										while($brand = $recent_brands->fetch_assoc()):
										?>
										<tr>
											<td><span class="badge badge-primary">Brand</span></td>
											<td><?= $brand['name'] ?></td>
											<td><?= $brand['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?></td>
										</tr>
										<?php endwhile; ?>
										
										<?php 
										$recent_categories = $conn->query("SELECT category, status FROM categories WHERE delete_flag = 0 ORDER BY date_created DESC LIMIT 3");
										while($category = $recent_categories->fetch_assoc()):
										?>
										<tr>
											<td><span class="badge badge-success">Category</span></td>
											<td><?= $category['category'] ?></td>
											<td><?= $category['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?></td>
										</tr>
										<?php endwhile; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
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