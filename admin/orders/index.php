<div class="card card-outline card-dark shadow rounded-0">
    <div class="card-header">
        <h3 class="card-title text-dark"><b>Order List</b></h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <?php
            // Pending orders summary (show latest 5 pending orders)
            $pending_qry = $conn->query("SELECT o.id,o.ref_code,o.total_amount,o.date_created, concat(c.lastname,', ', c.firstname,' ',c.middlename) as fullname
                                    FROM `order_list` o
                                    inner join client_list c on o.client_id = c.id
                                    WHERE o.status = 0
                                    ORDER BY unix_timestamp(o.date_created) DESC LIMIT 5");
            if($pending_qry && $pending_qry->num_rows > 0):
            ?>
            <div class="mb-3">
                <h5 class="mb-2"><i class="fa fa-exclamation-circle text-warning"></i> Pending Orders</h5>
                <div class="list-group">
                    <?php while($po = $pending_qry->fetch_assoc()): ?>
                        <a href="./?page=orders/view_order&id=<?= $po['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <div><strong><?= htmlspecialchars($po['ref_code']) ?></strong></div>
                                <small class="text-muted"><?= htmlspecialchars($po['fullname']) ?> &middot; <?= date("Y-m-d H:i", strtotime($po['date_created'])) ?></small>
                            </div>
                            <div class="text-right">
                                <span class="badge badge-warning">₱<?= number_format($po['total_amount'],2) ?></span>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
            <table class="table table-striped table-bordered">
                <colgroup>
                    <col width="5%">
                    <col width="12%">
                    <col width="12%">
                    <col width="15%">
                    <col width="25%">
                    <col width="12%">
                    <col width="12%">
                    <col width="7%">
                </colgroup>
                <thead>
                    <tr class="bg-gradient-dark text-dark">
                        <th class="text-center">#</th>
                        <th class="text-center">Date Created</th>
                        <th class="text-center">Ref. Code</th>
                        <th class="text-center">Client</th>
                        <th class="text-center">Products</th>
                        <th class="text-center">Total Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $orders = $conn->query("SELECT o.*,concat(c.lastname,', ', c.firstname,' ',c.middlename) as fullname,
                                           GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
                                           FROM `order_list` o 
                                           inner join client_list c on o.client_id = c.id 
                                           left join order_items oi on o.id = oi.order_id
                                           left join product_list p on oi.product_id = p.id
                                           group by o.id
                                           order by o.status asc, unix_timestamp(o.date_created) desc ");
                    while($row = $orders->fetch_assoc()):
                    ?>
                        <tr id="order-row-<?= $row['id'] ?>">
                            <td class="text-center"><?= $i++ ?></td>
                            <td><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
                            <td><?= $row['ref_code'] ?></td>
                            <td><?= $row['fullname'] ?></td>
                            <td>
                                <small class="text-muted">
                                    <?= $row['product_names'] ?: 'No products' ?>
                                </small>
                            </td>
                            <td class="text-right">₱<?= number_format($row['total_amount'],2) ?></td>
                            <td class="text-center">
                                <?php if($row['status'] == 0): ?>
                                    <span class="badge badge-secondary px-3 rounded-pill">Pending</span>
                                <?php elseif($row['status'] == 1): ?>
                                    <span class="badge badge-primary px-3 rounded-pill">Approved Order</span>
                                <?php elseif($row['status'] == 2): ?>
                                    <span class="badge badge-success px-3 rounded-pill">For Delivery</span>
                                <?php elseif($row['status'] == 3): ?>
                                    <span class="badge badge-warning px-3 rounded-pill">On the Way</span>
                                <?php elseif($row['status'] == 4): ?>
                                    <span class="badge badge-default bg-gradient-teal px-3 rounded-pill">Delivered</span>
                                <?php elseif($row['status'] == 6): ?>
                                    <span class="badge badge-success px-3 rounded-pill">Claimed</span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-3 rounded-pill">Cancelled</span>
                                <?php endif; ?>
                            </td>
                                                        <td align="center">
                                                                 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                                    Action
                                                                    <span class="sr-only">Toggle Dropdown</span>
                                                                </button>
                                                                <div class="dropdown-menu" role="menu">
                                                                    <a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Update Status</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
                                                                </div>
                                                        </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.table th, .table td').addClass("align-middle px-2 py-1")
		$('.table').dataTable();
		
		// Auto-refresh every 30 seconds to show new orders
		setInterval(function(){
			location.reload();
		}, 30000);
    })
</script>
<script>
    $(document).ready(function(){
        $('.view_data').click(function(){
            var id = $(this).attr('data-id');
            uni_modal("Order Details","orders/view_order.php?id="+id,'large')
        })
        $('.edit_data').click(function(){
            var id = $(this).attr('data-id');
            uni_modal("Update Order Status","orders/update_status.php?id="+id,'medium')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this order permanently?","delete_order",[$(this).attr('data-id')])
        })
    })
    function delete_order(id){
        start_loader();
        $.ajax({
            url:_base_url_+'classes/Master.php?f=delete_order',
            method:'POST',
            data:{id:id},
            dataType:'json',
            error:err=>{
                console.error('Delete order error:', err);
                alert_toast('An error occurred.','error');
                end_loader();
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast(resp.msg || 'An error occurred.','error');
                    end_loader();
                }
            }
        })
    }
</script>