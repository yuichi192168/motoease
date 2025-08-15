<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">Customer Account Balances</h3>
		<div class="card-tools">
			<button class="btn btn-flat btn-sm btn-default" type="button" id="print_reports"><span class="fa fa-print"></span> Print Report</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="row mb-3">
				<div class="col-md-3">
					<div class="info-box bg-primary">
						<span class="info-box-icon"><i class="fas fa-users"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Customers</span>
							<span class="info-box-number">
								<?php 
									try {
										$total_customers = $conn->query("SELECT COUNT(*) as total FROM client_list WHERE delete_flag = 0")->fetch_assoc()['total'];
										echo number_format($total_customers);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-success">
						<span class="info-box-icon"><i class="fas fa-wallet"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Total Balance</span>
							<span class="info-box-number">
								₱<?php 
									try {
										$total_balance = $conn->query("SELECT SUM(account_balance) as total FROM client_list WHERE delete_flag = 0")->fetch_assoc()['total'];
										echo number_format($total_balance ?: 0, 2);
									} catch (Exception $e) {
										echo "0.00";
									}
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-warning">
						<span class="info-box-icon"><i class="fas fa-credit-card"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Active Accounts</span>
							<span class="info-box-number">
								<?php 
									try {
										$active_accounts = $conn->query("SELECT COUNT(*) as total FROM client_list WHERE delete_flag = 0 AND account_balance > 0")->fetch_assoc()['total'];
										echo number_format($active_accounts);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="info-box bg-info">
						<span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Today's Transactions</span>
							<span class="info-box-number">
								<?php 
									try {
										$today_transactions = $conn->query("SELECT COUNT(*) as total FROM customer_transactions WHERE DATE(date_created) = CURDATE()")->fetch_assoc()['total'];
										echo number_format($today_transactions);
									} catch (Exception $e) {
										echo "0";
									}
								?>
							</span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="table-responsive">
				<table class="table table-bordered table-stripped">
					<colgroup>
						<col width="5%">
						<col width="20%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
						<col width="15%">
					</colgroup>
					<thead>
						<tr>
							<th>#</th>
							<th>Customer Name</th>
							<th>Contact</th>
							<th>Email</th>
							<th>Account Balance</th>
							<th>Last Transaction</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						try {
							$i = 1;
							$qry = $conn->query("SELECT c.*, 
												(SELECT MAX(date_created) FROM customer_transactions WHERE client_id = c.id) as last_transaction
												FROM `client_list` c 
												WHERE c.delete_flag = 0 
												ORDER BY c.account_balance DESC, c.lastname ASC");
							while($row = $qry->fetch_assoc()):
								foreach($row as $k=> $v){
									$row[$k] = trim(stripslashes($v));
								}
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td><?php echo ucwords($row['lastname'].', '.$row['firstname'].' '.$row['middlename']) ?></td>
								<td><?php echo $row['contact'] ?></td>
								<td><?php echo $row['email'] ?></td>
								<td class="text-right">
									<span class="badge badge-<?= $row['account_balance'] > 0 ? 'success' : 'secondary' ?>">
										₱<?= number_format($row['account_balance'], 2) ?>
									</span>
								</td>
								<td>
									<?php 
									if($row['last_transaction']){
										echo date("M d, Y H:i", strtotime($row['last_transaction']));
									} else {
										echo '<span class="text-muted">No transactions</span>';
									}
									?>
								</td>
								<td align="center">
									<button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
										Action
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item view_transactions" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
											<span class="fa fa-history text-primary"></span> View Transactions
										</a>
										<a class="dropdown-item adjust_balance" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-name="<?php echo ucwords($row['lastname'].', '.$row['firstname']) ?>">
											<span class="fa fa-edit text-info"></span> Adjust Balance
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item" href="./?page=clients/manage_client&id=<?php echo $row['id'] ?>">
											<span class="fa fa-user text-success"></span> View Profile
										</a>
									</div>
								</td>
							</tr>
						<?php endwhile; ?>
						<?php if($qry->num_rows <= 0): ?>
						<tr>
							<td colspan="7" class="text-center">No customers found.</td>
						</tr>
						<?php endif; ?>
						<?php } catch (Exception $e) { ?>
						<tr>
							<td colspan="7" class="text-center text-danger">Error loading customers: <?php echo $e->getMessage(); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Adjust Balance Modal -->
<div class="modal fade" id="adjustBalanceModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Adjust Account Balance</h5>
				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<form id="adjustBalanceForm">
				<div class="modal-body">
					<input type="hidden" name="client_id" id="adjust_client_id">
					<div class="form-group">
						<label>Customer Name</label>
						<input type="text" id="adjust_customer_name" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label>Current Balance</label>
						<input type="text" id="current_balance" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label>Adjustment Type</label>
						<select name="adjustment_type" class="form-control" required>
							<option value="">Select Type</option>
							<option value="add">Add Balance</option>
							<option value="deduct">Deduct Balance</option>
							<option value="set">Set Balance</option>
						</select>
					</div>
					<div class="form-group">
						<label>Amount</label>
						<input type="number" name="amount" class="form-control" step="0.01" min="0" required>
					</div>
					<div class="form-group">
						<label>Reason</label>
						<textarea name="reason" class="form-control" rows="3" required></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Adjust Balance</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- View Transactions Modal -->
<div class="modal fade" id="viewTransactionsModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Transaction History</h5>
				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="transactions_list"></div>
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
</style>

<script>
	$(document).ready(function(){
		$('.table').dataTable({
			"scrollX": true,
			"scrollY": "400px",
			"scrollCollapse": true
		});
		
		$('.adjust_balance').click(function(){
			var id = $(this).attr('data-id');
			var name = $(this).attr('data-name');
			
			// Get current balance
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=get_client_balance",
				method: "POST",
				data: {client_id: id},
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#adjust_client_id').val(id);
						$('#adjust_customer_name').val(name);
						$('#current_balance').val('₱' + parseFloat(resp.balance).toFixed(2));
						$('#adjustBalanceModal').modal('show');
					}
				}
			});
		});
		
		$('.view_transactions').click(function(){
			var id = $(this).attr('data-id');
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=get_client_transactions",
				method: "POST",
				data: {client_id: id},
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#transactions_list').html(resp.html);
						$('#viewTransactionsModal').modal('show');
					}
				}
			});
		});
		
		$('#adjustBalanceForm').submit(function(e){
			e.preventDefault();
			start_loader();
			
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=adjust_client_balance",
				method: "POST",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success: function(resp){
					if(resp.status == 'success'){
						$('#adjustBalanceModal').modal('hide');
						location.reload();
					} else {
						alert_toast(resp.msg, 'error');
					}
					end_loader();
				}
			});
		});
		
		$('#print_reports').click(function(){
			var nw = window.open("print_customer_accounts.php","_blank","width=800,height=600")
		});
	})
</script>
