-- =============================================
-- PadViolett Database for InfinityFree
-- Import this file via phpMyAdmin
-- =============================================

-- =============================================
-- Table: user
-- =============================================

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- =============================================
-- Table: books
-- =============================================

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `cover` text,
  `stock` int(11) NOT NULL DEFAULT '0',
  `category` varchar(500) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- =============================================
-- Insert Users
-- =============================================

INSERT INTO `user` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'admin', 'admin@padviolett.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'user', 'user@padviolett.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
(3, 'demo', 'demo@padviolett.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- =============================================
-- Insert Sample Books
-- =============================================

INSERT INTO `books` (`id`, `title`, `author`, `price`, `description`, `cover`, `stock`, `category`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 85000.00, 'Novel yang menceritakan tentang perjuangan anak-anak Belitung untuk mendapatkan pendidikan. Kisah yang penuh inspirasi tentang persahabatan, mimpi, dan harapan di tengah keterbatasan.', 'https://images-na.ssl-images-amazon.com/images/I/81+oKNFoEyL.jpg', 15, 'Fiksi, Klasik'),

(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', 95000.00, 'Novel pertama dari Tetralogi Buru yang mengisahkan perjalanan Minke, seorang pribumi yang berjuang melawan kolonialisme Belanda. Karya sastra Indonesia yang monumental.', 'https://images-na.ssl-images-amazon.com/images/I/71QcZQqJOyL.jpg', 12, 'Fiksi, Sejarah, Klasik'),

(3, 'Atomic Habits', 'James Clear', 120000.00, 'Panduan praktis untuk membangun kebiasaan baik dan menghilangkan kebiasaan buruk. Buku self-improvement yang telah mengubah hidup jutaan orang di seluruh dunia.', 'https://images-na.ssl-images-amazon.com/images/I/81wgcld4wxL.jpg', 25, 'Self Improvement, Non-Fiksi'),

(4, 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', 135000.00, 'Sebuah eksplorasi mendalam tentang sejarah umat manusia, dari zaman batu hingga era modern. Buku yang mengubah cara pandang kita tentang peradaban manusia.', 'https://images-na.ssl-images-amazon.com/images/I/713jIoMO3UL.jpg', 18, 'Sejarah, Sains, Non-Fiksi'),

(5, 'The Psychology of Money', 'Morgan Housel', 110000.00, 'Buku yang mengungkap aspek psikologis dalam pengelolaan keuangan. Pelajaran berharga tentang bagaimana emosi dan perilaku mempengaruhi keputusan finansial kita.', 'https://images-na.ssl-images-amazon.com/images/I/81cpDaCJJCL.jpg', 20, 'Self Improvement, Non-Fiksi'),

(6, 'Dilan 1990', 'Pidi Baiq', 75000.00, 'Novel romantis yang mengisahkan cinta remaja antara Dilan dan Milea di Bandung tahun 1990. Kisah cinta yang sederhana namun menyentuh hati.', 'https://images-na.ssl-images-amazon.com/images/I/71fxiUsqp7L.jpg', 30, 'Fiksi'),

(7, 'Steve Jobs', 'Walter Isaacson', 145000.00, 'Biografi resmi pendiri Apple Inc. yang ditulis berdasarkan wawancara eksklusif. Kisah inspiratif tentang inovasi, kepemimpinan, dan visi yang mengubah dunia teknologi.', 'https://images-na.ssl-images-amazon.com/images/I/81VStYnDGrL.jpg', 10, 'Biografi, Teknologi'),

(8, 'Sejarah Dunia yang Disembunyikan', 'Jonathan Black', 98000.00, 'Eksplorasi mendalam tentang sejarah rahasia dunia yang tidak diajarkan di sekolah. Mengungkap misteri dan konspirasi yang membentuk peradaban manusia.', 'https://images-na.ssl-images-amazon.com/images/I/81Q8GvKl+8L.jpg', 8, 'Sejarah, Non-Fiksi'),

(9, 'Filosofi Teras', 'Henry Manampiring', 89000.00, 'Pengantar filosofi Stoikisme untuk kehidupan sehari-hari. Buku yang mengajarkan cara menghadapi masalah hidup dengan tenang dan bijaksana.', 'https://images-na.ssl-images-amazon.com/images/I/71rJufvXTgL.jpg', 22, 'Self Improvement, Non-Fiksi'),

(10, 'Clean Code', 'Robert C. Martin', 165000.00, 'Panduan untuk menulis kode yang bersih, mudah dibaca, dan mudah dipelihara. Buku wajib untuk setiap programmer yang ingin meningkatkan kualitas kodenya.', 'https://images-na.ssl-images-amazon.com/images/I/41xShlnTZTL.jpg', 14, 'Teknologi, Non-Fiksi'),

(11, 'Harry Potter dan Batu Bertuah', 'J.K. Rowling', 125000.00, 'Novel fantasi pertama dari seri Harry Potter. Mengisahkan petualangan seorang anak yatim piatu yang menemukan bahwa dia adalah seorang penyihir.', 'https://images-na.ssl-images-amazon.com/images/I/81YOuOGFCJL.jpg', 35, 'Fiksi, Anak'),

(12, 'Thinking, Fast and Slow', 'Daniel Kahneman', 140000.00, 'Eksplorasi mendalam tentang cara kerja pikiran manusia. Buku yang mengungkap dua sistem berpikir yang mempengarugi setiap keputusan yang kita buat.', 'https://images-na.ssl-images-amazon.com/images/I/71T4HXnVhvL.jpg', 16, 'Sains, Self Improvement'),

(13, 'Negeri 5 Menara', 'Ahmad Fuadi', 82000.00, 'Novel yang mengisahkan perjuangan enam santri di Pondok Modern Gontor. Kisah inspiratif tentang persahabatan, pendidikan, dan perjuangan meraih mimpi.', 'https://images-na.ssl-images-amazon.com/images/I/81fPKd+2BHL.jpg', 28, 'Fiksi, Klasik'),

(14, 'The Lean Startup', 'Eric Ries', 128000.00, 'Metodologi untuk membangun startup yang sukses dengan pendekatan yang efisien dan terukur. Panduan praktis untuk entrepreneur modern.', 'https://images-na.ssl-images-amazon.com/images/I/81-QB7nDh4L.jpg', 11, 'Teknologi, Self Improvement'),

(15, 'Ronggeng Dukuh Paruk', 'Ahmad Tohari', 78000.00, 'Novel klasik Indonesia yang mengisahkan kehidupan seorang ronggeng di desa Dukuh Paruk. Karya sastra yang kaya akan nilai budaya dan tradisi Jawa.', 'https://images-na.ssl-images-amazon.com/images/I/71XQZ8vKzgL.jpg', 19, 'Fiksi, Klasik, Sejarah');

-- =============================================
-- Set AUTO_INCREMENT
-- =============================================

ALTER TABLE `user` AUTO_INCREMENT = 4;
ALTER TABLE `books` AUTO_INCREMENT = 16;

-- =============================================
-- SETUP COMPLETE!
-- 
-- Login Credentials:
-- Admin: admin@padviolett.com / password
-- User: user@padviolett.com / password  
-- Demo: demo@padviolett.com / password
-- =============================================