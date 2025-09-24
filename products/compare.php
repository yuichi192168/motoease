<?php
require_once(__DIR__ . '/../config.php');
$ids = isset($_GET['ids']) ? array_filter(array_map('intval', explode(',', $_GET['ids']))) : [];

if(empty($ids)){
    echo '<div class="content py-5 mt-3"><div class="container"><div class="alert alert-info">No products selected for comparison.</div></div></div>';
    exit;
}
$in = implode(',', $ids);
$qry = $conn->query("SELECT p.*, b.name as brand, c.category FROM product_list p JOIN brand_list b ON p.brand_id=b.id JOIN categories c ON p.category_id=c.id WHERE p.id IN ($in)");
$products = [];
while($row = $qry->fetch_assoc()){ $products[] = $row; }
?>
<div class="content py-5 mt-3">
    <div class="container">
        <h3 class="mb-3">Compare Models</h3>
        <style>
            /* Theme: red (#dc3545 primary) and black accents */
            .compare-table thead th{background:#dc3545;color:#fff;border-color:#c82333}
            .compare-table td,.compare-table th{vertical-align:middle}
            .color-badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid #2c2c2c;border-radius:14px;font-size:.86rem;margin:0 6px 6px 0;background:#111;color:#fff}
            .color-badge img{width:18px;height:18px;border-radius:3px;margin-right:6px;object-fit:cover;border:1px solid #444;background:#fff}
            .price-cell{font-weight:700;color:#dc3545}
        </style>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped compare-table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <?php foreach($products as $p): ?>
                        <th><?= htmlspecialchars($p['brand'].' '.$p['name']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Image</th>
                        <?php foreach($products as $p): ?>
                        <td class="text-center"><img src="<?= validate_image($p['image_path']) ?>" style="width:120px;height:90px;object-fit:cover;"></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Model</th>
                        <?php foreach($products as $p): ?>
                        <td><?= htmlspecialchars($p['models']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <?php foreach($products as $p): ?>
                        <td><?= htmlspecialchars($p['category']) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <?php foreach($products as $p): ?>
                        <td class="price-cell">₱<?= number_format($p['price'],2) ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Available Colors</th>
                        <?php foreach($products as $p): ?>
                        <td>
                            <?php 
                            $sw = $conn->query("SELECT color, image_path FROM product_color_images WHERE product_id = '".$p['id']."'");
                            if($sw && $sw->num_rows>0){
                                echo '<div class="d-flex flex-wrap">';
                                while($s=$sw->fetch_assoc()){
                                    $cTxt = htmlspecialchars($s['color']);
                                    echo '<span class="color-badge" title="'.$cTxt.'">';
                                    echo '<img src="'.validate_image($s['image_path']).'" alt="'.$cTxt.'">';
                                    echo '<span>'.$cTxt.'</span>';
                                    echo '</span>';
                                }
                                echo '</div>';
                            } else {
                                echo htmlspecialchars($p['available_colors'] ?: '—');
                            }
                            ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <?php foreach($products as $p): ?>
                        <td><?= strip_tags(html_entity_decode($p['description'])) ?></td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



