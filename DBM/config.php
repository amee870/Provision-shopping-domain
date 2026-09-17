<?php
// ============================================================
// Database connection settings — edit these to match your setup
// ============================================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'provision_shop');
 
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
 
$conn->set_charset('utf8mb4');
 
// Start session for cart + admin login (safe to call from any page)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
