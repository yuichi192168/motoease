<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .log-entry {
        border-left: 4px solid #007bff;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    .log-entry.service {
        border-left-color: #28a745;
    }
    .log-entry.transaction {
        border-left-color: #ffc107;
    }
    .log-entry.login {
        border-left-color: #17a2b8;
    }
    .log-entry.order {
        border-left-color: #6f42c1;
    }
    .log-timestamp {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .log-user {
        font-weight: bold;
        color: #495057;
    }
    .log-action {
        color: #007bff;
        font-weight: 500;
    }
    .log-details {
        margin-top: 5px;
        font-size: 0.9rem;
    }
</style>

<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">User Activity Log</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-outline-primary" id="export_logs">
				<i class="fa fa-download"></i> Export
			</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<!-- Filter Section -->
			<div class="row mb-3">
				<div class="col-md-3">
					<label for="user_filter">Filter by User:</label>
					<select id="user_filter" class="form-control form-control-sm">
						<option value="">All Users</option>
						<?php 
						$users = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as fullname FROM client_list WHERE delete_flag = 0 ORDER BY firstname, lastname");
						while($user = $users->fetch_assoc()):
						?>
						<option value="<?= $user['id'] ?>"><?= $user['fullname'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="col-md-3">
					<label for="activity_filter">Filter by Activity:</label>
					<select id="activity_filter" class="form-control form-control-sm">
						<option value="">All Activities</option>
						<option value="service">Service Requests</option>
						<option value="transaction">Transactions</option>
						<option value="login">Login Activity</option>
                        <option value="order">Orders</option>
                        <option value="login">Staff Logins</option>
					</select>
				</div>
				<div class="col-md-3">
					<label for="date_from">Date From:</label>
					<input type="date" id="date_from" class="form-control form-control-sm" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
				</div>
				<div class="col-md-3">
					<label for="date_to">Date To:</label>
					<input type="date" id="date_to" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
				</div>
			</div>
			
			<!-- Activity Log Display -->
			<div id="activity_logs">
				<?php
				// Get service requests
				$service_requests = $conn->query("
					SELECT 
						sr.id,
						sr.date_created,
						sr.status,
						sr.service_type,
						CONCAT(c.firstname, ' ', c.lastname) as customer_name,
						c.id as customer_id,
						rm.meta_value as vehicle_info
					FROM service_requests sr
					INNER JOIN client_list c ON sr.client_id = c.id
					LEFT JOIN request_meta rm ON sr.id = rm.request_id AND rm.meta_field = 'vehicle_name'
					WHERE sr.date_created >= DATE_SUB(NOW(), INTERVAL 30 DAY)
					ORDER BY sr.date_created DESC
					LIMIT 50
				");
				
				// Get transactions
				$transactions = $conn->query("
					SELECT 
						ct.id,
						ct.date_created,
						ct.transaction_type,
						ct.amount,
						ct.description,
						CONCAT(c.firstname, ' ', c.lastname) as customer_name,
						c.id as customer_id
					FROM customer_transactions ct
					INNER JOIN client_list c ON ct.client_id = c.id
					WHERE ct.date_created >= DATE_SUB(NOW(), INTERVAL 30 DAY)
					ORDER BY ct.date_created DESC
					LIMIT 50
				");
				
				// Get orders
				$orders = $conn->query("
					SELECT 
						ol.id,
						ol.date_created,
						ol.status,
						ol.total_amount,
						ol.ref_code,
						CONCAT(c.firstname, ' ', c.lastname) as customer_name,
						c.id as customer_id
					FROM order_list ol
					INNER JOIN client_list c ON ol.client_id = c.id
					WHERE ol.date_created >= DATE_SUB(NOW(), INTERVAL 30 DAY)
					ORDER BY ol.date_created DESC
					LIMIT 50
				");
				
                // Combine and sort all activities
				$activities = [];
				
				while($sr = $service_requests->fetch_assoc()) {
					$activities[] = [
						'type' => 'service',
						'date' => $sr['date_created'],
						'user_id' => $sr['customer_id'],
						'user_name' => $sr['customer_name'],
						'action' => 'Service Request',
						'details' => "{$sr['service_type']} - " . ($sr['vehicle_info'] ?: 'Vehicle service') . " (Status: " . getServiceStatus($sr['status']) . ")",
						'id' => $sr['id']
					];
				}
				
				while($tr = $transactions->fetch_assoc()) {
					$activities[] = [
						'type' => 'transaction',
						'date' => $tr['date_created'],
						'user_id' => $tr['customer_id'],
						'user_name' => $tr['customer_name'],
						'action' => 'Transaction',
						'details' => "{$tr['transaction_type']} - ₱" . number_format($tr['amount'], 2) . " - {$tr['description']}",
						'id' => $tr['id']
					];
				}
				
				while($ord = $orders->fetch_assoc()) {
					$activities[] = [
						'type' => 'order',
						'date' => $ord['date_created'],
						'user_id' => $ord['customer_id'],
						'user_name' => $ord['customer_name'],
						'action' => 'Order',
						'details' => "Order #{$ord['ref_code']} - ₱" . number_format($ord['total_amount'], 2) . " (Status: " . getOrderStatus($ord['status']) . ")",
						'id' => $ord['id']
					];
				}
				
                // Staff login activity (last 30 days)
                $staff_logins = $conn->query("SELECT id, firstname, lastname, last_login FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY last_login DESC");
                while($lg = $staff_logins->fetch_assoc()){
                    $activities[] = [
                        'type' => 'login',
                        'date' => $lg['last_login'],
                        'user_id' => $lg['id'],
                        'user_name' => $lg['firstname'].' '.$lg['lastname'],
                        'action' => 'Staff Login',
                        'details' => 'Logged into the admin dashboard',
                        'id' => $lg['id']
                    ];
                }

                // Sort by date (newest first)
				usort($activities, function($a, $b) {
					return strtotime($b['date']) - strtotime($a['date']);
				});
				
				// Display activities
				foreach($activities as $activity):
				?>
				<div class="log-entry <?= $activity['type'] ?>" data-user="<?= $activity['user_id'] ?>" data-type="<?= $activity['type'] ?>" data-date="<?= $activity['date'] ?>">
					<div class="d-flex justify-content-between align-items-start">
						<div>
							<span class="log-user"><?= $activity['user_name'] ?></span>
							<span class="log-action"><?= $activity['action'] ?></span>
						</div>
						<span class="log-timestamp"><?= date('M d, Y H:i', strtotime($activity['date'])) ?></span>
					</div>
					<div class="log-details"><?= $activity['details'] ?></div>
				</div>
				<?php endforeach; ?>
				
				<?php if(empty($activities)): ?>
				<div class="text-center py-5">
					<i class="fa fa-info-circle fa-3x text-muted mb-3"></i>
					<p class="text-muted">No activity logs found for the selected period.</p>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function(){
	// Filter functionality
	function filterLogs() {
		var userFilter = $('#user_filter').val();
		var activityFilter = $('#activity_filter').val();
		var dateFrom = $('#date_from').val();
		var dateTo = $('#date_to').val();
		
		var visibleCount = 0;
		
		$('.log-entry').each(function() {
			var entry = $(this);
			var show = true;
			
			// User filter - check if user filter is set and matches
			if(userFilter && userFilter !== '' && entry.data('user') != userFilter) {
				show = false;
			}
			
			// Activity filter - check if activity filter is set and matches
			if(activityFilter && activityFilter !== '' && entry.data('type') != activityFilter) {
				show = false;
			}
			
			// Date filter - improved date comparison
			if(dateFrom || dateTo) {
				var entryDate = entry.data('date');
				if(entryDate) {
					// Handle both datetime and date-only formats
					var entryDateOnly = entryDate.split(' ')[0];
					
					if(dateFrom && entryDateOnly < dateFrom) {
						show = false;
					}
					if(dateTo && entryDateOnly > dateTo) {
						show = false;
					}
				}
			}
			
			if(show) {
				entry.show();
				visibleCount++;
			} else {
				entry.hide();
			}
		});
		
		// Show message if no logs are visible
		if(visibleCount === 0) {
			$('#activity_logs').append('<div class="text-center py-5" id="no-logs-message"><i class="fa fa-info-circle fa-3x text-muted mb-3"></i><p class="text-muted">No activity logs found for the selected filters.</p></div>');
		} else {
			$('#no-logs-message').remove();
		}
	}
	
	// Bind filter events
	$('#user_filter, #activity_filter, #date_from, #date_to').on('change', filterLogs);
	
	// Export functionality
	$('#export_logs').click(function() {
		var visibleLogs = $('.log-entry:visible');
		if(visibleLogs.length === 0) {
			alert_toast('No logs to export', 'warning');
			return;
		}
		
		var csv = 'User,Action,Details,Date\n';
		visibleLogs.each(function() {
			var user = $(this).find('.log-user').text();
			var action = $(this).find('.log-action').text();
			var details = $(this).find('.log-details').text().replace(/"/g, '""');
			var date = $(this).find('.log-timestamp').text();
			
			csv += '"' + user + '","' + action + '","' + details + '","' + date + '"\n';
		});
		
		var blob = new Blob([csv], { type: 'text/csv' });
		var url = window.URL.createObjectURL(blob);
		var a = document.createElement('a');
		a.href = url;
		a.download = 'user_activity_log_' + new Date().toISOString().split('T')[0] + '.csv';
		a.click();
		window.URL.revokeObjectURL(url);
	});
});

function getServiceStatus(status) {
	switch(parseInt(status)) {
		case 0: return 'Pending';
		case 1: return 'Confirmed';
		case 2: return 'In Progress';
		case 3: return 'Completed';
		case 4: return 'Cancelled';
		default: return 'Unknown';
	}
}

function getOrderStatus(status) {
	switch(parseInt(status)) {
		case 0: return 'Pending';
		case 1: return 'Packed';
		case 2: return 'For Delivery';
		case 3: return 'On the Way';
		case 4: return 'Delivered';
		case 5: return 'Cancelled';
		default: return 'Unknown';
	}
}
</script>

<?php
function getServiceStatus($status) {
	switch($status) {
		case 0: return 'Pending';
		case 1: return 'Confirmed';
		case 2: return 'In Progress';
		case 3: return 'Completed';
		case 4: return 'Cancelled';
		default: return 'Unknown';
	}
}

function getOrderStatus($status) {
	switch($status) {
		case 0: return 'Pending';
		case 1: return 'Packed';
		case 2: return 'For Delivery';
		case 3: return 'On the Way';
		case 4: return 'Delivered';
		case 6: return 'Claimed';
		case 5: return 'Cancelled';
		default: return 'Unknown';
	}
}
?>
