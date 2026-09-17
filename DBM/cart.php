<?php
require_once 'config.php';
 
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
 
// Handle remove item
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][(int) $_GET['remove']]);
    header('Location: cart.php');
    exit;
}
 
$cart_items = [];
$total = 0;
 
if (!empty($_SESSION['cart'])) {
    $ids = array_map('intval', array_keys($_SESSION['cart']));
    $in = implode(',', $ids);
    $result = $conn->query("SELECT * FROM products WHERE product_id IN ($in)");
    while ($row = $result->fetch_assoc()) {
        $qty = $_SESSION['cart'][$row['product_id']];
        $subtotal = $qty * $row['price'];
        $total += $subtotal;
        $cart_items[] = [
            'product' => $row,
            'qty' => $qty,
            'subtotal' => $subtotal,
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart - Provision Shop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
 
<header>
    <h1>Provision Shop Ordering System</h1>
    <nav>
        <a href="index.php">Catalog</a>
        <a href="cart.php">Cart</a>
        <a href="admin/login.php">Staff Login</a>
    </nav>
</header>
 
<div class="container">
    <h2>Your Cart</h2>
 
    <?php if (empty($cart_items)): ?>
        <div class="card">
            <p>Your cart is empty. <a href="index.php">Browse products</a> to get started.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <table>
                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th><th></th></tr>
                <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product']['name']); ?></td>
                        <td><?php echo $item['qty']; ?></td>
                        <td>$<?php echo number_format($item['product']['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                        <td><a href="cart.php?remove=<?php echo $item['product']['product_id']; ?>" style="color:#c0392b;">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <h3 style="text-align:right; margin-top:16px;">Total: $<?php echo number_format($total, 2); ?></h3>
        </div>
 
        <a href="checkout.php" class="btn" style="padding:12px 24px;">Proceed to Checkout</a>
    <?php endif; ?>
</div>
</body>
</html>
