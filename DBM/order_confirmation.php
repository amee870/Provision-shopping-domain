<?php
require_once 'config.php';

$ref = $_GET['ref'] ?? '';
$order = null;

if ($ref !== '') {
    $stmt = $conn->prepare('SELECT customer_name, customer_contact, order_date, total_amount, status FROM orders WHERE reference_no = ?');
    $stmt->bind_param('s', $ref);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();
}

// Estimated pickup date: 1 day after the order was placed
$pickup_date = '';
if ($order) {
    $order_timestamp = strtotime($order['order_date']);
    $pickup_date = date('l, F j, Y', strtotime('+1 day', $order_timestamp));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Confirmed - Provision Shop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Provision Shop Ordering System</h1>
    <nav><a href="index.php">Catalog</a></nav>
</header>

<div class="container">
    <div class="card" style="text-align:center;">
        <h2>Order Placed Successfully!</h2>

        <?php if ($order): ?>
            <p>Thank you, <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>. Your order has been received.</p>
        <?php endif; ?>

        <p>Please keep this reference number and show it when you come to collect your order:</p>
        <div class="ref-box"><?php echo htmlspecialchars($ref); ?></div>

        <?php if ($order): ?>
            <table style="margin:20px auto; text-align:left; max-width:400px;">
                <tr><td><strong>Name:</strong></td><td><?php echo htmlspecialchars($order['customer_name']); ?></td></tr>
                <tr><td><strong>Order Code:</strong></td><td><?php echo htmlspecialchars($ref); ?></td></tr>
                <tr><td><strong>Total:</strong></td><td>$<?php echo number_format($order['total_amount'], 2); ?></td></tr>
                <tr><td><strong>Estimated Pickup Date:</strong></td><td><?php echo htmlspecialchars($pickup_date); ?></td></tr>
            </table>
        <?php endif; ?>

        <p>You will be able to collect your order once staff mark it as <strong>Prepared</strong>.</p>
        <a href="index.php" class="btn" style="margin-top:16px; padding:10px 20px;">Back to Catalog</a>
    </div>
</div>
</body>
</html>