<?php
require_once '../config.php';
require_once '../includes/auth.php';
 
// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = (int) $_POST['order_id'];
    $status = $_POST['status'];
    $allowed = ['Pending', 'Prepared', 'Collected'];
 
    if (in_array($status, $allowed, true)) {
        $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE order_id = ?');
        $stmt->bind_param('si', $status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: orders.php');
    exit;
}
 
$filter = $_GET['status'] ?? 'all';
$sql = 'SELECT * FROM orders';
if (in_array($filter, ['Pending', 'Prepared', 'Collected'], true)) {
    $sql .= " WHERE status = '" . $conn->real_escape_string($filter) . "'";
}
$sql .= ' ORDER BY order_date DESC';
$orders = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Orders - Provision Shop</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
 
<header>
    <h1>Manage Orders</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="products.php">Products</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>
 
<div class="container">
    <div class="card" style="margin-bottom:16px;">
        <a href="orders.php" class="btn">All</a>
        <a href="orders.php?status=Pending" class="btn">Pending</a>
        <a href="orders.php?status=Prepared" class="btn">Prepared</a>
        <a href="orders.php?status=Collected" class="btn">Collected</a>
    </div>
 
    <div class="card">
        <table>
            <tr><th>Reference</th><th>Customer</th><th>Contact</th><th>Total</th><th>Status</th><th>Update</th></tr>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['reference_no']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['customer_contact']); ?></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><span class="badge badge-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></td>
                    <td>
                        <form method="POST" class="inline">
                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                            <select name="status">
                                <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Prepared" <?php echo $order['status'] === 'Prepared' ? 'selected' : ''; ?>>Prepared</option>
                                <option value="Collected" <?php echo $order['status'] === 'Collected' ? 'selected' : ''; ?>>Collected</option>
                            </select>
                            <button type="submit">Save</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
