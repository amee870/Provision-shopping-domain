<?php
require_once '../config.php';
require_once '../includes/auth.php';
 
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = ['name' => '', 'category' => '', 'price' => '', 'stock_qty' => ''];
$is_edit = false;
 
if ($id) {
    $stmt = $conn->prepare('SELECT * FROM products WHERE product_id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $product = $row;
        $is_edit = true;
    }
    $stmt->close();
}
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = (float) $_POST['price'];
    $stock_qty = (int) $_POST['stock_qty'];
 
    if ($is_edit) {
        $stmt = $conn->prepare('UPDATE products SET name=?, category=?, price=?, stock_qty=? WHERE product_id=?');
        $stmt->bind_param('ssdii', $name, $category, $price, $stock_qty, $id);
    } else {
        $stmt = $conn->prepare('INSERT INTO products (name, category, price, stock_qty) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssdi', $name, $category, $price, $stock_qty);
    }
    $stmt->execute();
    $stmt->close();
 
    header('Location: products.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $is_edit ? 'Edit' : 'Add'; ?> Product - Provision Shop</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
 
<header>
    <h1><?php echo $is_edit ? 'Edit' : 'Add'; ?> Product</h1>
    <nav><a href="products.php">Back to Products</a></nav>
</header>
 
<div class="container">
    <div class="card" style="max-width:420px;">
        <form method="POST">
            <label>Product Name</label><br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required style="width:100%; margin:8px 0;"><br>
 
            <label>Category</label><br>
            <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required style="width:100%; margin:8px 0;"><br>
 
            <label>Price</label><br>
            <input type="number" step="0.01" min="0" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required style="width:100%; margin:8px 0;"><br>
 
            <label>Stock Quantity</label><br>
            <input type="number" min="0" name="stock_qty" value="<?php echo htmlspecialchars($product['stock_qty']); ?>" required style="width:100%; margin:8px 0;"><br><br>
 
            <button type="submit" style="width:100%;"><?php echo $is_edit ? 'Save Changes' : 'Add Product'; ?></button>
        </form>
    </div>
</div>
</body>
</html>
 