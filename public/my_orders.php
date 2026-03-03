<?php
session_start();
require_once '../config/database.php';
require_once '../config/cart_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$cart_count = getCartCount();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - PadViolett</title>
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
                    <a href="cart.php" class="relative text-gray-600 hover:text-gray-800">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center"><?= $cart_count ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="index.php" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-home mr-1"></i>Beranda
                    </a>
                    <a href="../auth/logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 pt-24">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Pesanan Saya</h1>
        
        <?php if (empty($orders)): ?>
            <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-6"></i>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Belum Ada Pesanan</h2>
                <p class="text-gray-600 mb-6">Anda belum melakukan pemesanan apapun.</p>
                <a href="index.php" class="inline-block bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:opacity-90 transition-all">
                    <i class="fas fa-book mr-2"></i>Mulai Belanja
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($orders as $order): 
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];
                    $statusLabels = [
                        'pending' => 'Menunggu Konfirmasi',
                        'processing' => 'Sedang Diproses',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan'
                    ];
                    
                    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                    $stmt->execute([$order['id']]);
                    $order_items = $stmt->fetchAll();
                ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Order Header -->
                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <p class="font-bold text-lg text-gray-800"><?= htmlspecialchars($order['order_number']) ?></p>
                            <p class="text-sm text-gray-500"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusColors[$order['status']] ?>">
                            <?= $statusLabels[$order['status']] ?>
                        </span>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="p-6">
                        <div class="space-y-3 mb-4">
                            <?php foreach ($order_items as $item): ?>
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($item['title']) ?></p>
                                    <p class="text-gray-500"><?= $item['quantity'] ?> x Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
                                </div>
                                <span class="font-medium">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="border-t pt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Total Pesanan</p>
                                <p class="text-xl font-bold text-purple-600">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></p>
                            </div>
                            <button onclick="toggleOrderDetail(<?= $order['id'] ?>)" class="text-purple-600 hover:text-purple-800 font-medium">
                                <i class="fas fa-chevron-down mr-1"></i>Lihat Detail
                            </button>
                        </div>
                        
                        <!-- Hidden Detail -->
                        <div id="order-detail-<?= $order['id'] ?>" class="hidden mt-4 pt-4 border-t">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Nama Penerima</p>
                                    <p class="font-medium"><?= htmlspecialchars($order['customer_name']) ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-500">No. Telepon</p>
                                    <p class="font-medium"><?= htmlspecialchars($order['customer_phone']) ?></p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-gray-500">Alamat Pengiriman</p>
                                    <p class="font-medium"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
                                </div>
                                <?php if (!empty($order['notes'])): ?>
                                <div class="md:col-span-2">
                                    <p class="text-gray-500">Catatan</p>
                                    <p class="font-medium"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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
        function toggleOrderDetail(orderId) {
            const detail = document.getElementById('order-detail-' + orderId);
            if (detail.classList.contains('hidden')) {
                detail.classList.remove('hidden');
            } else {
                detail.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
