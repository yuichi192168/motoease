<?php
require_once('./config.php');

if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<script>alert('Invalid Order ID'); window.close();</script>";
    exit;
}

$raw_id = $_GET['id'];
// Support both numeric order id and reference code
if(ctype_digit((string)$raw_id)){
    $order_id = $raw_id;
} else {
    $ref = $conn->real_escape_string($raw_id);
    $oid_rs = $conn->query("SELECT id FROM order_list WHERE ref_code = '{$ref}'");
    if($oid_rs && $oid_rs->num_rows){
        $order_id = $oid_rs->fetch_assoc()['id'];
    } else {
        echo "<script>alert('Order not found'); window.close();</script>";
        exit;
    }
}

// Get order details
$order = $conn->query("SELECT o.*, c.firstname, c.lastname, c.middlename, c.email, c.contact, c.address
                      FROM order_list o 
                      INNER JOIN client_list c ON o.client_id = c.id 
                      WHERE o.id = '{$order_id}'")->fetch_assoc();

if(!$order){
    echo "<script>alert('Order not found'); window.close();</script>";
    exit;
}

// Get order items
$items = $conn->query("SELECT oi.*, 
                              p.name as product_name, 
                              p.description as product_description, 
                              p.image_path,
                              COALESCE(oi.price, p.price, 0) as unit_price
                      FROM order_items oi 
                      INNER JOIN product_list p ON oi.product_id = p.id 
                      WHERE oi.order_id = '{$order_id}'");

// Get status text
function getStatusText($status) {
    switch($status) {
        case 0: return 'Pending';
        case 1: return 'Ready for pickup';
        case 2: return 'Processing';
        case 3: return 'Ready for Pickup';
        case 4: return 'Completed';
        case 5: return 'Cancelled';
        case 6: return 'Claimed';
        default: return 'Unknown';
    }
}

function getStatusClass($status) {
    switch($status) {
        case 0: return 'badge-secondary';
        case 1: return 'badge-primary';
        case 2: return 'badge-success';
        case 3: return 'badge-warning';
        case 4: return 'badge-info';
        case 5: return 'badge-danger';
        case 6: return 'badge-success';
        default: return 'badge-secondary';
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Order Header -->
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Order Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Order Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Order ID:</strong></td>
                                    <td><?= $order['id'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Reference Code:</strong></td>
                                    <td><?= $order['ref_code'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date Ordered:</strong></td>
                                    <td><?= date('F d, Y H:i A', strtotime($order['date_created'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td><span class="badge <?= getStatusClass($order['status']) ?> px-3 rounded-pill"><?= getStatusText($order['status']) ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Amount:</strong></td>
                                    <td><strong class="text-primary">₱<?= number_format($order['total_amount'], 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Customer Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td><?= $order['firstname'] . ' ' . $order['lastname'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td><?= $order['email'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Contact:</strong></td>
                                    <td><?= $order['contact'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Address:</strong></td>
                                    <td><?= $order['address'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Order Items</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Description</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                while($item = $items->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if(!empty($item['image_path'])): ?>
                                            <img src="<?= validate_image($item['image_path']) ?>" alt="Product Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                            <?php endif; ?>
                                            <strong><?= $item['product_name'] ?></strong>
                                        </div>
                                    </td>
                                    <td><?= !empty($item['product_description']) ? html_entity_decode($item['product_description']) : '-' ?></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-right">₱<?= number_format($item['unit_price'], 2) ?></td>
                                    <td class="text-right"><strong>₱<?= number_format($item['quantity'] * $item['unit_price'], 2) ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="5" class="text-right">Total Amount:</th>
                                    <th class="text-right text-primary">₱<?= number_format($order['total_amount'], 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            <?php if(!empty($order['notes'])): ?>
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sticky-note"></i> Order Notes</h3>
                </div>
                <div class="card-body">
                    <p><?= nl2br($order['notes']) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="card card-outline card-secondary">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-primary" onclick="printOrder()">
                        <i class="fas fa-print"></i> Print Order
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.close()">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printOrder() {
    window.print();
}

// Auto-print when page loads (optional)
// window.onload = function() {
//     printOrder();
// };
</script>

<style>
@media print {
    .card-header, .btn, .card-outline {
        border: 1px solid #000 !important;
    }
    .btn {
        display: none !important;
    }
    body {
        font-size: 12px;
    }
    .table th, .table td {
        padding: 5px !important;
    }
}
</style>