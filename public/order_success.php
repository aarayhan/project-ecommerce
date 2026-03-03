<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$order_number = $_GET['order_number'] ?? '';

if (empty($order_number)) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? AND user_id = ?");
$stmt->execute([$order_number, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$order['id']]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - PadViolett</title>
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
        
        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .checkmark-animation {
            animation: checkmark 0.5s ease-in-out;
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
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 pt-24">
        <div class="max-w-2xl mx-auto">
            <!-- Success Message -->
            <div class="bg-white rounded-2xl shadow-xl p-8 text-center mb-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 checkmark-animation">
                    <i class="fas fa-check text-4xl text-green-500"></i>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Pesanan Berhasil!</h1>
                <p class="text-gray-600 mb-6">Terima kasih telah melakukan pemesanan. Pesanan Anda akan segera diproses.</p>
                
                <div class="bg-purple-50 rounded-xl p-4 inline-block">
                    <p class="text-sm text-gray-600">Nomor Pesanan</p>
                    <p class="text-2xl font-bold text-purple-600"><?= htmlspecialchars($order['order_number']) ?></p>
                </div>
            </div>
            
            <!-- Order Details -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Pesanan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Nama Penerima</p>
                        <p class="font-medium"><?= htmlspecialchars($order['customer_name']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="font-medium"><?= htmlspecialchars($order['customer_email']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">No. Telepon</p>
                        <p class="font-medium"><?= htmlspecialchars($order['customer_phone']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                            Menunggu Konfirmasi
                        </span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Alamat Pengiriman</p>
                    <p class="font-medium"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Catatan</p>
                    <p class="font-medium"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Order Items -->
            <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Buku yang Dipesan</h2>
                
                <div class="space-y-4">
                    <?php foreach ($order_items as $item): ?>
                    <div class="flex justify-between items-center border-b pb-4 last:border-b-0">
                        <div>
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($item['title']) ?></p>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($item['author']) ?></p>
                            <p class="text-sm text-gray-500"><?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                        </div>
                        <span class="font-bold text-purple-600">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between text-xl font-bold">
                        <span>Total</span>
                        <span class="text-purple-600">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="my_orders.php" class="flex-1 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-center py-3 px-6 rounded-xl font-semibold hover:opacity-90 transition-all">
                    <i class="fas fa-list mr-2"></i>Lihat Semua Pesanan
                </a>
                <a href="index.php" class="flex-1 bg-white border-2 border-violet-600 text-violet-600 text-center py-3 px-6 rounded-xl font-semibold hover:bg-violet-50 transition-all">
                    <i class="fas fa-home mr-2"></i>Kembali ke Beranda
                </a>
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
