<?php
session_start();
require_once '../config/database.php';
require_once '../config/cart_helper.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action == 'add') {
    $book_id = $_POST['book_id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $price = $_POST['price'] ?? 0;
    $cover = $_POST['cover'] ?? '';
    $stock = $_POST['stock'] ?? 1;
    
    if ($book_id > 0 && !empty($title)) {
        addToCart($book_id, $title, $author, $price, $cover, $stock);
        echo json_encode([
            'success' => true,
            'cart_count' => getCartCount()
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Data buku tidak valid'
        ]);
    }
} elseif ($action == 'update') {
    $book_id = $_POST['book_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    updateCartQuantity($book_id, $quantity);
    echo json_encode([
        'success' => true,
        'cart_count' => getCartCount(),
        'cart_total' => getCartTotal()
    ]);
} elseif ($action == 'remove') {
    $book_id = $_POST['book_id'] ?? 0;
    
    removeFromCart($book_id);
    echo json_encode([
        'success' => true,
        'cart_count' => getCartCount(),
        'cart_total' => getCartTotal()
    ]);
} elseif ($action == 'clear') {
    clearCart();
    echo json_encode([
        'success' => true,
        'cart_count' => 0,
        'cart_total' => 0
    ]);
} elseif ($action == 'get') {
    echo json_encode([
        'success' => true,
        'cart' => getCart(),
        'cart_count' => getCartCount(),
        'cart_total' => getCartTotal()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
