<?php
require_once '../config.php';
require_once '../includes/auth.php';
 
// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM products WHERE product_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: products.php');
    exit;
}
 
$products = $conn->query('SELECT * FROM products ORDER BY category, name');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Products - Provision Shop</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
 
<header>
    <h1>Manage Products</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
 
<div class="container">
    <a href="product_form.php" class="btn" style="padding:10px 18px;">+ Add New Product</a>
 
    <div class="card" style="margin-top:16px;">
        <table>
            <tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
            <?php while ($p = $products->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo htmlspecialchars($p['category']); ?></td>
                    <td>$<?php echo number_format($p['price'], 2); ?></td>
                    <td><?php echo $p['stock_qty']; ?></td>
                    <td>
                        <a href="product_form.php?id=<?php echo $p['product_id']; ?>">Edit</a>
                        &nbsp;|&nbsp;
                        <a href="products.php?delete=<?php echo $p['product_id']; ?>"
                           style="color:#c0392b;"
                           onclick="return confirm('Delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
