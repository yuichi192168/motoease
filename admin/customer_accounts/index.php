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
			<div class="alert alert-info mb-0">
				Account balance and transaction management UI has been removed.
				You can still manage customer profiles and OR/CR documents.
			</div>
			
			<!-- Account balance table removed -->
		</div>
	</div>
</div>

<!-- Adjust Balance Modal removed -->

<!-- View Transactions Modal removed -->

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
