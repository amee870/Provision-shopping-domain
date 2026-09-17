<?php
require_once '../config.php';
 
$error = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
 
    $stmt = $conn->prepare('SELECT user_id, full_name, password_hash, role FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php');
            exit;
        }
    }
 
    $error = 'Invalid username or password.';
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Login - Provision Shop</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
 
<header>
    <h1>Provision Shop Ordering System</h1>
    <nav><a href="../index.php">Back to Catalog</a></nav>
</header>
 
<div class="container">
    <h2>Staff / Admin Login</h2>
 
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
 
    <div class="card" style="max-width:360px;">
        <form method="POST">
            <label>Username</label><br>
            <input type="text" name="username" required style="width:100%; margin:8px 0;"><br>
            <label>Password</label><br>
            <input type="password" name="password" required style="width:100%; margin:8px 0;"><br><br>
            <button type="submit" style="width:100%;">Log In</button>
        </form>
    </div>
</div>
</body>
</html>
 
