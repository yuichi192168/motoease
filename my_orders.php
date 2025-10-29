<div class="content py-5 mt-3">
    <div class="container">
        <h3><b>My Orders</b></h3>
        <hr>
        
        <!-- Desktop Table View -->
        <div class="card card-outline card-dark shadow rounded-0 d-none d-md-block">
            <div class="card-body">
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-stripped table-bordered">
                            <colgroup>
                                <col width="5%">
                                <col width="20%">
                                <col width="25%">
                                <col width="20%">
                                <col width="15%">
                                <col width="15%">
                            </colgroup>
                            <thead>
                                <tr class="bg-gradient-light text-light">
                                    <th class="text-center">#</th>
                                    <th class="text-center">Date Ordered</th>
                                    <th class="text-center">Ref. Code</th>
                                    <th class="text-center">Total Amount</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $orders = $conn->query("SELECT * FROM `order_list` where client_id = '{$_settings->userdata('id')}' order by unix_timestamp(date_created) desc ");
                                while($row = $orders->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                                        <td><span class="text-muted"><?= $row['ref_code'] ?></span></td>
                                        <td class="text-right">₱<?= number_format((float)($row['total_amount'] ?? 0),2) ?></td>
                                        <td class="text-center">
                                            <?php if($row['status'] == 0): ?>
                                                <span class="badge badge-secondary px-3 rounded-pill">Pending</span>
                                            <?php elseif($row['status'] == 1): ?>
                                                <span class="badge badge-primary px-3 rounded-pill">Ready for pickup</span>
                                            <?php elseif($row['status'] == 2): ?>
                                                <span class="badge badge-success px-3 rounded-pill">For Delivery</span>
                                            <?php elseif($row['status'] == 3): ?>
                                                <!-- <span class="badge badge-warning px-3 rounded-pill">On the Way</span> -->
                                            <?php elseif($row['status'] == 4): ?>
                                                <span class="badge badge-default bg-gradient-teal px-3 rounded-pill">Delivered</span>
                                            <?php elseif($row['status'] == 6): ?>
                                                <span class="badge badge-success px-3 rounded-pill">Claimed</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger px-3 rounded-pill">Cancelled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <button class="btn btn-sm btn-primary view_order" data-id="<?= $row['id'] ?>">
                                                <i class="fa fa-eye"></i> View Details
                                            </button>
                                            <?php if($row['status'] == 0): ?>
                                                <button class="btn btn-sm btn-outline-danger cancel_order" data-id="<?= $row['id'] ?>">
                                                    <i class="fa fa-times"></i> Cancel
                                                </button>
                                            <?php elseif($row['status'] == 4): ?>
                                                <button class="btn btn-sm btn-success confirm_receipt" data-id="<?= $row['id'] ?>">
                                                    <i class="fa fa-check"></i> Confirm Receipt
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            <?php 
            $i = 1;
            $orders_mobile = $conn->query("SELECT * FROM `order_list` where client_id = '{$_settings->userdata('id')}' order by unix_timestamp(date_created) desc ");
            while($row = $orders_mobile->fetch_assoc()):
            ?>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">Order #<?= $i++ ?></h6>
                            <small class="text-muted"><?= date("M d, Y", strtotime($row['date_created'])) ?></small>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Ref. Code:</small><br>
                                <span class="text-muted"><?= $row['ref_code'] ?></span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Total Amount:</small><br>
                                <strong class="text-primary">₱<?= number_format((float)($row['total_amount'] ?? 0),2) ?></strong>
                            </div>
                        </div>
                        
                        <div class="mt-2">
                            <small class="text-muted">Status:</small><br>
                            <?php if($row['status'] == 0): ?>
                                <span class="badge badge-secondary">Pending</span>
                            <?php elseif($row['status'] == 1): ?>
                                <span class="badge badge-primary">Ready for pickup</span>
                            <?php elseif($row['status'] == 2): ?>
                                <span class="badge badge-success">For Delivery</span>
                            <?php elseif($row['status'] == 3): ?>
                                <span class="badge badge-warning">On the Way</span>
                            <?php elseif($row['status'] == 4): ?>
                                <span class="badge badge-default bg-gradient-teal">Delivered</span>
                            <?php elseif($row['status'] == 6): ?>
                                <span class="badge badge-success">Claimed</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Cancelled</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Order ID: <?= $row['id'] ?></small>
                                <small class="text-muted"><?= date("M d, Y H:i", strtotime($row['date_created'])) ?></small>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-primary view_order" data-id="<?= $row['id'] ?>">
                                    <i class="fa fa-eye"></i> View Details
                                </button>
                                <?php if($row['status'] == 0): ?>
                                    <button class="btn btn-sm btn-outline-danger cancel_order" data-id="<?= $row['id'] ?>">
                                        <i class="fa fa-times"></i> Cancel
                                    </button>
                                <?php elseif($row['status'] == 4): ?>
                                    <button class="btn btn-sm btn-success confirm_receipt" data-id="<?= $row['id'] ?>">
                                        <i class="fa fa-check"></i> Confirm Receipt
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <?php if($orders_mobile->num_rows == 0): ?>
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fa fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Orders Yet</h5>
                        <p class="text-muted">You haven't placed any orders yet.</p>
                        <a href="./?p=products" class="btn btn-primary">Browse Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.table th, .table td').addClass("align-middle px-2 py-1")
		$('.table').dataTable();
		
		
		// Handle view order button clicks
		$('.view_order').click(function(){
			var order_id = $(this).data('id');
			viewOrder(order_id);
		});
		
		// Handle cancel order button clicks
		$('.cancel_order').click(function(){
			var order_id = $(this).data('id');
			cancelOrder(order_id);
		});
		
		// Handle confirm receipt button clicks
		$('.confirm_receipt').click(function(){
			var order_id = $(this).data('id');
			confirmReceipt(order_id);
		});
    })
    
	
	function viewOrder(order_id){
		uni_modal("Order Details","view_order.php?id="+order_id,'modal-lg');
	}
	
	function cancelOrder(order_id){
		_conf("Are you sure you want to cancel this order?","cancel_order",[order_id]);
	}
	
	function confirmReceipt(order_id){
		_conf("Are you sure you have received this order?","confirm_receipt",[order_id]);
	}
	
	function cancel_order(order_id){
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=cancel_order",
			method: "POST",
			data: {id: order_id},
			dataType: "json",
			error: err => {
				console.log(err);
				// Try to recover from non-JSON responses that still contain JSON
				try{
					if(err && err.responseText){
						var parsed = null;
						try{ parsed = JSON.parse(err.responseText); }catch(e){ parsed = null; }
						if(!parsed){
							var txt = err.responseText;
							var s = txt.indexOf('{');
							var e = txt.lastIndexOf('}');
							if(s !== -1 && e !== -1 && e > s){
								var sub = txt.substring(s, e+1);
								try{ parsed = JSON.parse(sub); }catch(e2){ parsed = null; }
							}
						}
						if(parsed && parsed.status == 'success'){
							alert_toast("Order cancelled successfully.", 'success');
							// Add delay to allow user to see the success message
							setTimeout(function(){
								location.reload();
							}, 2000);
							return;
						}else if(parsed && parsed.msg){
							alert_toast(parsed.msg,'error');
							end_loader();
							return;
						}
					}
				}catch(parseErr){ console.log('Response parse failed', parseErr); }
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success: function(resp){
				if(typeof resp == 'object' && resp.status == 'success'){
					alert_toast("Order cancelled successfully.", 'success');
					// Add delay to allow user to see the success message
					setTimeout(function(){
						location.reload();
					}, 2000);
				} else {
					alert_toast(resp.msg || "An error occurred.", 'error');
					end_loader();
				}
			}
		});
	}
	
	function confirm_receipt(order_id){
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=confirm_receipt",
			method: "POST",
			data: {id: order_id},
			dataType: "json",
			error: err => {
				console.log(err);
				// Try to recover from non-JSON responses that still contain JSON
				try{
					if(err && err.responseText){
						var parsed = null;
						try{ parsed = JSON.parse(err.responseText); }catch(e){ parsed = null; }
						if(!parsed){
							var txt = err.responseText;
							var s = txt.indexOf('{');
							var e = txt.lastIndexOf('}');
							if(s !== -1 && e !== -1 && e > s){
								var sub = txt.substring(s, e+1);
								try{ parsed = JSON.parse(sub); }catch(e2){ parsed = null; }
							}
						}
						if(parsed && parsed.status == 'success'){
							alert_toast("Order receipt confirmed successfully.", 'success');
							// Add delay to allow user to see the success message
							setTimeout(function(){
								location.reload();
							}, 2000);
							return;
						}else if(parsed && parsed.msg){
							alert_toast(parsed.msg,'error');
							end_loader();
							return;
						}
					}
				}catch(parseErr){ console.log('Response parse failed', parseErr); }
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success: function(resp){
				if(typeof resp == 'object' && resp.status == 'success'){
					alert_toast("Order receipt confirmed successfully.", 'success');
					// Add delay to allow user to see the success message
					setTimeout(function(){
						location.reload();
					}, 2000);
				} else {
					alert_toast(resp.msg || "An error occurred.", 'error');
					end_loader();
				}
			}
		});
	}
</script>

<!-- Modal Structure -->
<div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog rounded-0 modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>