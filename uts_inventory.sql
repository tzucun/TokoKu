-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 01, 2025 at 06:23 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uts_inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(23, 'CRSL Drinke Tumblr', '𝐂𝐑𝐒𝐋 𝐃𝐫𝐢𝐧𝐤𝐞 𝐓𝐮𝐦𝐛𝐥𝐞𝐫 𝐒𝐞𝐫𝐢𝐞𝐬\r\n\r\nColor : Sky Blue, Dusty Pink , Sunflower, Tosca\r\n\r\nMaterial : STAINLESS STEEL\r\n\r\n\r\n\r\nNeed a bigger water bottle?\r\n\r\nNo worries! Drinke is the perfect size to keep you hydrated, refreshed, and ready to slay every day. It’s designed for all-day sipping and is leak-proof with a handy handle!\r\n\r\nNot just a bottle, Drinkee is a style statement and comes in two different colors.', '190000.00', 17, '67e51e67bd785-id-11134207-7rbk7-m7fw64741rfr90.jpg', '2025-03-27 09:44:54'),
(24, 'CRSL Tugo Tumblr', 'Tumbler keren yang bisa digunakan untuk air panas maupun air dingin selama 8 jam', '150000.00', 20, '67e51ec769cb9-id-11134207-7rbka-m7hon1dk39okb6.jpg', '2025-03-27 09:47:51'),
(25, 'CRSL Raku Backpack', 'Get ready to break free from traditional norms with our 𝐑𝐚𝐤𝐮 𝐁𝐚𝐜𝐤𝐩𝐚𝐜𝐤. The expressive design allows you to showcase your distinct style, setting you apart in a world of ordinary whether you&#039;re sprinting between classes or hitting up a study session.The versatile silhouette made from mild crinkle, completed with a chest buckle strap, ensures comfort without compromising on style. Features hidden back compartment for your valuables, front and side pockets for easy access to essentials, and a sleek cover lid as &quot;click-secure&quot; to top it off .', '300000.00', 26, '67e51f09d49cf-id-11134207-7r98v-lrxz54z0s7lpb8.jpg', '2025-03-27 09:48:57'),
(27, 'iPhone 15', 'iPhone 15 menghadirkan Dynamic Island, kamera Utama 48 MP, dan USB-C — semuanya dalam desain aluminium dan kaca berwarna yang tangguh. \r\n\r\n\r\n\r\nIsi Kotak :\r\n\r\n• iPhone dengan iOS 17.\r\n\r\n• Kabel USB-C ke USB-C.\r\n\r\n• Buku Manual dan dokumentasi lain.', '11499000.00', 13, '67e51fb4921a0-iphone_15_plus_hero.png', '2025-03-27 09:51:48'),
(28, 'iPhone 13', 'iPhone 13. Sistem kamera ganda paling canggih yang pernah ada di iPhone. Chip A15 Bionic yang secepat kilat. Lompatan besar dalam kekuatan baterai. Desain kokoh. Dan layar Super Retina XDR yang lebih cerah.\r\n\r\n\r\n\r\n\r\n\r\nIsi Kotak :\r\n\r\n• iPhone dengan iOS 15.\r\n\r\n• Kabel USB-C ke Lightning.\r\n\r\n• Buku Manual dan Dokumentasi lain.', '8249000.00', 17, '67e51ff3321eb-111872_iphone13-colors-480.png', '2025-03-27 09:52:51'),
(29, 'Labubu Have A Seat', 'Nama Merek: POP MART\r\n\r\nInformasi Produk: THE MONSTERS - Have a Seat Vinyl Plush Blind Box\r\n\r\nUkuran Produk: Tinggi sekitar 15 cm\r\n\r\n⁂Bahan Produk: PVC / ABS / Polyester / Iron Wire / Nylon', '240000.00', 55, '67e520516830b-01052969_d.jpg', '2025-03-27 09:54:25'),
(30, 'Hacipupu The Constellation Series Vinyl ', 'Discover the rare and exclusive Pop Mart Hacipupu The Constellation Series Vinyl Plush Blind Box,  perfect for collectors and enthusiasts alike. Ideal for displaying or adding to your growing collection, Pop Mart Hacipupu The Constellation Series Vinyl Plush Blind Box is a true collector’s gem.', '220000.00', 45, '67e5209f58543-id-11134207-7rasl-m2zbh73snmdt19.jpg', '2025-03-27 09:55:43'),
(31, 'CRSL Wallet', 'CRSL WALLET DOMPET', '123321.00', 45, '68013880740e8-b815dd252d2de07ef0a5e014440dcb71.jpg', '2025-04-17 17:21:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'halo', 'a@gmail.com', '$2y$10$XQNNzafsB1163fv33GDTNeJW5jCuIu3.gBqG676vt7V02frAOIoEG', '2025-05-01 17:19:16'),
(2, 'a', '777@gmail.com', '$2y$10$gV9KHXHw4iLbZBSIIDI28.FWYayUWRo464gBsOC6qYNk80wySoHEi', '2025-05-01 18:18:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
