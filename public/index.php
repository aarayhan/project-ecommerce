<?php
session_start();
require_once '../config/database.php';
require_once '../config/cart_helper.php';

$cart_count = getCartCount();

// Get search parameters
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';

// Build query with search and filter
$query = "SELECT * FROM books WHERE stock > 0";
$params = [];

// Add search condition
if (!empty($search)) {
    $query .= " AND (title LIKE ? OR author LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Add category filter
if (!empty($category_filter)) {
    $query .= " AND (category LIKE ? OR category LIKE ? OR category LIKE ?)";
    $params[] = "%$category_filter%";           // For single category
    $params[] = "$category_filter,%";           // For first in list
    $params[] = "%, $category_filter%";         // For middle/end in list
}

$query .= " ORDER BY id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadViolett</title>
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
        
        /* Line Clamp Utilities */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Smooth transitions for all elements */
        * {
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Card hover effects */
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Gradient overlay effects */
        .gradient-overlay {
            background: linear-gradient(to top, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(255, 255, 255, 0.4) 50%, 
                transparent 100%);
        }
        
        /* Text shadow for better readability on gradient */
        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        /* Custom gradient backgrounds */
        .bg-gradient-purple-pink {
            background: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        }
        
        .bg-gradient-blue-indigo {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
        }
        
        .bg-gradient-red-orange {
            background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
        }
        
        .bg-gradient-green-teal {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Navigation untuk User -->
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800"><?= $_SESSION['username'] ?></p>
                                <p class="text-sm text-gray-500">Pengguna PadViolett</p>
                            </div>
                        </div>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <a href="../admin/dashboard.php" class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:opacity-90">Admin Panel</a>
                        <?php endif; ?>
                        <a href="../auth/logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
                    <?php else: ?>
                        <a href="../auth/register.php" class="text-gray-600 hover:text-gray-800 mr-4">Daftar</a>
                        <a href="../auth/login.php" class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:opacity-90">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 pt-24">
        <!-- Search and Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Pencarian & Filter Buku</h3>
            
            <form method="GET" class="space-y-6">
                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="relative">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Cari judul buku atau nama penulis..." 
                               class="w-full p-4 pl-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Category Filter -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="font-medium text-gray-700 mb-3">Filter Berdasarkan Genre</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="submit" name="category" value="" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= empty($category_filter) ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Semua Genre
                            </button>
                            <button type="submit" name="category" value="Fiksi" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Fiksi' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Fiksi
                            </button>
                            <button type="submit" name="category" value="Non-Fiksi" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Non-Fiksi' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Non-Fiksi
                            </button>
                            <button type="submit" name="category" value="Sejarah" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Sejarah' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Sejarah
                            </button>
                            <button type="submit" name="category" value="Sains" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Sains' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Sains
                            </button>
                            <button type="submit" name="category" value="Teknologi" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Teknologi' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Teknologi
                            </button>
                            <button type="submit" name="category" value="Biografi" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Biografi' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Biografi
                            </button>
                            <button type="submit" name="category" value="Anak" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Anak' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Anak
                            </button>
                            <button type="submit" name="category" value="Klasik" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Klasik' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Klasik
                            </button>
                            <button type="submit" name="category" value="Self Improvement" 
                                    class="px-4 py-2 rounded-full text-sm transition-colors <?= $category_filter == 'Self Improvement' ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-800 hover:bg-violet-200' ?>">
                                Self Improvement
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex items-end">
                        <div class="w-full">
                            <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl hover:opacity-90 transition-all">
                                <i class="fas fa-search mr-2"></i>Cari Buku
                            </button>
                            <?php if (!empty($search) || !empty($category_filter)): ?>
                                <a href="?" class="block w-full text-center mt-2 text-gray-600 hover:text-gray-800 text-sm">
                                    <i class="fas fa-times mr-1"></i>Reset Pencarian
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Preserve search when filtering by category -->
                <?php if (!empty($search)): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <?php endif; ?>
            </form>
        </div>

        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">
                <?php if (!empty($search) || !empty($category_filter)): ?>
                    Hasil Pencarian
                <?php else: ?>
                    Koleksi Buku Terbaik
                <?php endif; ?>
            </h2>
            <p class="text-gray-600 text-lg">
                <?php if (!empty($search) || !empty($category_filter)): ?>
                    <?php
                    $result_info = [];
                    if (!empty($search)) $result_info[] = "\"" . htmlspecialchars($search) . "\"";
                    if (!empty($category_filter)) $result_info[] = "Genre: " . htmlspecialchars($category_filter);
                    ?>
                    Menampilkan <?= count($books) ?> buku untuk <?= implode(' dan ', $result_info) ?>
                <?php else: ?>
                    Temukan buku favorit Anda dengan harga terjangkau
                <?php endif; ?>
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($books as $book): ?>
            <!-- Product Card with Gradient Header -->
            <div class="w-full mx-auto">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    
                    <!-- Header with Gradient Background -->
                    <div class="relative h-48 sm:h-64 md:h-72 bg-gradient-to-r from-purple-500 to-pink-600">
                        
                        <!-- Cover Image (if available) -->
                        <?php if ($book['cover'] && (filter_var($book['cover'], FILTER_VALIDATE_URL) || strpos($book['cover'], 'data:image/') === 0)): ?>
                            <img src="<?= htmlspecialchars($book['cover']) ?>" 
                                 alt="<?= htmlspecialchars($book['title']) ?>" 
                                 class="absolute inset-0 w-full h-full object-cover"
                                 onerror="this.style.display='none';">
                            <!-- Overlay gelap untuk kontras teks -->
                            <div class="absolute bg-black/30"></div>
                        <?php else: ?>
                            <!-- Overlay gelap untuk kontras teks -->
                            <div class="absolute inset-0 bg-black/20"></div>
                        <?php endif; ?>
                        
                        <!-- Gradasi putih dari bawah -->
                        <div class="absolute bg-origin-border inset-0 bg-gradient-to-t from-white via-white/30 to-transparent"></div>
                        
                    </div>
                    
                    <!-- Content -->
                    <div class="p-3 sm:p-4 md:p-6">
                        <!-- Teks judul dan penulis -->
                        <div class="relative flex flex-col items-left justify-left text-purple-600 text-left mb-3 md:mb-4">
                            <h1 class="text-sm sm:text-base md:text-lg lg:text-xl font-black leading-tight mb-1 md:mb-2 line-clamp-2">
                                <?= strtoupper(htmlspecialchars($book['title'])) ?>
                            </h1>
                            <p class="text-xs sm:text-sm md:text-base text-gray-600 mb-2 md:mb-3">
                                Oleh <span class="font-bold text-purple-600"><?= htmlspecialchars($book['author']) ?></span>
                            </p>
                            
                            <!-- Genre/Kategori -->
                            <?php if (!empty($book['category'])): ?>
                                <?php
                                $categoryColors = [
                                    'Fiksi' => 'bg-violet-100 text-violet-700',
                                    'Non-Fiksi' => 'bg-blue-100 text-blue-700',
                                    'Sejarah' => 'bg-amber-100 text-amber-700',
                                    'Sains' => 'bg-green-100 text-green-700',
                                    'Teknologi' => 'bg-indigo-100 text-indigo-700',
                                    'Biografi' => 'bg-purple-100 text-purple-700',
                                    'Anak' => 'bg-pink-100 text-pink-700',
                                    'Klasik' => 'bg-orange-100 text-orange-700',
                                    'Self Improvement' => 'bg-emerald-100 text-emerald-700'
                                ];
                                
                                // Split categories by comma if multiple
                                $categories = array_map('trim', explode(',', $book['category']));
                                ?>
                                <div class="flex flex-wrap gap-1 mb-2 md:mb-3">
                                    <?php 
                                    // Limit categories on mobile (show max 2)
                                    $displayCategories = array_slice($categories, 0, 2);
                                    foreach ($displayCategories as $cat): ?>
                                        <?php if (!empty($cat)): ?>
                                            <?php $colorClass = $categoryColors[$cat] ?? 'bg-gray-100 text-gray-700'; ?>
                                            <span class="inline-block px-1.5 py-0.5 md:px-2 md:py-1 <?= $colorClass ?> rounded-full text-xs font-medium">
                                                <?= htmlspecialchars($cat) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if (count($categories) > 2): ?>
                                        <span class="inline-block px-1.5 py-0.5 md:px-2 md:py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                            +<?= count($categories) - 2 ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Sinopsis - Hidden on mobile, visible on larger screens -->
                        <?php if ($book['description']): ?>
                            <p class="hidden md:block text-gray-700 text-sm mb-4 leading-relaxed line-clamp-3">
                                <?= htmlspecialchars($book['description']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <!-- Harga dan stok -->
                        <div class="mb-3 md:mb-4">
                            <div class="text-base sm:text-lg md:text-xl font-bold text-purple-600 mb-1">
                                Rp <?= number_format($book['price'], 0, ',', '.') ?>
                            </div>
                            <div class="text-xs sm:text-sm text-gray-600">
                                Stok: <span class="font-medium"><?= $book['stock'] ?></span>
                            </div>
                        </div>
                        
<!-- Tombol -->
                        <div class="flex space-x-2">
                            <a href="book_detail.php?id=<?= $book['id'] ?>" class="flex-1 bg-purple-600 hover:bg-purple-800 text-white font-medium py-2 md:py-3 rounded-lg text-xs sm:text-sm transition-colors duration-500 text-center">
                                Lihat Detail
                            </a>
                            <?php if ($book['stock'] > 0): ?>
                                <button onclick="addToCart(<?= $book['id'] ?>, '<?= addslashes($book['title']) ?>', '<?= addslashes($book['author']) ?>', <?= $book['price'] ?>, '<?= addslashes($book['cover']) ?>', <?= $book['stock'] ?>)" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 md:py-3 rounded-lg text-xs sm:text-sm transition-colors duration-500 px-3">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($books)): ?>
        <div class="text-center py-12">
            <?php if (!empty($search) || !empty($category_filter)): ?>
                <div class="mb-6">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Tidak Ada Buku Ditemukan</h3>
                <p class="text-gray-600 mb-6">
                    <?php if (!empty($search)): ?>
                        Tidak ada buku yang cocok dengan pencarian "<?= htmlspecialchars($search) ?>"
                        <?= !empty($category_filter) ? ' dalam genre ' . htmlspecialchars($category_filter) : '' ?>
                    <?php elseif (!empty($category_filter)): ?>
                        Tidak ada buku dalam genre "<?= htmlspecialchars($category_filter) ?>"
                    <?php endif; ?>
                </p>
                <div class="space-y-2">
                    <p class="text-gray-500">Coba:</p>
                    <ul class="text-gray-500 space-y-1">
                        <li>• Periksa ejaan kata kunci</li>
                        <li>• Gunakan kata kunci yang lebih umum</li>
                        <li>• Coba genre lain</li>
                    </ul>
                </div>
                <a href="?" class="inline-block mt-6 bg-gradient-to-r from-violet-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:opacity-90">
                    <i class="fas fa-arrow-left mr-2"></i>Lihat Semua Buku
                </a>
            <?php else: ?>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum Ada Buku Tersedia</h3>
                <p class="text-gray-600">Silakan kembali lagi nanti untuk melihat koleksi buku terbaru.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer Modern -->
    <footer class="bg-gradient-to-r from-violet-600 to-indigo-700 rounded-t-2xl shadow-lg p-8 text-white mt-16">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <img src=../assets/images/PadViolett-logo-white.png class= "h-8 w-10">
                        <span class="text-2xl font-bold">PadViolett</span>
                    </div>
                    <p class="text-violet-100">Tempat terbaik untuk menemukan buku favorit Anda dengan pengalaman belanja yang menyenangkan.</p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Kategori</h4>
                    <ul class="space-y-2">
                        <li><a href="?category=Fiksi" class="text-violet-100 hover:text-white transition-colors">Fiksi</a></li>
                        <li><a href="?category=Non-Fiksi" class="text-violet-100 hover:text-white transition-colors">Non-Fiksi</a></li>
                        <li><a href="?category=Sejarah" class="text-violet-100 hover:text-white transition-colors">Sejarah</a></li>
                        <li><a href="?category=Sains" class="text-violet-100 hover:text-white transition-colors">Sains</a></li>
                        <li><a href="?category=Self Improvement" class="text-violet-100 hover:text-white transition-colors">Self Improvement</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Bantuan</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-violet-100 hover:text-white transition-colors">Cara Berbelanja</a></li>
                        <li><a href="#" class="text-violet-100 hover:text-white transition-colors">Pengiriman</a></li>
                        <li><a href="#" class="text-violet-100 hover:text-white transition-colors">Pembayaran</a></li>
                        <li><a href="#" class="text-violet-100 hover:text-white transition-colors">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Newsletter</h4>
                    <p class="text-violet-100 mb-4">Dapatkan promo dan rekomendasi buku terbaru</p>
                    <div class="flex">
                        <input type="email" placeholder="Email Anda" class="flex-1 p-3 rounded-l-lg text-gray-800 focus:outline-none">
                        <button class="bg-white text-violet-600 px-4 rounded-r-lg font-semibold hover:bg-gray-100 transition-colors">Daftar</button>
                    </div>
                </div>
            </div>
            <div class="border-t border-violet-500 mt-8 pt-6 text-center text-violet-200">
                <p>&copy; 2024 PadViolett. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

<script>
        function addToCart(bookId, title, author, price, cover, stock) {
            fetch('cart_ajax.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=add&book_id=' + bookId + '&title=' + encodeURIComponent(title) + '&author=' + encodeURIComponent(author) + '&price=' + price + '&cover=' + encodeURIComponent(cover) + '&stock=' + stock
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartBadge(data.cart_count);
                    alert('Buku "' + title + '" berhasil ditambahkan ke keranjang!');
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        }

        function updateCartBadge(count) {
            let badge = document.querySelector('.cart-badge');
            if (count > 0) {
                if (badge) {
                    badge.textContent = count;
                } else {
                    const cartLink = document.querySelector('a[href="cart.php"]');
                    const newBadge = document.createElement('span');
                    newBadge.className = 'cart-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center';
                    newBadge.textContent = count;
                    cartLink.appendChild(newBadge);
                }
            } else {
                if (badge) badge.remove();
            }
        }

        // Auto-submit form when Enter is pressed in search box
        document.querySelector('input[name="search"]').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.closest('form').submit();
            }
        });

        // Smooth scroll to results after search
        <?php if (!empty($search) || !empty($category_filter)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const resultsSection = document.querySelector('.grid.grid-cols-1.sm\\:grid-cols-2');
            if (resultsSection) {
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
        <?php endif; ?>

        // Highlight search terms in results
        <?php if (!empty($search)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const searchTerm = <?= json_encode($search) ?>;
            const cards = document.querySelectorAll('.max-w-xs');
            
            cards.forEach(card => {
                const title = card.querySelector('h1');
                const author = card.querySelector('p span');
                
                if (title && title.textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                    title.innerHTML = title.innerHTML.replace(
                        new RegExp(searchTerm, 'gi'), 
                        '<mark class="bg-yellow-200 px-1 rounded">$&</mark>'
                    );
                }
                
                if (author && author.textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                    author.innerHTML = author.innerHTML.replace(
                        new RegExp(searchTerm, 'gi'), 
                        '<mark class="bg-yellow-200 px-1 rounded">$&</mark>'
                    );
                }
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>