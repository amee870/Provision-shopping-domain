<?php
// Include this at the top of any admin-only page.
// Assumes config.php has already been required (session is started there).
 
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
