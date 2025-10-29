<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .motorcycle-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .motorcycle-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        font-weight: bold;
    }
    .variant-item {
        border-bottom: 1px solid #f8f9fa;
        padding: 10px 15px;
        transition: background-color 0.2s;
    }
    .variant-item:hover {
        background-color: #f8f9fa;
    }
    .variant-item:last-child {
        border-bottom: none;
    }
    .stock-indicator {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }
    .stock-high { background-color: #28a745; }
    .stock-medium { background-color: #ffc107; }
    .stock-low { background-color: #dc3545; }
    .stock-out { background-color: #6c757d; }
    .variant-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Motorcycle Inventory - All Variants</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-outline-success" id="export_inventory">
				<i class="fa fa-download"></i> Export
			</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- Filter Section -->
			<div class="filter-section">
				<div class="row">
					<div class="col-md-3">
						<label for="brand_filter">Filter by Brand:</label>
						<select id="brand_filter" class="form-control form-control-sm">
							<option value="">All Brands</option>
							<?php 
							$brands = $conn->query("SELECT DISTINCT b.id, b.name FROM brand_list b 
													INNER JOIN product_list p ON b.id = p.brand_id 
													WHERE b.delete_flag = 0 AND p.delete_flag = 0 
													ORDER BY b.name");
							while($brand = $brands->fetch_assoc()):
							?>
							<option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
							<?php endwhile; ?>
						</select>
					</div>
					<div class="col-md-3">
						<label for="category_filter">Filter by Category:</label>
						<select id="category_filter" class="form-control form-control-sm">
							<option value="">All Categories</option>
							<?php 
							$categories = $conn->query("SELECT DISTINCT c.id, c.category FROM categories c 
														INNER JOIN product_list p ON c.id = p.category_id 
														WHERE c.delete_flag = 0 AND p.delete_flag = 0 
														ORDER BY c.category");
							while($category = $categories->fetch_assoc()):
							?>
							<option value="<?= $category['id'] ?>"><?= $category['category'] ?></option>
							<?php endwhile; ?>
						</select>
					</div>
					<div class="col-md-3">
						<label for="stock_filter">Filter by Stock Level:</label>
						<select id="stock_filter" class="form-control form-control-sm">
							<option value="">All Stock Levels</option>
							<option value="high">High Stock (>10)</option>
							<option value="medium">Medium Stock (5-10)</option>
							<option value="low">Low Stock (1-4)</option>
							<option value="out">Out of Stock (0)</option>
						</select>
					</div>
					<div class="col-md-3">
						<label for="search_filter">Search:</label>
						<input type="text" id="search_filter" class="form-control form-control-sm" placeholder="Search products...">
					</div>
				</div>
			</div>
			
			<!-- Motorcycle Inventory Display -->
			<div id="motorcycle_inventory">
				<?php
				// Get all products grouped by brand and category
				$products_query = "
					SELECT 
						p.*,
						b.name as brand_name,
						b.id as brand_id,
						c.category as category_name,
						c.id as category_id,
						COALESCE(stock_summary.total_stock, 0) as total_stock,
						COALESCE(order_summary.total_ordered, 0) as total_ordered,
						(COALESCE(stock_summary.total_stock, 0) - COALESCE(order_summary.total_ordered, 0)) as available_stock
					FROM product_list p
					INNER JOIN brand_list b ON p.brand_id = b.id
					INNER JOIN categories c ON p.category_id = c.id
					LEFT JOIN (
						SELECT product_id, SUM(quantity) as total_stock 
						FROM stock_list 
						WHERE type = 1 
						GROUP BY product_id
					) stock_summary ON p.id = stock_summary.product_id
					LEFT JOIN (
						SELECT oi.product_id, SUM(oi.quantity) as total_ordered
						FROM order_items oi
						INNER JOIN order_list ol ON oi.order_id = ol.id
						WHERE ol.status != 5
						GROUP BY oi.product_id
					) order_summary ON p.id = order_summary.product_id
					WHERE p.delete_flag = 0 AND p.status = 1
					ORDER BY b.name, c.category, p.name
				";
				
				$products = $conn->query($products_query);
				
				// Group products by brand and category
				$grouped_products = [];
				while($product = $products->fetch_assoc()) {
					$brand_key = $product['brand_name'];
					$category_key = $product['category_name'];
					
					if(!isset($grouped_products[$brand_key])) {
						$grouped_products[$brand_key] = [];
					}
					if(!isset($grouped_products[$brand_key][$category_key])) {
						$grouped_products[$brand_key][$category_key] = [];
					}
					
					$grouped_products[$brand_key][$category_key][] = $product;
				}
				
				// Display grouped products
				foreach($grouped_products as $brand_name => $categories):
					foreach($categories as $category_name => $variants):
						if(!empty($variants)):
				?>
				<div class="motorcycle-card" data-brand="<?= $variants[0]['brand_id'] ?>" data-category="<?= $variants[0]['category_id'] ?>">
					<div class="motorcycle-header">
						<h5 class="mb-0">
							<i class="fa fa-motorcycle"></i> 
							<?= $brand_name ?> - <?= $category_name ?>
							<small class="float-right"><?= count($variants) ?> variant(s)</small>
						</h5>
					</div>
					
					<?php foreach($variants as $variant): ?>
					<div class="variant-item" data-stock="<?= getStockLevel($variant['available_stock']) ?>" data-search="<?= strtolower($variant['name'] . ' ' . $variant['models'] . ' ' . $variant['brand_name'] . ' ' . $variant['category_name']) ?>">
						<div class="row align-items-center">
							<div class="col-md-2">
								<img src="<?= validate_image($variant['image_path']) ?>" alt="<?= $variant['name'] ?>" class="variant-image">
							</div>
							<div class="col-md-4">
								<h6 class="mb-1"><?= $variant['name'] ?></h6>
								<small class="text-muted">Compatible: <?= $variant['models'] ?></small>
							</div>
							<div class="col-md-2">
								<span class="stock-indicator stock-<?= getStockLevel($variant['available_stock']) ?>"></span>
								<span class="font-weight-bold"><?= $variant['available_stock'] ?> units</span>
							</div>
							<div class="col-md-2">
								<span class="text-primary font-weight-bold">₱<?= number_format($variant['price'], 2) ?></span>
							</div>
							<div class="col-md-2 text-right">
								<a href="?page=inventory/view_stock&id=<?= $variant['id'] ?>" class="btn btn-sm btn-outline-primary">
									<i class="fa fa-eye"></i> View
								</a>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				<?php 
						endif;
					endforeach;
				endforeach; 
				
				if(empty($grouped_products)):
				?>
				<div class="text-center py-5">
					<i class="fa fa-box-open fa-3x text-muted mb-3"></i>
					<p class="text-muted">No motorcycle products found in inventory.</p>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// Filter functionality
	function filterInventory() {
		var brandFilter = $('#brand_filter').val();
		var categoryFilter = $('#category_filter').val();
		var stockFilter = $('#stock_filter').val();
		var searchFilter = $('#search_filter').val().toLowerCase();
		
		$('.motorcycle-card').each(function() {
			var card = $(this);
			var showCard = true;
			
			// Brand filter
			if(brandFilter && card.data('brand') != brandFilter) {
				showCard = false;
			}
			
			// Category filter
			if(categoryFilter && card.data('category') != categoryFilter) {
				showCard = false;
			}
			
			// Stock filter
			if(stockFilter) {
				var hasMatchingStock = false;
				card.find('.variant-item').each(function() {
					if($(this).data('stock') === stockFilter) {
						hasMatchingStock = true;
					}
				});
				if(!hasMatchingStock) {
					showCard = false;
				}
			}
			
			// Search filter
			if(searchFilter) {
				var hasMatchingSearch = false;
				card.find('.variant-item').each(function() {
					if($(this).data('search').includes(searchFilter)) {
						hasMatchingSearch = true;
					}
				});
				if(!hasMatchingSearch) {
					showCard = false;
				}
			}
			
			if(showCard) {
				card.show();
			} else {
				card.hide();
			}
		});
	}
	
	// Bind filter events
	$('#brand_filter, #category_filter, #stock_filter').on('change', filterInventory);
	$('#search_filter').on('input', filterInventory);
	
	// Export functionality
	$('#export_inventory').click(function() {
		var visibleCards = $('.motorcycle-card:visible');
		if(visibleCards.length === 0) {
			alert_toast('No inventory to export', 'warning');
			return;
		}
		
		var csv = 'Brand,Category,Product Name,Compatible Models,Available Stock,Price\n';
		visibleCards.each(function() {
			var card = $(this);
			card.find('.variant-item').each(function() {
				var variant = $(this);
				var brand = card.find('.motorcycle-header h5').text().split(' - ')[0].trim();
				var category = card.find('.motorcycle-header h5').text().split(' - ')[1].split(' ')[0].trim();
				var name = variant.find('h6').text();
				var models = variant.find('small').text().replace('Compatible: ', '');
				var stock = variant.find('.font-weight-bold').first().text().replace(' units', '');
				var price = variant.find('.text-primary').text();
				
				csv += '"' + brand + '","' + category + '","' + name + '","' + models + '","' + stock + '","' + price + '"\n';
			});
		});
		
		var blob = new Blob([csv], { type: 'text/csv' });
		var url = window.URL.createObjectURL(blob);
		var a = document.createElement('a');
		a.href = url;
		a.download = 'motorcycle_inventory_' + new Date().toISOString().split('T')[0] + '.csv';
		a.click();
		window.URL.revokeObjectURL(url);
	});
});

function getStockLevel(available) {
	if(available > 10) return 'high';
	if(available > 4) return 'medium';
	if(available > 0) return 'low';
	return 'out';
}
</script>

<?php
function getStockLevel($available) {
	if($available > 10) return 'high';
	if($available > 4) return 'medium';
	if($available > 0) return 'low';
	return 'out';
}
?>
