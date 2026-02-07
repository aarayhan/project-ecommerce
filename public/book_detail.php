<?php
session_start();
require_once '../config/database.php';

// Get book ID from URL
$book_id = $_GET['id'] ?? 0;

// Get book details
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch();

// Redirect if book not found
if (!$book) {
    header("Location: index.php");
    exit();
}

// Get related books (same categories, excluding current book)
$related_books = [];
if (!empty($book['category'])) {
    $categories = array_map('trim', explode(',', $book['category']));
    $category_conditions = [];
    $params = [];
    
    foreach ($categories as $cat) {
        $category_conditions[] = "category LIKE ?";
        $params[] = "%$cat%";
    }
    
    if (!empty($category_conditions)) {
        $related_query = "SELECT * FROM books WHERE id != ? AND stock > 0 AND (" . implode(' OR ', $category_conditions) . ") LIMIT 4";
        $stmt = $pdo->prepare($related_query);
        $stmt->execute(array_merge([$book_id], $params));
        $related_books = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?> - PadViolett</title>
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
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.2);
        }
        
        .book-cover-shadow {
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
        <!-- Book Detail Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Book Cover -->
                <div class="flex justify-center lg:justify-start">
                    <div class="relative">
                        <?php if ($book['cover'] && (filter_var($book['cover'], FILTER_VALIDATE_URL) || strpos($book['cover'], 'data:image/') === 0)): ?>
                            <img src="<?= htmlspecialchars($book['cover']) ?>" 
                                 alt="<?= htmlspecialchars($book['title']) ?>" 
                                 class="w-80 h-96 object-cover rounded-xl book-cover-shadow"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-80 h-96 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl book-cover-shadow hidden flex items-center justify-center">
                                <div class="text-center text-white p-8">
                                    <i class="fas fa-book text-6xl mb-4"></i>
                                    <h3 class="text-2xl font-bold"><?= htmlspecialchars($book['title']) ?></h3>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="w-80 h-96 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl book-cover-shadow flex items-center justify-center">
                                <div class="text-center text-white p-8">
                                    <i class="fas fa-book text-6xl mb-4"></i>
                                    <h3 class="text-2xl font-bold"><?= htmlspecialchars($book['title']) ?></h3>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Book Information -->
                <div class="space-y-6">
                    <!-- Title and Author -->
                    <div>
                        <h1 class="text-4xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($book['title']) ?></h1>
                        <p class="text-xl text-gray-600">Oleh <span class="font-semibold text-purple-600"><?= htmlspecialchars($book['author']) ?></span></p>
                    </div>

                    <!-- Categories -->
                    <?php if (!empty($book['category'])): ?>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Genre</h3>
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
                            
                            $categories = array_map('trim', explode(',', $book['category']));
                            ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($categories as $cat): ?>
                                    <?php if (!empty($cat)): ?>
                                        <?php $colorClass = $categoryColors[$cat] ?? 'bg-gray-100 text-gray-700'; ?>
                                        <span class="px-3 py-1 <?= $colorClass ?> rounded-full text-sm font-medium">
                                            <?= htmlspecialchars($cat) ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Price and Stock -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-3xl font-bold text-purple-600">Rp <?= number_format($book['price'], 0, ',', '.') ?></p>
                                <p class="text-gray-600">Harga per buku</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold text-gray-800">Stok: <?= $book['stock'] ?></p>
                                <?php if ($book['stock'] > 0): ?>
                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Tersedia</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Habis</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex space-x-4">
                            <?php if ($book['stock'] > 0): ?>
                                <button class="flex-1 bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl hover:opacity-90 transition-all">
                                    <i class="fas fa-shopping-cart mr-2"></i>Beli Sekarang
                                </button>
                                <button class="flex-1 bg-white border-2 border-violet-600 text-violet-600 font-semibold py-3 px-6 rounded-xl hover:bg-violet-50 transition-all">
                                    <i class="fas fa-heart mr-2"></i>Simpan ke Wishlist
                                </button>
                            <?php else: ?>
                                <button class="flex-1 bg-gray-400 text-white font-semibold py-3 px-6 rounded-xl cursor-not-allowed" disabled>
                                    <i class="fas fa-times mr-2"></i>Stok Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Book Details -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600">ID Buku</p>
                            <p class="font-semibold">#<?= str_pad($book['id'], 4, '0', STR_PAD_LEFT) ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-600">Status</p>
                            <p class="font-semibold"><?= $book['stock'] > 0 ? 'Tersedia' : 'Habis' ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Synopsis Section -->
        <?php if (!empty($book['description'])): ?>
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Sinopsis</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 leading-relaxed text-lg"><?= nl2br(htmlspecialchars($book['description'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Related Books Section -->
        <?php if (!empty($related_books)): ?>
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Buku Serupa</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($related_books as $related_book): ?>
                <div class="bg-gray-50 rounded-xl p-4 hover-lift">
                    <div class="relative h-48 mb-4">
                        <?php if ($related_book['cover'] && (filter_var($related_book['cover'], FILTER_VALIDATE_URL) || strpos($related_book['cover'], 'data:image/') === 0)): ?>
                            <img src="<?= htmlspecialchars($related_book['cover']) ?>" 
                                 alt="<?= htmlspecialchars($related_book['title']) ?>" 
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
                    <h3 class="font-bold text-gray-800 mb-1 line-clamp-2"><?= htmlspecialchars($related_book['title']) ?></h3>
                    <p class="text-gray-600 text-sm mb-2">Oleh <?= htmlspecialchars($related_book['author']) ?></p>
                    <p class="text-purple-600 font-bold mb-3">Rp <?= number_format($related_book['price'], 0, ',', '.') ?></p>
                    <a href="book_detail.php?id=<?= $related_book['id'] ?>" class="block w-full bg-violet-600 text-white text-center py-2 rounded-lg hover:bg-violet-700 transition-colors text-sm">
                        Lihat Detail
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-violet-600 to-indigo-700 rounded-t-2xl shadow-lg p-8 text-white mt-16">
        <div class="container mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <i class="fas fa-book text-violet-600 text-xl"></i>
                        </div>
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
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll for related books
            const relatedBooks = document.querySelectorAll('.hover-lift');
            relatedBooks.forEach(book => {
                book.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                book.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>