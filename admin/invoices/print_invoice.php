<?php
require_once('../../config.php');

if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<script>alert('Invalid Invoice ID'); window.close();</script>";
    exit;
}

$invoice_id = $_GET['id'];

// Get invoice details
$invoice = $conn->query("SELECT i.*, c.firstname, c.lastname, c.middlename, c.email, c.contact,
                                u.firstname as staff_firstname, u.lastname as staff_lastname
                        FROM invoices i
                        INNER JOIN client_list c ON i.customer_id = c.id
                        LEFT JOIN users u ON i.generated_by = u.id
                        WHERE i.id = '{$invoice_id}'")->fetch_assoc();

if(!$invoice){
    echo "<script>alert('Invoice not found'); window.close();</script>";
    exit;
}

// Get invoice items
$items = $conn->query("SELECT * FROM invoice_items WHERE invoice_id = '{$invoice_id}'");

// Get receipt if exists
$receipt = $conn->query("SELECT r.*, u.firstname as staff_firstname, u.lastname as staff_lastname
                        FROM receipts r
                        LEFT JOIN users u ON r.received_by = u.id
                        WHERE r.invoice_id = '{$invoice_id}'")->fetch_assoc();

// Get company settings
$settings = [];
$settings_qry = $conn->query("SELECT setting_key, setting_value FROM invoice_settings");
while($row = $settings_qry->fetch_assoc()){
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= $invoice['invoice_number'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding: 20px;
            margin-bottom: 20px;
        }
        .company-info {
            flex: 1;
            text-align: center;
        }
        .company-info h2 {
            margin: 0;
            text-transform: uppercase;
            font-weight: bold;
            color: #333;
        }
        .company-info p {
            margin: 5px 0;
            color: #666;
        }
        .logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 0 20px;
        }
        .customer-info, .invoice-info {
            flex: 1;
        }
        .customer-info h4, .invoice-info h4 {
            margin: 0 0 10px 0;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th, .items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .totals {
            display: flex;
            justify-content: flex-end;
            margin: 20px 0;
            padding: 0 20px;
        }
        .totals-table {
            width: 300px;
        }
        .totals-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #eee;
        }
        .totals-table .total-row {
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        .footer-info {
            padding: 20px;
            border-top: 1px solid #ddd;
            margin-top: 30px;
        }
        .footer-info h5 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .footer-info p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        .payment-status {
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
        .payment-status.paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .payment-status.unpaid {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        @media print {
            body { margin: 0; }
            .invoice-container { border: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header with dual logos -->
        <div class="invoice-header">
            <!-- Main Logo -->
            <div>
                <img src="<?= validate_image($_settings->info('main_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Main Logo" class="logo">
            </div>
            
            <!-- Company Info -->
            <div class="company-info">
                <h2><?= $settings['company_name'] ?? 'Star Honda Calamba' ?></h2>
                <p><?= $settings['company_address'] ?? 'National Highway Brgy. Parian, Calamba City, Laguna' ?></p>
                <p>Phone: <?= $settings['company_phone'] ?? '0948-235-3207' ?> | Email: <?= $settings['company_email'] ?? 'starhondacalamba55@gmail.com' ?></p>
                <h3>INVOICE</h3>
            </div>
            
            <!-- Secondary Logo -->
            <div>
                <img src="<?= validate_image($_settings->info('secondary_logo')) ?: validate_image($_settings->info('logo')) ?>" alt="Secondary Logo" class="logo">
            </div>
        </div>

        <!-- Invoice and Customer Details -->
        <div class="invoice-details">
            <div class="customer-info">
                <h4>Bill To:</h4>
                <p><strong><?= $invoice['firstname'] . ' ' . $invoice['lastname'] ?></strong></p>
                <p><?= $invoice['email'] ?></p>
                <p><?= $invoice['contact'] ?></p>
            </div>
            <div class="invoice-info">
                <h4>Invoice Details:</h4>
                <p><strong>Invoice No:</strong> <?= $invoice['invoice_number'] ?></p>
                <p><strong>Date:</strong> <?= date('F d, Y', strtotime($invoice['generated_at'])) ?></p>
                <p><strong>Due Date:</strong> <?= date('F d, Y', strtotime($invoice['due_date'])) ?></p>
                <p><strong>Transaction Type:</strong> <?= strtoupper(str_replace('_', ' ', $invoice['transaction_type'])) ?></p>
                <p><strong>Payment Type:</strong> <?= strtoupper($invoice['payment_type']) ?></p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= $item['item_name'] ?></td>
                    <td><?= $item['item_description'] ?: '-' ?></td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-right">₱<?= number_format($item['unit_price'], 2) ?></td>
                    <td class="text-right">₱<?= number_format($item['total_price'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₱<?= number_format($invoice['subtotal'], 2) ?></td>
                </tr>
                <tr>
                    <td>VAT (<?= $settings['vat_rate'] ?? '12' ?>%):</td>
                    <td class="text-right">₱<?= number_format($invoice['vat_amount'], 2) ?></td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Amount:</strong></td>
                    <td class="text-right"><strong>₱<?= number_format($invoice['total_amount'], 2) ?></strong></td>
                </tr>
            </table>
        </div>

        <!-- Payment Status -->
        <div class="payment-status <?= $invoice['payment_status'] ?>">
            <?php if($invoice['payment_status'] == 'paid'): ?>
                ✅ PAID - Thank you for your payment!
            <?php else: ?>
                ⏳ PENDING PAYMENT - Payment must be completed in-store
            <?php endif; ?>
        </div>

        <!-- Receipt Information (if paid) -->
        <?php if($receipt): ?>
        <div style="padding: 0 20px; margin: 20px 0; border: 1px solid #28a745; background-color: #f8fff9;">
            <h5 style="color: #28a745; margin: 0 0 10px 0;">Receipt Information</h5>
            <p><strong>Receipt No:</strong> <?= $receipt['receipt_number'] ?></p>
            <p><strong>Amount Paid:</strong> ₱<?= number_format($receipt['amount_paid'], 2) ?></p>
            <p><strong>Payment Method:</strong> <?= strtoupper($receipt['payment_method']) ?></p>
            <p><strong>Date Paid:</strong> <?= date('F d, Y H:i', strtotime($receipt['issued_at'])) ?></p>
            <p><strong>Received By:</strong> <?= $receipt['staff_firstname'] . ' ' . $receipt['staff_lastname'] ?></p>
            <?php if($receipt['payment_reference']): ?>
            <p><strong>Reference:</strong> <?= $receipt['payment_reference'] ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Footer Information -->
        <div class="footer-info">
            <h5>Important Information:</h5>
            <p><strong>Pickup Location:</strong> <?= $invoice['pickup_location'] ?></p>
            <p><strong>Payment Instructions:</strong> <?= $invoice['payment_instructions'] ?></p>
            <?php if($invoice['pickup_instructions']): ?>
            <p><strong>Pickup Instructions:</strong> <?= $invoice['pickup_instructions'] ?></p>
            <?php endif; ?>
            
            <hr style="margin: 20px 0;">
            
            <h5>Contact Information:</h5>
            <p>📍 <?= $settings['company_address'] ?? 'National Highway Brgy. Parian, Calamba City, Laguna' ?></p>
            <p>📞 <?= $settings['company_phone'] ?? '0948-235-3207' ?></p>
            <p>✉️ <?= $settings['company_email'] ?? 'starhondacalamba55@gmail.com' ?></p>
            <p>📘 Facebook: <a href="https://www.facebook.com/starhondacalambabranch" target="_blank">@starhondacalambabranch</a></p>
            
            <p style="text-align: center; margin-top: 20px; font-size: 11px; color: #999;">
                This invoice was generated on <?= date('F d, Y \a\t H:i A', strtotime($invoice['generated_at'])) ?> 
                <?php if($invoice['staff_firstname']): ?>
                by <?= $invoice['staff_firstname'] . ' ' . $invoice['staff_lastname'] ?>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>

