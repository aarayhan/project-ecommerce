<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getCart() {
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

function saveCart($cart) {
    $_SESSION['cart'] = $cart;
}

function addToCart($book_id, $title, $author, $price, $cover = '', $stock = 1) {
    $cart = getCart();
    
    if (isset($cart[$book_id])) {
        if ($cart[$book_id]['quantity'] < $stock) {
            $cart[$book_id]['quantity']++;
        }
    } else {
        $cart[$book_id] = [
            'book_id' => $book_id,
            'title' => $title,
            'author' => $author,
            'price' => $price,
            'cover' => $cover,
            'stock' => $stock,
            'quantity' => 1
        ];
    }
    
    saveCart($cart);
    return $cart;
}

function updateCartQuantity($book_id, $quantity) {
    $cart = getCart();
    
    if (isset($cart[$book_id])) {
        if ($quantity <= 0) {
            unset($cart[$book_id]);
        } elseif ($quantity <= $cart[$book_id]['stock']) {
            $cart[$book_id]['quantity'] = $quantity;
        }
    }
    
    saveCart($cart);
    return $cart;
}

function removeFromCart($book_id) {
    $cart = getCart();
    
    if (isset($cart[$book_id])) {
        unset($cart[$book_id]);
    }
    
    saveCart($cart);
    return $cart;
}

function clearCart() {
    $_SESSION['cart'] = [];
}

function getCartTotal() {
    $cart = getCart();
    $total = 0;
    
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    return $total;
}

function getCartCount() {
    $cart = getCart();
    $count = 0;
    
    foreach ($cart as $item) {
        $count += $item['quantity'];
    }
    
    return $count;
}

function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(uniqid());
}
