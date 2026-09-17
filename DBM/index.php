<?php
require_once 'config.php';
 
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int) $_POST['product_id'];
    $qty = max(1, (int) $_POST['quantity']);
 
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
 
    header('Location: index.php?added=1');
    exit;
}
 
$result = $conn->query('SELECT * FROM products ORDER BY category, name');
$cart_count = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Provision Shop - Product Catalog</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
 
<header>
    <h1>Provision Shop Ordering System</h1>
    <nav>
        <a href="index.php">Catalog</a>
        <a href="cart.php">Cart (<?php echo $cart_count; ?>)</a>
        <a href="admin/login.php">Staff Login</a>
    </nav>
</header>
 
<div class="container">
 
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success">Item added to your cart.</div>
    <?php endif; ?>
 
    <h2>Available Products</h2>
    <p>Browse products below, choose a quantity, and add them to your cart. When you're ready, go to your cart to place the order.</p>
 
    <div class="grid" style="margin-top:20px;">
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="card product-card">
                <?php if (!empty($product['image_url'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
                <?php else: ?>
                    <div class="product-img placeholder-img">No Image</div>
                <?php endif; ?>
 
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <div style="color:#888; font-size:0.85rem;"><?php echo htmlspecialchars($product['category']); ?></div>
                <div class="price">$<?php echo number_format($product['price'], 2); ?></div>
 
                <?php if ($product['stock_qty'] > 0): ?>
                    <div class="stock <?php echo $product['stock_qty'] <= 5 ? 'low' : ''; ?>">
                        <?php echo $product['stock_qty']; ?> in stock
                    </div>
                    <form method="POST" class="inline">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_qty']; ?>" style="width:60px;">
                        <button type="submit" name="add_to_cart">Add</button>
                    </form>
                <?php else: ?>
                    <div class="stock low">Out of stock</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
 
</div>
</body>
</html>
 

