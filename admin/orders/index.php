<div class="card card-outline card-dark shadow rounded-0">
    <div class="card-header">
        <h3 class="card-title text-dark"><b>Order List</b></h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
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
                        <tr>
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
                                    <span class="badge badge-primary px-3 rounded-pill">Ready for pickup</span>
                                <?php elseif($row['status'] == 2): ?>
                                    <span class="badge badge-info px-3 rounded-pill">Processing</span>
                                <?php elseif($row['status'] == 3): ?>
                                    <span class="badge badge-warning px-3 rounded-pill">Ready for Pickup</span>
                                <?php elseif($row['status'] == 4): ?>
                                    <span class="badge badge-default bg-gradient-teal px-3 rounded-pill">Completed</span>
                                <?php elseif($row['status'] == 6): ?>
                                    <span class="badge badge-success px-3 rounded-pill">Claimed</span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-3 rounded-pill">Cancelled</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a class="btn btn-flat btn-sm btn-default border view_data" href="./?page=orders/view_order&id=<?= $row['id'] ?>" data-id="<?= $row['id'] ?>"><i class="fa fa-eye"></i> View</a>
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