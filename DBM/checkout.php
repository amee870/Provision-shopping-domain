<?php
require_once 'config.php';
 
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}
 
$error = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name']);
    $customer_contact = trim($_POST['customer_contact']);
 
    if ($customer_name === '' || $customer_contact === '') {
        $error = 'Please enter your name and contact number.';
    } else {
        // --- Validate stock availability for every item before creating the order ---
        $ids = array_map('intval', array_keys($_SESSION['cart']));
        $in = implode(',', $ids);
        $result = $conn->query("SELECT * FROM products WHERE product_id IN ($in)");
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[$row['product_id']] = $row;
        }
 
        foreach ($_SESSION['cart'] as $pid => $qty) {
            if (!isset($products[$pid]) || $products[$pid]['stock_qty'] < $qty) {
                $error = 'Sorry, "' . htmlspecialchars($products[$pid]['name'] ?? 'a product') . '" no longer has enough stock. Please update your cart.';
                break;
            }
        }
 
        if ($error === '') {
            // --- All items valid: create the order ---
            $conn->begin_transaction();
            try {
                $reference_no = 'PS-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -5));
                $total = 0;
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    $total += $products[$pid]['price'] * $qty;
                }
 
                $stmt = $conn->prepare(
                    'INSERT INTO orders (reference_no, customer_name, customer_contact, status, total_amount)
                     VALUES (?, ?, ?, "Pending", ?)'
                );
                $stmt->bind_param('sssd', $reference_no, $customer_name, $customer_contact, $total);
                $stmt->execute();
                $order_id = $conn->insert_id;
                $stmt->close();
 
                $item_stmt = $conn->prepare(
                    'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)'
                );
                $stock_stmt = $conn->prepare(
                    'UPDATE products SET stock_qty = stock_qty - ? WHERE product_id = ?'
                );
 
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    $price = $products[$pid]['price'];
                    $item_stmt->bind_param('iiid', $order_id, $pid, $qty, $price);
                    $item_stmt->execute();
 
                    $stock_stmt->bind_param('ii', $qty, $pid);
                    $stock_stmt->execute();
                }
 
                $item_stmt->close();
                $stock_stmt->close();
                $conn->commit();
 
                $_SESSION['cart'] = []; // clear cart
                header('Location: order_confirmation.php?ref=' . urlencode($reference_no));
                exit;
 
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Something went wrong while placing your order. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - Provision Shop</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
 
<header>
    <h1>Provision Shop Ordering System</h1>
    <nav><a href="index.php">Catalog</a> <a href="cart.php">Cart</a></nav>
</header>
 
<div class="container">
    <h2>Checkout</h2>
 
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
 
    <div class="card" style="max-width:400px;">
        <form method="POST">
            <label>Full Name</label><br>
            <input type="text" name="customer_name" required style="width:100%; margin:8px 0;"><br>
            <label>Contact Number</label><br>
            <input type="text" name="customer_contact" required style="width:100%; margin:8px 0;"><br><br>
            <button type="submit" style="width:100%;">Place Order</button>
        </form>
    </div>
</div>
</body>
</html>
