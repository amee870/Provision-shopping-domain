<?php
require_once '../config.php';
require_once '../includes/auth.php';
 
$pending = $conn->query("SELECT COUNT(*) c FROM orders WHERE status = 'Pending'")->fetch_assoc()['c'];
$prepared = $conn->query("SELECT COUNT(*) c FROM orders WHERE status = 'Prepared'")->fetch_assoc()['c'];
$collected_today = $conn->query("SELECT COUNT(*) c FROM orders WHERE status = 'Collected' AND DATE(order_date) = CURDATE()")->fetch_assoc()['c'];
$low_stock = $conn->query("SELECT COUNT(*) c FROM products WHERE stock_qty <= 5")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Provision Shop</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
 
<header>
    <h1>Admin Dashboard</h1>
    <nav>
        <span>Hi, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <a href="orders.php">Orders</a>
        <a href="products.php">Products</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
 
<div class="container">
    <div class="grid">
        <div class="card"><h3>Pending Orders</h3><p style="font-size:2rem;"><?php echo $pending; ?></p></div>
        <div class="card"><h3>Prepared (awaiting pickup)</h3><p style="font-size:2rem;"><?php echo $prepared; ?></p></div>
        <div class="card"><h3>Collected Today</h3><p style="font-size:2rem;"><?php echo $collected_today; ?></p></div>
        <div class="card"><h3>Low Stock Items</h3><p style="font-size:2rem;"><?php echo $low_stock; ?></p></div>
    </div>
 
    <div class="card" style="margin-top:20px;">
        <a href="orders.php" class="btn" style="padding:10px 18px;">Manage Orders</a>
        <a href="products.php" class="btn" style="padding:10px 18px;">Manage Products</a>
    </div>
</div>
</body>
</html>
