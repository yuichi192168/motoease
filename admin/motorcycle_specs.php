<?php
require_once('../config.php');

// Handle AJAX requests for adding specifications
if(isset($_POST['action']) && $_POST['action'] == 'add_specifications'){
    try {
        // Read and execute the SQL file
        $sql_file = '../add_motorcycle_specifications.sql';
        if(file_exists($sql_file)){
            $sql_content = file_get_contents($sql_file);
            
            // Split the SQL content into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql_content)));
            
            $success_count = 0;
            $error_count = 0;
            $errors = [];
            
            foreach($statements as $statement){
                if(!empty($statement) && !preg_match('/^--/', $statement)){
                    try {
                        $conn->query($statement);
                        $success_count++;
                    } catch(Exception $e) {
                        $error_count++;
                        $errors[] = $e->getMessage();
                    }
                }
            }
            
            if($error_count == 0){
                echo json_encode(['status' => 'success', 'message' => "Successfully added motorcycle specifications! {$success_count} statements executed."]);
            } else {
                echo json_encode(['status' => 'partial', 'message' => "Partially completed. {$success_count} statements executed successfully, {$error_count} errors occurred.", 'errors' => $errors]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'SQL file not found.']);
        }
    } catch(Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

// Get all Honda motorcycles with their specifications
$motorcycles = [];
try {
    $qry = $conn->query("SELECT p.*, ms.* FROM product_list p 
                        LEFT JOIN motorcycle_specifications ms ON p.id = ms.product_id 
                        WHERE p.brand_id = 9 AND p.category_id = 10 AND p.delete_flag = 0 
                        ORDER BY p.name");
    while($row = $qry->fetch_assoc()){
        $motorcycles[] = $row;
    }
} catch(Exception $e) {
    $motorcycles = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Motorcycle Specifications - <?php echo $_settings->info('short_name') ?></title>
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <style>
        .spec-table {
            font-size: 0.9em;
        }
        .spec-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            width: 30%;
        }
        .spec-table td {
            padding: 8px 12px;
        }
        .motorcycle-card {
            margin-bottom: 20px;
        }
        .spec-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include('../admin/inc/navigation.php') ?>
        <?php include('../admin/inc/sidebar.php') ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Motorcycle Specifications</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="./">Home</a></li>
                                <li class="breadcrumb-item active">Motorcycle Specifications</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Honda Motorcycle Specifications</h3>
                                    <div class="card-tools">
                                        <button class="btn btn-primary btn-sm" id="add_specifications">
                                            <i class="fas fa-plus"></i> Add Specifications
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if(empty($motorcycles)): ?>
                                        <div class="alert alert-info">
                                            <h5><i class="icon fas fa-info"></i> No Motorcycles Found!</h5>
                                            Click "Add Specifications" to populate the database with Honda motorcycle specifications.
                                        </div>
                                    <?php else: ?>
                                        <div class="row">
                                            <?php foreach($motorcycles as $motorcycle): ?>
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card motorcycle-card">
                                                        <div class="card-header">
                                                            <h5 class="card-title"><?php echo $motorcycle['name'] ?></h5>
                                                            <small class="text-muted"><?php echo $motorcycle['models'] ?></small>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="spec-section">
                                                                <h6><strong>Description</strong></h6>
                                                                <p><?php echo strip_tags($motorcycle['description']) ?></p>
                                                            </div>
                                                            
                                                            <?php if(!empty($motorcycle['make'])): ?>
                                                                <div class="spec-section">
                                                                    <h6><strong>Specifications</strong></h6>
                                                                    <table class="table table-sm spec-table">
                                                                        <?php if($motorcycle['make']): ?>
                                                                            <tr><th>Make</th><td><?php echo $motorcycle['make'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['model']): ?>
                                                                            <tr><th>Model</th><td><?php echo $motorcycle['model'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['displacement']): ?>
                                                                            <tr><th>Displacement</th><td><?php echo $motorcycle['displacement'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['engine_type']): ?>
                                                                            <tr><th>Engine Type</th><td><?php echo $motorcycle['engine_type'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['transmission']): ?>
                                                                            <tr><th>Transmission</th><td><?php echo $motorcycle['transmission'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['seat_height']): ?>
                                                                            <tr><th>Seat Height</th><td><?php echo $motorcycle['seat_height'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['fuel_capacity']): ?>
                                                                            <tr><th>Fuel Capacity</th><td><?php echo $motorcycle['fuel_capacity'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['maximum_power']): ?>
                                                                            <tr><th>Maximum Power</th><td><?php echo $motorcycle['maximum_power'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['maximum_torque']): ?>
                                                                            <tr><th>Maximum Torque</th><td><?php echo $motorcycle['maximum_torque'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                        <?php if($motorcycle['fuel_consumption']): ?>
                                                                            <tr><th>Fuel Consumption</th><td><?php echo $motorcycle['fuel_consumption'] ?></td></tr>
                                                                        <?php endif; ?>
                                                                    </table>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="alert alert-warning">
                                                                    <small>No specifications available for this motorcycle.</small>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <div class="spec-section">
                                                                <h6><strong>Pricing</strong></h6>
                                                                <p><strong>Price:</strong> ₱<?php echo number_format($motorcycle['price'], 2) ?></p>
                                                                <p><strong>Category:</strong> <?php echo $motorcycle['abc_category'] ?></p>
                                                                <?php if($motorcycle['available_colors']): ?>
                                                                    <p><strong>Available Colors:</strong> <?php echo $motorcycle['available_colors'] ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../dist/js/adminlte.js"></script>
    
    <script>
        $(document).ready(function(){
            $('#add_specifications').click(function(){
                if(confirm('This will add motorcycle specifications to the database. Continue?')){
                    var btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
                    
                    $.ajax({
                        url: '',
                        method: 'POST',
                        data: {action: 'add_specifications'},
                        dataType: 'json',
                        success: function(resp){
                            if(resp.status == 'success'){
                                alert_toast(resp.message, 'success');
                                setTimeout(function(){
                                    location.reload();
                                }, 2000);
                            } else if(resp.status == 'partial'){
                                alert_toast(resp.message, 'warning');
                                if(resp.errors){
                                    console.log('Errors:', resp.errors);
                                }
                                setTimeout(function(){
                                    location.reload();
                                }, 3000);
                            } else {
                                alert_toast(resp.message, 'error');
                            }
                        },
                        error: function(){
                            alert_toast('An error occurred while adding specifications.', 'error');
                        },
                        complete: function(){
                            btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Add Specifications');
                        }
                    });
                }
            });
        });
        
        function alert_toast(message, type){
            var alert_class = type == 'success' ? 'alert-success' : (type == 'warning' ? 'alert-warning' : 'alert-danger');
            var toast = '<div class="alert ' + alert_class + ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                        '<strong>' + (type == 'success' ? 'Success!' : (type == 'warning' ? 'Warning!' : 'Error!')) + '</strong> ' + message +
                        '<button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>' +
                        '</div>';
            $('body').append(toast);
            setTimeout(function(){
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
</body>
</html>

