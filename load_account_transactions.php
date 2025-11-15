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

$transactions = $accountBalance->getTransactionHistory($account_id);
?>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Receipt Number</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($transactions) > 0): ?>
                <?php foreach($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo date('M d, Y h:i A', strtotime($transaction['transaction_date'])) ?></td>
                        <td>
                            <?php 
                            $type = str_replace('_', ' ', $transaction['transaction_type']);
                            echo ucwords($type);
                            ?>
                        </td>
                        <td class="text-right">
                            <?php 
                            $color = 'text-success';
                            if($transaction['transaction_type'] == 'late_fee') $color = 'text-danger';
                            ?>
                            <span class="<?php echo $color ?>">
                                ₱<?php echo number_format($transaction['amount'], 2) ?>
                            </span>
                        </td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $transaction['payment_method'])) ?></td>
                        <td><?php echo $transaction['receipt_number'] ?: '-' ?></td>
                        <td><?php echo htmlspecialchars($transaction['notes'] ?: '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No transactions found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

