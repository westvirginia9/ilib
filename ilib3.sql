-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2024 at 03:25 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ilib3`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `genre` enum('fiksi','nonfiksi') NOT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reader_count` int(11) DEFAULT 0,
  `preview_file` varchar(255) NOT NULL,
  `full_file` varchar(255) NOT NULL,
  `rental_period` varchar(255) NOT NULL DEFAULT '7 Hari'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `cover_image`, `genre`, `rental_price`, `file_path`, `author_id`, `created_at`, `reader_count`, `preview_file`, `full_file`, `rental_period`) VALUES
(1, '1984', '../uploads/gambar2.jpeg', 'fiksi', 10000.00, '../uploads/teslah.pdf', 2, '2024-07-02 12:46:50', 1, '', '', '7 Hari'),
(2, 'Betty', '../uploads/gambar6.jpeg', 'nonfiksi', 12000.00, '../uploads/teslah.pdf', 3, '2024-07-02 13:38:17', 0, '', '', '7 Hari'),
(3, 'Shine', '../uploads/gambar7.jpeg', 'fiksi', 11000.00, '../uploads/teslah.pdf', 3, '2024-07-02 13:38:37', 0, '', '', '7 Hari'),
(4, 'Dark Clouds Over Salty Shores', '../uploads/covers/66841647d24b2_1718670300.jpg', 'nonfiksi', 10000.00, '', 2, '2024-07-02 15:01:27', 2, '66841647d261b_preview.pdf', '66841647d2748_Dark-Clouds-Over-Salty-Shores.pdf', '7 Hari'),
(6, 'Try Not To Laugh', '../uploads/covers/66841a9e51d89_ketawa.jpg', 'nonfiksi', 10000.00, '', 2, '2024-07-02 15:19:58', 1, '../uploads/preview/66841a9e51ec8_preview2.pdf', '../uploads/full/66841a9e51fdc_Try-Not-To-Laugh-1000-Hilarious-Jokes-For-All-Ages.pdf', '7 Hari');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_message` text NOT NULL,
  `bot_response` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `user_id`, `user_message`, `bot_response`, `created_at`) VALUES
(1, 2, 'halo', 'Halo! 👋  Saya siap membantu Anda. Bagaimana saya bisa membantu Anda hari ini? 🤔', '2024-07-03 07:57:13'),
(2, 2, 'jenis buku apa yang harus saya buat selanjutnya?', 'Informasi yang Anda berikan tidak termasuk analisis tentang jenis buku yang paling banyak dibaca, sehingga saya tidak dapat menjawab pertanyaan tersebut dari data yang tersedia.', '2024-07-03 07:57:32'),
(3, 2, 'buat analisis buku', 'Untuk membuat analisis buku, Anda perlu menunjukkan:\n\n**1. Pendahuluan:**\n\n* Pengenalan singkat buku dan pengarangnya\n* Ringasan singkat plot/kesajagan\n* Tujuan Anda dalam menganalisis buku\n\n**2. Analisis Isi:**\n\n- **Tema:** Identifikasi tema utama yang terkandung di buku - apa pesan yang ingin penuliskan?\n- **Ideologi:** Apakah buku tersebut mendukung ideologi tertentu?\n- **Simbolisme:** Apa saja simbol yang digunakan dan apa maknanya?\n- **Motivasi:** Apa motivasi dan tujuan para tokoh?\n- **Konflik:** Bagaimana konflik dalam buku dapat dipahami?\n- **Resolusi:** Bagaimana masalah dalam buku diselesaikan?\n\n**3. Analisis Gaya:**\n\n- **Struktur:** Bagaimana struktur cerita/kesajagan buku?\n- **Genre:** Apakah buku tersebut termasuk dalam genre tertentu?\n- **Bentuk Bahasa:** Apakah bahasa yang digunakan sederhana, kompleks, atau formal?\n- **Nstated:** Apakah penulis menggunakan nstated untuk menyatakan ide mereka?\n\n**4. Kesimpulan:**\n\n* Ringasan singkat analisis Anda\n* Interpretasi Anda tentang buku\n* Implikasi dan penemuan yang Anda buat dari buku\n\n**Tips:**\n\n* Gunakan sumber data yang terpercaya untuk membantu Anda dalam analisis Anda.\n* Pastikan Anda memberikan contoh dan referensi yang tepat.\n* Pastikan Anda membuat analisis yang objektif dan tidak bias.', '2024-07-03 07:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_conversations`
--

CREATE TABLE `chatbot_conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `response` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contests`
--

CREATE TABLE `contests` (
  `id` int(11) NOT NULL,
  `contest_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `contest_image` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contest_participants`
--

CREATE TABLE `contest_participants` (
  `id` int(11) NOT NULL,
  `contest_id` int(11) NOT NULL,
  `reader_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `book_id`, `amount`, `payment_date`) VALUES
(1, 4, 4, 10000.00, '2024-07-02 22:49:22'),
(2, 4, 4, 10000.00, '2024-07-02 22:50:56');

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `reader_id` int(11) NOT NULL,
  `rental_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('aktif','tidak aktif') NOT NULL DEFAULT 'aktif',
  `payment_status` varchar(20) DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rentals`
--

INSERT INTO `rentals` (`id`, `book_id`, `reader_id`, `rental_date`, `status`, `payment_status`, `amount`, `payment_date`) VALUES
(1, 4, 5, '2024-07-02 16:41:05', 'aktif', 'paid', 10000.00, '2024-07-02 23:41:05'),
(2, 4, 4, '2024-07-02 16:46:01', 'aktif', 'paid', 10000.00, '2024-07-02 23:46:01'),
(3, 4, 6, '2024-07-02 17:08:53', 'aktif', 'paid', 10000.00, '2024-07-03 00:08:53'),
(4, 6, 5, '2024-07-03 05:52:03', 'aktif', 'paid', 10000.00, '2024-07-03 12:52:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','penulis','pembaca') NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone_number`, `created_at`) VALUES
(1, 'Admin Yogi', 'admin1@ilib.com', '123', 'admin', '081234567890', '2024-07-02 12:22:11'),
(2, 'Ainun Hafidz', 'hafidz@gmail.com', '123', 'penulis', '081234567891', '2024-07-02 12:22:11'),
(3, 'Sambo', 'sambo@gmail.com', '123', 'penulis', '081234567892', '2024-07-02 12:22:11'),
(4, 'Yusa', 'yusa@gmail.com', '123', 'pembaca', '081234567893', '2024-07-02 12:22:11'),
(5, 'Tyo', 'tyo@gmail.com', '123', 'pembaca', '081234567894', '2024-07-02 12:22:11'),
(6, 'Aldo', 'aldo@gmail.com', '123', 'pembaca', '081231234', '2024-07-02 17:08:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chatbot_conversations`
--
ALTER TABLE `chatbot_conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contests`
--
ALTER TABLE `contests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `contest_participants`
--
ALTER TABLE `contest_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contest_id` (`contest_id`),
  ADD KEY `reader_id` (`reader_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rentals`
--
ALTER TABLE `rentals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `reader_id` (`reader_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `chatbot_conversations`
--
ALTER TABLE `chatbot_conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contests`
--
ALTER TABLE `contests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contest_participants`
--
ALTER TABLE `contest_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rentals`
--
ALTER TABLE `rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `chatbot_conversations`
--
ALTER TABLE `chatbot_conversations`
  ADD CONSTRAINT `chatbot_conversations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `contests`
--
ALTER TABLE `contests`
  ADD CONSTRAINT `contests_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contest_participants`
--
ALTER TABLE `contest_participants`
  ADD CONSTRAINT `contest_participants_ibfk_1` FOREIGN KEY (`contest_id`) REFERENCES `contests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contest_participants_ibfk_2` FOREIGN KEY (`reader_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rentals`
--
ALTER TABLE `rentals`
  ADD CONSTRAINT `rentals_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rentals_ibfk_2` FOREIGN KEY (`reader_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
