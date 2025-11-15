<?php
if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    echo "<script> alert('You are not allowed to access this page.'); location.replace('./') </script>";
}

require_once('classes/CustomerAccountBalance.php');

$client_id = $_settings->userdata('id');
$accountBalance = new CustomerAccountBalance($conn);

// Get all customer accounts
$accounts = $accountBalance->getCustomerAccounts($client_id);

// Get notifications
$notifications = $accountBalance->getCustomerNotifications($client_id, true);
$unread_count = count($notifications);
?>
<?php include('inc/header.php') ?>
<body>
<?php include('inc/topBarNav.php') ?>
<?php include('inc/navigation.php') ?>

<div class="content py-5 mt-3">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-outline card-primary shadow rounded-0">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-wallet"></i> My Account Balances</h3>
                    </div>
                    <div class="card-body">
                        <?php if(count($accounts) > 0): ?>
                            <div class="row">
                                <?php foreach($accounts as $account): 
                                    $schedule = $accountBalance->getPaymentSchedule($account['id']);
                                    $upcoming = array_filter($schedule, function($s) {
                                        return in_array($s['payment_status'], ['Unpaid', 'Partial', 'Late']) && strtotime($s['due_date']) >= strtotime('today');
                                    });
                                    $overdue = array_filter($schedule, function($s) {
                                        return in_array($s['payment_status'], ['Unpaid', 'Partial', 'Late']) && strtotime($s['due_date']) < strtotime('today');
                                    });
                                ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="card card-outline <?php echo $account['remaining_balance'] > 0 ? 'card-warning' : 'card-success' ?>">
                                            <div class="card-header">
                                                <h4 class="card-title"><?php echo htmlspecialchars($account['item_purchased']) ?></h4>
                                                <div class="card-tools">
                                                    <span class="badge badge-<?php echo $account['status'] == 'paid' ? 'success' : ($account['status'] == 'active' ? 'primary' : 'danger') ?>">
                                                        <?php echo ucfirst($account['status']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <dl class="row">
                                                    <dt class="col-sm-6">Total Price:</dt>
                                                    <dd class="col-sm-6"><strong>₱<?php echo number_format($account['total_price'], 2) ?></strong></dd>
                                                    
                                                    <dt class="col-sm-6">Downpayment:</dt>
                                                    <dd class="col-sm-6 text-info">₱<?php echo number_format($account['downpayment_amount'], 2) ?></dd>
                                                    
                                                    <dt class="col-sm-6">Paid Amount:</dt>
                                                    <dd class="col-sm-6 text-success"><strong>₱<?php echo number_format($account['paid_amount'], 2) ?></strong></dd>
                                                    
                                                    <dt class="col-sm-6">Remaining Balance:</dt>
                                                    <dd class="col-sm-6 text-danger"><strong>₱<?php echo number_format($account['remaining_balance'], 2) ?></strong></dd>
                                                    
                                                    <?php if($account['installment_plan_months']): ?>
                                                    <dt class="col-sm-6">Installment Plan:</dt>
                                                    <dd class="col-sm-6"><?php echo $account['installment_plan_months'] ?> months</dd>
                                                    
                                                    <dt class="col-sm-6">Monthly Payment:</dt>
                                                    <dd class="col-sm-6">₱<?php echo number_format($account['monthly_payment_amount'], 2) ?></dd>
                                                    <?php endif; ?>
                                                    
                                                    <?php if(count($overdue) > 0): ?>
                                                    <dt class="col-sm-6">Overdue Payments:</dt>
                                                    <dd class="col-sm-6"><span class="badge badge-danger"><?php echo count($overdue) ?></span></dd>
                                                    <?php endif; ?>
                                                    
                                                    <?php if(count($upcoming) > 0): ?>
                                                    <dt class="col-sm-6">Upcoming Payments:</dt>
                                                    <dd class="col-sm-6"><span class="badge badge-warning"><?php echo count($upcoming) ?></span></dd>
                                                    <?php endif; ?>
                                                </dl>
                                                
                                                <button type="button" class="btn btn-primary btn-sm view_schedule" data-id="<?php echo $account['id'] ?>">
                                                    <i class="fas fa-calendar-alt"></i> View Payment Schedule
                                                </button>
                                                <button type="button" class="btn btn-info btn-sm view_transactions" data-id="<?php echo $account['id'] ?>">
                                                    <i class="fas fa-history"></i> View Transaction History
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> You don't have any active account balances.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Schedule Modal -->
<div class="modal fade" id="schedule_modal" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Schedule</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="schedule_details"></div>
        </div>
    </div>
</div>

<!-- Transaction History Modal -->
<div class="modal fade" id="transaction_modal" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction History</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transaction_details"></div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('.view_schedule').click(function(){
        var account_id = $(this).attr('data-id');
        $.ajax({
            url: 'load_account_schedule.php',
            method: 'POST',
            data: {account_id: account_id},
            success: function(response){
                $('#schedule_details').html(response);
                $('#schedule_modal').modal('show');
            }
        });
    });
    
    $('.view_transactions').click(function(){
        var account_id = $(this).attr('data-id');
        $.ajax({
            url: 'load_account_transactions.php',
            method: 'POST',
            data: {account_id: account_id},
            success: function(response){
                $('#transaction_details').html(response);
                $('#transaction_modal').modal('show');
            }
        });
    });
});
</script>

<?php include('inc/footer.php') ?>
</body>
</html>


