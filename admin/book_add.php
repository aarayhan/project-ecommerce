<?php
session_start();
require_once '../config/database.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $cover = $_POST['cover'];
    
    // Handle multiple categories
    $categories = $_POST['categories'] ?? [];
    $category = implode(', ', $categories); // Join with comma
    
    try {
        $stmt = $pdo->prepare("INSERT INTO books (title, author, price, description, cover, stock, category) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $author, $price, $description, $cover, $stock, $category]);
        $success = 'Buku berhasil ditambahkan!';
        header("Location: dashboard.php?added=1");
    } catch (Exception $e) {
        $error = 'Gagal menambahkan buku: ' . $e->getMessage();
    }
}
?>