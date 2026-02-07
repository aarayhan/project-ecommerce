<?php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

// Initialize form variables
$username = '';
$email = '';

// Jika sudah login, redirect ke halaman yang sesuai
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../public/index.php");
    }
    exit();
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field harus diisi!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        try {
            // Cek apakah email sudah terdaftar
            $stmt = $pdo->prepare("SELECT id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Email sudah terdaftar! Silakan gunakan email lain.';
            } else {
                // Cek apakah username sudah ada
                $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ?");
                $stmt->execute([$username]);
                
                if ($stmt->fetch()) {
                    $error = 'Username sudah digunakan! Silakan pilih username lain.';
                } else {
                    // Insert user baru
                    $stmt = $pdo->prepare("INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)");
                    $result = $stmt->execute([
                        $username,
                        $email,
                        password_hash($password, PASSWORD_DEFAULT),
                        'user' // Default role adalah user
                    ]);
                    
                    if ($result) {
                        $success = 'Registrasi berhasil! Silakan login dengan akun Anda.';
                        // Reset form
                        $username = '';
                        $email = '';
                    } else {
                        $error = 'Terjadi kesalahan saat registrasi. Silakan coba lagi.';
                    }
                }
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - PadViolett</title>
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
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.2);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header dengan Logo -->
        <div class="text-center mb-8">
            <div class="flex justify-center items-center space-x-2 mb-4">
                <img src="../assets/images/PadViolett-logo.png" alt="PadViolett Logo" class="w-12 h-10 " onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="w-12 h-12 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-lg flex items-center justify-center hidden">
                    <i class="fas fa-book text-white text-xl"></i>
                </div>
                <span class="text-3xl font-bold bg-gradient-to-r from-violet-600 to-indigo-600 bg-clip-text text-transparent">PadViolett</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Daftar Akun Baru</h1>
            <p class="text-gray-600">Bergabunglah dengan komunitas pembaca PadViolett</p>
        </div>

        <!-- Form Register -->
        <div class="glass-effect rounded-2xl shadow-xl p-8 hover-lift">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-user mr-2 text-violet-600"></i>Username
                    </label>
                    <input type="text" id="username" name="username" required
                           value="<?= isset($username) ? htmlspecialchars($username) : '' ?>"
                           class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                           placeholder="Masukkan username Anda">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-envelope mr-2 text-violet-600"></i>Email
                    </label>
                    <input type="email" id="email" name="email" required
                           value="<?= isset($email) ? htmlspecialchars($email) : '' ?>"
                           class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                           placeholder="contoh@email.com">
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-lock mr-2 text-violet-600"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                               class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all pr-12"
                               placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="password-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-gray-700 font-medium mb-2">
                        <i class="fas fa-lock mr-2 text-violet-600"></i>Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" required
                               class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all pr-12"
                               placeholder="Ulangi password Anda">
                        <button type="button" onclick="togglePassword('confirm_password')" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="confirm_password-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold py-4 rounded-xl hover:opacity-90 transition-all transform hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                </button>
            </form>

            <!-- Link ke Login -->
            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-600">
                    Sudah punya akun? 
                    <a href="login.php" class="text-violet-600 hover:text-violet-800 font-semibold transition-colors">
                        Masuk di sini
                    </a>
                </p>
            </div>

            <!-- Link ke Home -->
            <div class="text-center mt-4">
                <a href="../public/" class="text-gray-500 hover:text-gray-700 text-sm transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const eye = document.getElementById(fieldId + '-eye');
            
            if (field.type === 'password') {
                field.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // Auto-hide success message after 5 seconds
        <?php if ($success): ?>
        setTimeout(function() {
            const successDiv = document.querySelector('.bg-green-100');
            if (successDiv) {
                successDiv.style.transition = 'opacity 0.5s ease';
                successDiv.style.opacity = '0';
                setTimeout(() => successDiv.remove(), 500);
            }
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>