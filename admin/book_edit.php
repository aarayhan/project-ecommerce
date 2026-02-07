<?php
session_start();
require_once '../config/database.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$success = '';
$error = '';

// Get book data
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    header("Location: dashboard.php");
    exit();
}

if ($_POST) {
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
        $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, price = ?, description = ?, cover = ?, stock = ?, category = ? WHERE id = ?");
        $stmt->execute([$title, $author, $price, $description, $cover, $stock, $category, $id]);
        $success = 'Buku berhasil diupdate!';
        
        // Refresh book data
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$id]);
        $book = $stmt->fetch();
    } catch (Exception $e) {
        $error = 'Gagal mengupdate buku: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - PadViolett Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-purple-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Edit Buku</h1>
            <a href="dashboard.php" class="bg-purple-700 px-3 py-1 rounded hover:bg-purple-800">Kembali</a>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Judul Buku</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Penulis</label>
                    <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                    <input type="number" name="price" value="<?= $book['price'] ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                    <input type="number" name="stock" value="<?= $book['stock'] ?>" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Cover Buku (URL atau Base64)</label>
                    <textarea name="cover" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"
                              placeholder="https://example.com/book-cover.jpg atau data:image/jpeg;base64,/9j/4AAQ..."><?= htmlspecialchars($book['cover']) ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">Masukkan URL gambar atau Base64 data (opsional)</p>
                    <?php if ($book['cover']): ?>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Preview:</p>
                            <img src="<?= htmlspecialchars($book['cover']) ?>" alt="Cover Preview" 
                                 class="w-20 h-28 object-cover rounded border mt-1" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <p class="text-red-500 text-sm hidden">Gambar tidak dapat dimuat</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori (Pilih satu atau lebih)</label>
                    <?php 
                    // Get current categories as array
                    $currentCategories = !empty($book['category']) ? array_map('trim', explode(',', $book['category'])) : [];
                    ?>
                    <div class="grid grid-cols-2 gap-2 p-3 border border-gray-300 rounded-lg max-h-40 overflow-y-auto">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Fiksi" class="rounded text-purple-600" <?= in_array('Fiksi', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Fiksi</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Non-Fiksi" class="rounded text-purple-600" <?= in_array('Non-Fiksi', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Non-Fiksi</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Sejarah" class="rounded text-purple-600" <?= in_array('Sejarah', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Sejarah</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Sains" class="rounded text-purple-600" <?= in_array('Sains', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Sains</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Teknologi" class="rounded text-purple-600" <?= in_array('Teknologi', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Teknologi</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Biografi" class="rounded text-purple-600" <?= in_array('Biografi', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Biografi</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Anak" class="rounded text-purple-600" <?= in_array('Anak', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Anak</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Klasik" class="rounded text-purple-600" <?= in_array('Klasik', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Klasik</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="categories[]" value="Self Improvement" class="rounded text-purple-600" <?= in_array('Self Improvement', $currentCategories) ? 'checked' : '' ?>>
                            <span class="text-sm">Self Improvement</span>
                        </label>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Pilih satu atau lebih kategori yang sesuai dengan buku</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Sinopsis</label>
                    <textarea name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500"><?= htmlspecialchars($book['description']) ?></textarea>
                </div>

                <button type="submit" 
                        class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition duration-200">
                    Update Buku
                </button>
            </form>
        </div>
    </div>
</body>
</html>