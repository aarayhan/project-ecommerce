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
    } catch (Exception $e) {
        $error = 'Gagal menambahkan buku: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - PadViolett Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-violet-50 to-purple-100 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-violet-600 to-purple-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <a href="dashboard.php" class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center hover:bg-white/30 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-bold">Tambah Buku Baru</h1>
                        <p class="text-sm text-violet-200">Admin Dashboard - PadViolett</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition flex items-center space-x-2">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full"></div>
                        <div class="text-right hidden md:block">
                            <p class="font-semibold"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
                            <p class="text-xs text-violet-200">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Success/Error Messages -->
            <?php if ($success): ?>
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold">Berhasil!</p>
                            <p><?= htmlspecialchars($success) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-semibold">Gagal!</p>
                            <p><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Container -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 p-6 border-b">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-violet-600 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Informasi Buku</h2>
                            <p class="text-gray-600">Isi semua detail buku yang akan ditambahkan ke katalog</p>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <form method="POST" class="p-6 space-y-6">
                    <!-- Row 1: Title & Author -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-heading text-violet-600 mr-2"></i>
                                Judul Buku
                            </label>
                            <div class="relative">
                                <input type="text" name="title" required 
                                       class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition"
                                       placeholder="Masukkan judul buku">
                                <i class="fas fa-book absolute left-4 top-4 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-user-pen text-violet-600 mr-2"></i>
                                Penulis
                            </label>
                            <div class="relative">
                                <input type="text" name="author" required 
                                       class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition"
                                       placeholder="Nama penulis">
                                <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Price & Stock -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-tag text-violet-600 mr-2"></i>
                                Harga (Rp)
                            </label>
                            <div class="relative">
                                <input type="number" name="price" required 
                                       class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition"
                                       placeholder="0">
                                <i class="fas fa-money-bill-wave absolute left-4 top-4 text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Harga dalam Rupiah</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">
                                <i class="fas fa-boxes text-violet-600 mr-2"></i>
                                Stok
                            </label>
                            <div class="relative">
                                <input type="number" name="stock" required 
                                       class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition"
                                       placeholder="0">
                                <i class="fas fa-layer-group absolute left-4 top-4 text-gray-400"></i>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Jumlah buku yang tersedia</p>
                        </div>
                    </div>

                    <!-- Cover URL -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-image text-violet-600 mr-2"></i>
                            Cover Buku
                        </label>
                        <div class="relative">
                            <textarea name="cover" rows="2"
                                      class="w-full px-4 py-3 pl-11 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition resize-none"
                                      placeholder="https://example.com/book-cover.jpg"></textarea>
                            <i class="fas fa-link absolute left-4 top-4 text-gray-400"></i>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-sm text-gray-500">URL gambar atau Base64 data</p>
                            <span class="text-xs text-violet-600">Opsional</span>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-tags text-violet-600 mr-2"></i>
                            Kategori
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 border border-gray-300 rounded-xl max-h-60 overflow-y-auto bg-gray-50">
                            <?php 
                            $allCategories = [
                                'Fiksi', 'Non-Fiksi', 'Sejarah', 'Sains', 
                                'Teknologi', 'Biografi', 'Anak', 'Klasik', 
                                'Self Improvement', 'Romance', 'Fantasi', 'Misteri',
                                'Bisnis', 'Kesehatan', 'Seni', 'Filsafat'
                            ];
                            
                            foreach ($allCategories as $cat): 
                            ?>
                                <label class="flex items-center space-x-2 p-2 hover:bg-violet-50 rounded-lg cursor-pointer transition">
                                    <input type="checkbox" name="categories[]" value="<?= htmlspecialchars($cat) ?>" 
                                           class="w-5 h-5 text-violet-600 rounded focus:ring-violet-500 border-gray-300">
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($cat) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Pilih satu atau lebih kategori yang sesuai</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">
                            <i class="fas fa-align-left text-violet-600 mr-2"></i>
                            Sinopsis
                        </label>
                        <textarea name="description" rows="5" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition resize-none"
                                  placeholder="Tulis sinopsis atau deskripsi singkat tentang buku ini..."></textarea>
                        <p class="text-sm text-gray-500 mt-2">Deskripsi yang menarik akan meningkatkan penjualan</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 border-t">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" 
                                    class="flex-1 bg-gradient-to-r from-violet-600 to-purple-600 text-white font-semibold py-4 px-6 rounded-xl hover:from-violet-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl active:scale-[0.98] flex items-center justify-center space-x-2">
                                <i class="fas fa-plus"></i>
                                <span>Tambah Buku ke Katalog</span>
                            </button>
                            
                            <a href="dashboard.php" 
                               class="px-6 py-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition flex items-center justify-center space-x-2">
                                <i class="fas fa-times"></i>
                                <span>Batal</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Quick Tips -->
            <div class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 mb-2">Tips Menambahkan Buku:</h3>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Gunakan judul yang jelas dan menarik perhatian</li>
                            <li>• Pastikan harga sesuai dengan kualitas dan kategori buku</li>
                            <li>• Pilih kategori yang tepat untuk memudahkan pencarian</li>
                            <li>• Gunakan URL gambar yang jelas dan berkualitas tinggi</li>
                            <li>• Sinopsis yang informatif akan membantu pembeli</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-gradient-to-r from-violet-600 to-purple-600 rounded-lg"></div>
                    <span class="font-bold text-gray-800">PadViolett Admin</span>
                </div>
                <div class="text-sm text-gray-600">
                    © <?= date('Y') ?> PadViolett Bookstore. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Form validation enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const price = document.querySelector('input[name="price"]');
            const stock = document.querySelector('input[name="stock"]');
            
            if (parseInt(price.value) < 0) {
                alert('Harga tidak boleh negatif!');
                e.preventDefault();
                price.focus();
                return false;
            }
            
            if (parseInt(stock.value) < 0) {
                alert('Stok tidak boleh negatif!');
                e.preventDefault();
                stock.focus();
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>