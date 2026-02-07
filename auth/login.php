<?php
session_start();
require_once '../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../public/index.php");
    }
    exit();
}

$error = '';
$success = '';

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success = 'Anda telah berhasil logout.';
}

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../public/index.php");
        }
        exit();
    } else {
        $error = 'Email atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PadViolett</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-600 to-blue-500 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <img src="../assets/images/PadViolett-logo.png" alt="PadViolett Logo" class="h-15 w-16" onerror="this.style.display='none'">
            </div>
            <h1 class="text-3xl font-bold text-gray-800">PadViolett</h1>
            <p class="text-gray-600 mt-2">Masuk ke akun Anda</p>
        </div>
        
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
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500">
            </div>
            
            <button type="submit" 
                    class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition duration-200">
                Masuk
            </button>
        </form>
        
        <!-- Link Register -->
        <div class="text-center mt-6 pt-4 border-t border-gray-200">
            <p class="text-gray-600">
                Belum punya akun? 
                <a href="register.php" class="text-purple-600 hover:text-purple-800 font-semibold transition-colors">
                    Daftar di sini
                </a>
            </p>
        </div>
        
        <!-- Link ke Home -->
        <div class="text-center mt-4">
            <a href="../public/" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>