<?php
session_start();
require_once '../config/database.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: dashboard.php?deleted=1");
    } catch (Exception $e) {
        header("Location: dashboard.php?error=1");
    }
} else {
    header("Location: dashboard.php");
}
exit();
?>