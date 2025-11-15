<?php
require_once('config.php');
require_once('classes/CustomerAccountBalance.php');

if(!$_settings->userdata('id') > 0 || $_settings->userdata('login_type') != 2){
    die('Unauthorized');
}

$client_id = $_settings->userdata('id');
$account_id = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;

if($account_id <= 0){
    echo "Invalid account ID";
    exit;
}

$accountBalance = new CustomerAccountBalance($conn);
$account = $accountBalance->getAccountInfo($account_id);

// Verify account belongs to client
if(!$account || $account['client_id'] != $client_id){
    echo "Unauthorized access";
    exit;
}

$schedule = $accountBalance->getPaymentSchedule($account_id);
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Month</th>
                <th>Due Date</th>
                <th>Amount Due</th>
                <th>Paid Amount</th>
                <th>Late Fee</th>
                <th>Remaining Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($schedule) > 0): ?>
                <?php foreach($schedule as $item): 
                    $days_overdue = 0;
                    if($item['payment_status'] != 'Paid' && strtotime($item['due_date']) < strtotime('today')){
                        $days_overdue = floor((strtotime('today') - strtotime($item['due_date'])) / (60*60*24));
                    }
                ?>
                    <tr class="<?php echo $days_overdue > 7 ? 'table-danger' : ($days_overdue > 0 ? 'table-warning' : '') ?>">
                        <td><?php echo $item['installment_number'] ?></td>
                        <td>
                            <?php echo date('M d, Y', strtotime($item['due_date'])) ?>
                            <?php if($days_overdue > 0): ?>
                                <br><small class="text-danger"><?php echo $days_overdue ?> day(s) overdue</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">₱<?php echo number_format($item['amount_due'], 2) ?></td>
                        <td class="text-right text-success">₱<?php echo number_format($item['paid_amount'], 2) ?></td>
                        <td class="text-right text-danger">
                            <?php if($item['late_fee'] > 0): ?>
                                ₱<?php echo number_format($item['late_fee'], 2) ?>
                            <?php else: ?>
                                ₱0.00
                            <?php endif; ?>
                        </td>
                        <td class="text-right">₱<?php echo number_format($item['remaining_balance'], 2) ?></td>
                        <td class="text-center">
                            <?php 
                            $status = $item['payment_status'];
                            $badge = 'secondary';
                            switch($status){
                                case 'Paid': $badge = 'success'; break;
                                case 'Late': $badge = 'danger'; break;
                                case 'Partial': $badge = 'warning'; break;
                                case 'Unpaid': $badge = 'info'; break;
                            }
                            ?>
                            <span class="badge badge-<?php echo $badge ?>"><?php echo $status ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No payment schedule available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

