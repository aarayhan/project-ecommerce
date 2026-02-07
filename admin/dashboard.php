<?php
session_start();
require_once '../config/database.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get all books
$stmt = $pdo->query("SELECT * FROM books ORDER BY id DESC");
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PadViolett</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary: #7c3aed;
            --primary-light: #8b5cf6;
            --primary-dark: #6d28d9;
            --secondary: #f5f3ff;
        }
        
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
        
        .sidebar-item {
            transition: all 0.3s ease;
        }
        
        .sidebar-item:hover {
            background-color: rgba(124, 58, 237, 0.1);
            border-left: 4px solid var(--primary);
        }
        
        .active-sidebar {
            background-color: rgba(124, 58, 237, 0.1);
            border-left: 4px solid var(--primary);
            color: var(--primary);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">
    <!-- Navigation untuk Admin -->
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
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full"></div>
                        <div>
                            <p class="font-semibold text-gray-800"><?= $_SESSION['username'] ?></p>
                            <p class="text-sm text-gray-500">Admin PadViolett</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex pt-16">
        <!-- Sidebar Admin -->
        <aside class="w-64 bg-white min-h-screen shadow-lg fixed">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Admin Dashboard</h2>
                <nav class="space-y-2">
                    <a href="#" class="sidebar-item active-sidebar flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-chart-bar text-violet-600"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="../auth/logout.php" class="sidebar-item flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-sign-out-alt text-gray-600"></i>
                        <span class="font-medium">Logout</span>
                    </a>
                    <a href="../public/index.php" class="sidebar-item flex items-center space-x-3 p-3 rounded-lg">
                        <i class="fas fa-chart-bar text-gray-600"></i>
                        <span class="font-medium">User View</span>
                    </a>
                </nav>
                
                <div class="mt-10 p-4 bg-gradient-to-r from-violet-50 to-indigo-50 rounded-xl">
                    <h3 class="font-bold text-gray-800 mb-2">Statistik Hari Ini</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Total Buku</p>
                            <p class="text-xl font-bold text-violet-600"><?= count($books) ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Stok Tersedia</p>
                            <p class="text-xl font-bold text-violet-600"><?= array_sum(array_column($books, 'stock')) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Dashboard Content -->
        <main class="ml-64 flex-1 p-8">
            <!-- Header Dashboard -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
                    <p class="text-gray-600">Kelola buku dan penjualan di toko buku PadViolett</p>
                </div>
                <!-- Tombol Tambah Buku -->
                <button id="tambahBukuBtn" class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl hover-lift flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Buku Baru</span>
                </button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-lg hover-lift">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500">Total Buku</p>
                            <p class="text-3xl font-bold text-gray-800"><?= count($books) ?></p>
                        </div>
                        <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-book text-violet-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-green-500 mt-2">
                        <i class="fas fa-arrow-up"></i> Buku tersedia
                    </p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-lg hover-lift">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500">Total Stok</p>
                            <p class="text-3xl font-bold text-gray-800"><?= array_sum(array_column($books, 'stock')) ?></p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-boxes text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-green-500 mt-2">
                        <i class="fas fa-arrow-up"></i> Stok tersedia
                    </p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-lg hover-light">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500">Nilai Inventori</p>
                            <p class="text-3xl font-bold text-gray-800">Rp <?= number_format(array_sum(array_map(function($book) { return $book['price'] * $book['stock']; }, $books)), 0, ',', '.') ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-wallet text-purple-600 text-xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-green-500 mt-2">
                        <i class="fas fa-arrow-up"></i> Total nilai stok
                    </p>
                </div>
            </div>

            <!-- Tabel Daftar Buku -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-bold text-gray-800">Daftar Buku</h2>
                    <p class="text-gray-600">Kelola buku yang tersedia di toko</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-4 px-6 text-left text-gray-600 font-semibold">Judul Buku</th>
                                <th class="py-4 px-6 text-left text-gray-600 font-semibold">Penulis</th>
                                <th class="py-4 px-6 text-left text-gray-600 font-semibold">Kategori</th>
                                <th class="py-4 px-6 text-left text-gray-600 font-semibold">Harga</th>
                                <th class="py-4 px-6 text-left text-gray-600 font-semibold">Stok</th>
                                <th class="py-4 px-6 text-left text-gray-600 font-semibold">Status</th>
                                <th class="py-4 px-6 text-left text-gray-600 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-4">
                                        <?php if ($book['cover'] && (filter_var($book['cover'], FILTER_VALIDATE_URL) || strpos($book['cover'], 'data:image/') === 0)): ?>
                                            <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Cover" class="w-12 h-16 object-cover rounded" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                            <div class="w-12 h-16 bg-gradient-to-r from-violet-400 to-indigo-500 rounded hidden"></div>
                                        <?php else: ?>
                                            <div class="w-12 h-16 bg-gradient-to-r from-violet-400 to-indigo-500 rounded"></div>
                                        <?php endif; ?>
                                        <div>
                                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($book['title']) ?></p>
                                            <p class="text-sm text-gray-500">ID: <?= $book['id'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-gray-800"><?= htmlspecialchars($book['author']) ?></p>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if (!empty($book['category'])): ?>
                                        <?php
                                        $categoryColors = [
                                            'Fiksi' => 'bg-violet-100 text-violet-800',
                                            'Non-Fiksi' => 'bg-blue-100 text-blue-800',
                                            'Sejarah' => 'bg-amber-100 text-amber-800',
                                            'Sains' => 'bg-green-100 text-green-800',
                                            'Teknologi' => 'bg-indigo-100 text-indigo-800',
                                            'Biografi' => 'bg-purple-100 text-purple-800',
                                            'Anak' => 'bg-pink-100 text-pink-800',
                                            'Klasik' => 'bg-orange-100 text-orange-800',
                                            'Self Improvement' => 'bg-emerald-100 text-emerald-800'
                                        ];
                                        
                                        // Split categories by comma if multiple
                                        $categories = array_map('trim', explode(',', $book['category']));
                                        ?>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($categories as $cat): ?>
                                                <?php if (!empty($cat)): ?>
                                                    <?php $colorClass = $categoryColors[$cat] ?? 'bg-gray-100 text-gray-800'; ?>
                                                    <span class="px-2 py-1 <?= $colorClass ?> rounded-full text-xs">
                                                        <?= htmlspecialchars($cat) ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-sm">Tanpa Kategori</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="font-bold text-gray-800">Rp <?= number_format($book['price'], 0, ',', '.') ?></p>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-gray-800"><?= $book['stock'] ?></p>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if ($book['stock'] > 0): ?>
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Tersedia</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Habis</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex space-x-2">
                                        <a href="book_edit.php?id=<?= $book['id'] ?>" class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="book_delete.php?id=<?= $book['id'] ?>" onclick="return confirm('Yakin ingin menghapus buku ini?')" class="w-10 h-10 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-6 border-t flex justify-between items-center">
                    <p class="text-gray-600">Menampilkan <?= count($books) ?> buku</p>
                    <div class="flex space-x-2">
                        <button class="w-10 h-10 bg-gray-100 text-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="w-10 h-10 bg-violet-600 text-white rounded-lg flex items-center justify-center">1</button>
                        <button class="w-10 h-10 bg-gray-100 text-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors">2</button>
                        <button class="w-10 h-10 bg-gray-100 text-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors">3</button>
                        <button class="w-10 h-10 bg-gray-100 text-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Tambah Buku -->
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Modal Header -->
        <div class="p-6 border-b flex justify-between items-center shrink-0">
            <h3 class="text-xl font-bold text-gray-800">Tambah Buku Baru</h3>
            <button id="closeModal" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Modal Body - Scrollable -->
        <div class="flex-1 overflow-y-auto p-6">
            <form action="book_add.php" method="POST" class="space-y-6">
                <!-- Row 1: Title & Author -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Judul Buku</label>
                        <input type="text" name="title" required 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent" 
                               placeholder="Masukkan judul buku">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Penulis</label>
                        <input type="text" name="author" required 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent" 
                               placeholder="Nama penulis">
                    </div>
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Sinopsis</label>
                    <textarea name="description" rows="3" 
                              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent resize-none" 
                              placeholder="Sinopsis singkat buku"></textarea>
                </div>
                
                <!-- Row 2: Price & Stock -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Harga (Rp)</label>
                        <input type="number" name="price" required min="0"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent" 
                               placeholder="0">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Stok</label>
                        <input type="number" name="stock" required min="0"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent" 
                               placeholder="0">
                    </div>
                </div>
                
                <!-- Categories -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Kategori</label>
                    <div class="grid grid-cols-2 gap-3 p-4 border border-gray-300 rounded-lg max-h-48 overflow-y-auto bg-gray-50">
                        <?php 
                        $categories = [
                            'Fiksi', 'Non-Fiksi', 'Sejarah', 'Sains', 
                            'Teknologi', 'Biografi', 'Anak', 'Klasik', 
                            'Self Improvement', 'Romance', 'Fantasi', 'Misteri',
                            'Bisnis', 'Kesehatan', 'Seni', 'Filsafat', 'Self Improvement'
                        ];
                        
                        foreach ($categories as $category): 
                        ?>
                            <label class="flex items-center space-x-2 p-2 hover:bg-violet-50 rounded cursor-pointer">
                                <input type="checkbox" name="categories[]" value="<?= htmlspecialchars($category) ?>" 
                                       class="w-5 h-5 text-violet-600 rounded border-gray-300 focus:ring-violet-500">
                                <span class="text-sm text-gray-700"><?= htmlspecialchars($category) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Pilih satu atau lebih kategori yang sesuai</p>
                </div>
                
                <!-- Cover URL -->
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Cover Buku</label>
                    <textarea name="cover" rows="3" 
                              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent resize-none" 
                              placeholder="https://example.com/book-cover.jpg (opsional)"></textarea>
                    <p class="text-sm text-gray-500 mt-2">URL gambar atau Base64 data</p>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="p-6 border-t flex justify-end space-x-4 shrink-0">
            <button id="cancelModal" type="button" 
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Batal
            </button>
            <button type="button" onclick="document.querySelector('form').submit()" 
                    class="px-6 py-3 bg-gradient-to-r from-violet-600 to-indigo-600 text-white rounded-lg hover:opacity-90 transition">
                Simpan Buku
            </button>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk modal tambah buku
    const tambahBukuBtn = document.getElementById('tambahBukuBtn');
    const modalTambah = document.getElementById('modalTambah');
    const closeModal = document.getElementById('closeModal');
    const cancelModal = document.getElementById('cancelModal');

    if (tambahBukuBtn) {
        tambahBukuBtn.addEventListener('click', () => {
            modalTambah.classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Mencegah scroll background
        });
    }

    function closeModalFunc() {
        modalTambah.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Mengembalikan scroll background
    }

    if (closeModal) {
        closeModal.addEventListener('click', closeModalFunc);
    }

    if (cancelModal) {
        cancelModal.addEventListener('click', closeModalFunc);
    }

    // Tutup modal jika klik di luar
    window.addEventListener('click', (e) => {
        if (e.target === modalTambah) {
            closeModalFunc();
        }
    });

    // Tutup modal dengan Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modalTambah.classList.contains('hidden')) {
            closeModalFunc();
        }
    });
</script>
<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <script>
        alert('Buku berhasil ditambahkan!');
    </script>
<?php endif;?>

</body>
</html>