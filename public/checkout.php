<?php
session_start();
require_once '../config/database.php';
require_once '../config/cart_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php?redirect=cart");
    exit();
}

$cart = getCart();
$cart_total = getCartTotal();
$cart_count = getCartCount();

if (empty($cart)) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = false;

if ($_POST) {
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $shipping_address = $_POST['shipping_address'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($customer_name) || empty($customer_email) || empty($customer_phone) || empty($shipping_address)) {
        $error = 'Mohon lengkapi semua data pengiriman!';
    } else {
        try {
            $pdo->beginTransaction();
            
            $order_number = generateOrderNumber();
            $total_with_tax = $cart_total * 1.1;
            
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, status, customer_name, customer_email, customer_phone, shipping_address, notes) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $order_number,
                $total_with_tax,
                $customer_name,
                $customer_email,
                $customer_phone,
                $shipping_address,
                $notes
            ]);
            
            $order_id = $pdo->lastInsertId();
            
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, title, author, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($cart as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $stmt->execute([
                    $order_id,
                    $item['book_id'],
                    $item['title'],
                    $item['author'],
                    $item['price'],
                    $item['quantity'],
                    $subtotal
                ]);
                
                $updateStock = $pdo->prepare("UPDATE books SET stock = stock - ? WHERE id = ?");
                $updateStock->execute([$item['quantity'], $item['book_id']]);
            }
            
            $pdo->commit();
            
            clearCart();
            
            header("Location: order_success.php?order_number=" . $order_number);
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM user WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - PadViolett</title>
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
                    <a href="cart.php" class="text-gray-600 hover:text-gray-800 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 pt-24">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Checkout</h1>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form method="POST" class="bg-white rounded-xl shadow-lg p-6 space-y-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Data Pengiriman</h2>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="customer_name" value="<?= htmlspecialchars($user['username']) ?>" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" name="customer_email" value="<?= htmlspecialchars($user['email']) ?>" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">No. Telepon</label>
                            <input type="tel" name="customer_phone" placeholder="0812xxxxxxx" required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Pengiriman</label>
                        <textarea name="shipping_address" rows="3" required 
                                  placeholder="Jl. Nama Jalan, No. Rumah, Kota, Provinsi, Kode Pos"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Catatan untuk pesanan..."
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold py-4 px-6 rounded-xl hover:opacity-90 transition-all">
                        <i class="fas fa-check-circle mr-2"></i>Konfirmasi Pesanan
                    </button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-3 mb-6 max-h-64 overflow-y-auto">
                        <?php foreach ($cart as $item): ?>
                        <div class="flex justify-between items-center text-sm">
                            <div>
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($item['title']) ?></p>
                                <p class="text-gray-500"><?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                            </div>
                            <span class="font-medium">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($cart_total, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>PPN (10%)</span>
                            <span>Rp <?= number_format($cart_total * 0.1, 0, ',', '.') ?></span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between text-xl font-bold text-gray-800">
                            <span>Total</span>
                            <span class="text-purple-600">Rp <?= number_format($cart_total * 1.1, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pesanan akan diproses setelah konfirmasi. Silakan catat nomor pesanan Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-violet-600 to-indigo-700 rounded-t-2xl shadow-lg p-8 text-white mt-16">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 PadViolett. Semua hak dilindungi.</p>
        </div>
    </footer>
</body>
</html>
