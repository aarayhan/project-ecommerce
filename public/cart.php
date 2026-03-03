<?php
session_start();
require_once '../config/database.php';
require_once '../config/cart_helper.php';

$cart = getCart();
$cart_total = getCartTotal();
$cart_count = getCartCount();

$page_title = 'Keranjang Belanja';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - PadViolett</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Navigation -->
    <nav class="glass-effect shadow-lg fixed top-0 w-full z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <img src="../assets/images/PadViolett-logo.png" alt="PadViolett Logo" class="w-10 h-10 rounded-lg" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-10 h-10 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-lg flex items-center justify-center hidden">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">PadViolett</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-gray-600 hover:text-gray-800 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Katalog
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="my_orders.php" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-box mr-1"></i>Pesanan Saya
                        </a>
                        <a href="../auth/logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
                    <?php else: ?>
                        <a href="../auth/login.php" class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:opacity-90">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 pt-24">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Keranjang Belanja</h1>
        
        <?php if (empty($cart)): ?>
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Keranjang Anda Kosong</h2>
                <p class="text-gray-600 mb-6">Silakan pilih buku dari katalog kami untuk mulai belanja.</p>
                <a href="index.php" class="inline-block bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:opacity-90 transition-all">
                    <i class="fas fa-book mr-2"></i>Lihat Katalog Buku
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2 space-y-4">
                    <?php foreach ($cart as $book_id => $item): ?>
                    <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col sm:flex-row gap-4" id="cart-item-<?= $book_id ?>">
                        <!-- Book Cover -->
                        <div class="w-full sm:w-32 h-40 sm:h-32 flex-shrink-0">
                            <?php if ($item['cover'] && (filter_var($item['cover'], FILTER_VALIDATE_URL) || strpos($item['cover'], 'data:image/') === 0)): ?>
                                <img src="<?= htmlspecialchars($item['cover']) ?>" 
                                     alt="<?= htmlspecialchars($item['title']) ?>" 
                                     class="w-full h-full object-cover rounded-lg"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg hidden flex items-center justify-center">
                                    <i class="fas fa-book text-white text-2xl"></i>
                                </div>
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-book text-white text-2xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Book Info -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800 mb-1"><?= htmlspecialchars($item['title']) ?></h3>
                                <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($item['author']) ?></p>
                                <p class="text-purple-600 font-bold text-xl">Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                            </div>
                            
                            <div class="flex items-center justify-between mt-4">
                                <!-- Quantity Controls -->
                                <div class="flex items-center border rounded-lg">
                                    <button onclick="updateQuantity(<?= $book_id ?>, <?= $item['quantity'] - 1 ?>)" 
                                            class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="px-4 py-1 font-medium" id="qty-<?= $book_id ?>"><?= $item['quantity'] ?></span>
                                    <button onclick="updateQuantity(<?= $book_id ?>, <?= $item['quantity'] + 1 ?>)" 
                                            <?= $item['quantity'] >= $item['stock'] ? 'disabled class="px-3 py-1 text-gray-300 cursor-not-allowed"' : 'class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg"' ?>>
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                
                                <!-- Subtotal & Remove -->
                                <div class="flex items-center space-x-4">
                                    <span class="font-bold text-gray-800" id="subtotal-<?= $book_id ?>">
                                        Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                                    </span>
                                    <button onclick="removeItem(<?= $book_id ?>)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <a href="index.php" class="inline-flex items-center text-purple-600 hover:text-purple-800">
                        <i class="fas fa-arrow-left mr-2"></i>Lanjut Belanja
                    </a>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Ringkasan Pesanan</h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Total Item (<?= $cart_count ?>)</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>Rp <?= number_format($cart_total, 0, ',', '.') ?></span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Ppn (10%)</span>
                                <span>Rp <?= number_format($cart_total * 0.1, 0, ',', '.') ?></span>
                            </div>
                            <hr class="my-4">
                            <div class="flex justify-between text-xl font-bold text-gray-800">
                                <span>Total</span>
                                <span class="text-purple-600">Rp <?= number_format($cart_total * 1.1, 0, ',', '.') ?></span>
                            </div>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="checkout.php" class="block w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-center py-3 px-6 rounded-xl font-semibold hover:opacity-90 transition-all">
                                <i class="fas fa-shopping-bag mr-2"></i>Checkout
                            </a>
                        <?php else: ?>
                            <a href="../auth/login.php?redirect=cart" class="block w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-center py-3 px-6 rounded-xl font-semibold hover:opacity-90 transition-all">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login untuk Checkout
                            </a>
                            <p class="text-sm text-gray-500 text-center mt-2">Anda harus login untuk checkout</p>
                        <?php endif; ?>
                        
                        <button onclick="clearCart()" class="mt-4 w-full text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash mr-1"></i>Kosongkan Keranjang
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-violet-600 to-indigo-700 rounded-t-2xl shadow-lg p-8 text-white mt-16">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 PadViolett. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script>
        function updateQuantity(bookId, quantity) {
            fetch('cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=update&book_id=' + bookId + '&quantity=' + quantity
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (quantity <= 0) {
                        document.getElementById('cart-item-' + bookId).remove();
                    } else {
                        document.getElementById('qty-' + bookId).textContent = quantity;
                        document.getElementById('subtotal-' + bookId).textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.cart_total / data.cart_count * quantity);
                    }
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function removeItem(bookId) {
            if (confirm('Apakah Anda yakin ingin menghapus buku ini dari keranjang?')) {
                fetch('cart_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=remove&book_id=' + bookId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('cart-item-' + bookId).remove();
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function clearCart() {
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                fetch('cart_ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=clear'
                })
                .then(response => response.json())
                .then(data => {
                    location.reload();
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
</body>
</html>
