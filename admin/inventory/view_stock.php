<?php
// Ensure DB connection when accessed directly (e.g., from Inventory list links)
if(!isset($conn)){
    require_once(dirname(__DIR__,2).'/config.php');
}
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT p.*, b.name as brand,c.category from `product_list` p inner join brand_list b on p.brand_id = b.id inner join categories c on p.category_id = c.id where p.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
        $stock_snapshot = get_product_stock_levels($conn, $id);
        $available = isset($stock_snapshot['available_stock']) ? $stock_snapshot['available_stock'] : 0;
    }
}
?>
<style>
    .product-img{
        width:15em;
        height:12em;
        object-fit:scale-down;
        object-position:center center;
    }
</style>
<div class="content py-3">
    <div class="card card-outline rounded-0 card-primary shadow">
        <div class="card-header">
            <h4 class="card-title">Product Stock List</h4>
            <div class="card-tools">
                <a class="btn btn-default border btn-sm btn-flat" href="./?page=inventory"><i class="fa fa-angle-left"></i> Back</a>
            </div>
        </div>
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <img src="<?= validate_image(isset($image_path) ? $image_path : "") ?>" alt="Product Image <?= isset($name) ? $name : "" ?>" class="img-thumbnail product-img">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="mx-2 text-muted">Product Name</small>
                                <div class="pl-4"><?= isset($name) ? $name : '' ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="mx-2 text-muted">Category</small>
                                <div class="pl-4"><?= isset($category) ? $category : '' ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <small class="mx-2 text-muted">Compatible Motorcycle</small>
                                <div class="pl-4"><?= isset($models) ? $models : '' ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="mx-2 text-muted">Name</small>
                                <div class="pl-4"><?= isset($name) ? $name : '' ?></div>
                            </div>
                            <div class="col-md-6">
                                <small class="mx-2 text-muted">Price</small>
                                <div class="pl-4"><?= isset($price) ? number_format($price,2) : '' ?></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <small class="mx-2 text-muted">Available Quantity</small>
                                <div class="pl-4"><?= isset($available) ? number_format($available) : '0' ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3>Stock-in History</h3>
                        <table class="table table-bordered table-stripped">
                            <colgroup>
                                <col width="60%">
                                <col width="40%">
                            </colgroup>
                            <thead>
                                <tr class="bg-light text-light">
                                    <th class="py-1 text-center">Date Added</th>
                                    <th class="py-1 text-center">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stocks = $conn->query("SELECT * FROM `stock_list` where `product_id` = '{$id}' AND type = 1 AND COALESCE(delete_flag,0) = 0 ORDER BY date_created DESC");
                                while($row=$stocks->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="px-2 py-1 align-middle"><?= date('M d, Y H:i', strtotime($row['date_created'])) ?></td>
                                        <td class="px-2 py-1 text-right align-middle">
                                            <span class="badge badge-info"><?= number_format($row['quantity']) ?></span>
                                        </td>
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

<script>
    // Stock-in History is now read-only - no actions available
</script>