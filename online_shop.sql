-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2025 at 04:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `online_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'ไก่จ้าพี่มาแล้วจ่ะ'),
(2, 'ไก่อบ'),
(3, 'ไก่ทอด'),
(10, 'ไก่ต้ม'),
(11, 'ไก่แซ่บ');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `stock` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `is_featured`, `stock`, `category_id`, `image_url`, `created_at`) VALUES
(11, 'ไก่จ๋า', 'ไก่ จ๋า\r\nได้ยิน ไหมว่า เสียงใคร\r\nมันเหมือน เสียงคนร้องไห้\r\nแต่คล้าย ชายเจ้าน้ำตา\r\nเสียง นี้\r\nคือเสียง คนปวด อุรา\r\nจึงร้อง ครวญหา\r\nไก่จ๋า หลบหน้าไปไหน\r\nไก่ จ๋า\r\nขอเพียง หางตาเหลือบมอง\r\nฉันกลาย เป็นคนเศร้าหมอง\r\nฉันต้อง เหมือนคนสิ้นใจ\r\nหรือ ลืม\r\nสัญญา ที่เธอ ให้ไว้\r\nชาตินี้ จะไม่รักใคร\r\nไฉน ถึงผิดวาจา\r\nข่าว เขาว่า ไก่มีแฟนใหม่\r\nคง ร่ำรวยกันใหญ่\r\nสุขใจ อยู่กับเงินตรา\r\nแต่ไก่ รู้ไหม\r\nเมื่อยาม ไก่ยิ้มเต็มหน้า\r\nหัว ใจฉันแทบบ้า\r\nนอนนอง น้ำตาอยู่นาน\r\nไก่ จ๋า\r\nแม้ว่า ฉันต้อง ผิดหวัง\r\nทิ้งฉัน ไว้เพียงรำพัง\r\nจงลืม ความหลัง เมื่อวาน\r\nเปรียบ เหมือน\r\nลบรอย ชอล์กบน กระดาน\r\nโปรดจง รักเขา นานนาน\r\nลืมฉัน เสียเถิด ไก่จ๋า\r\n\r\n(พูด)ร้องไห้ ร้องไห้\r\nฉันได้แต่ร้องไห้\r\nปล่อยให้\r\nน้ำตาไหลลง เปื้อนหมอน\r\nทุกทุกวัน ทุกหยด รดที่นอน\r\nฉันท่วมเต็มเปียกหมอน\r\nว่ายน้ำตา\r\n\r\nข่าว เขาว่าไก่มีแฟนใหม่\r\nคง ร่ำรวยกันใหญ่\r\nสุขใจ อยู่กับเงิน ตรา\r\nแต่ไก่ รู้ไหม\r\nเมื่อยาม ไก่ยิ้มเต็มหน้า\r\nหัว ใจฉันแทบบ้า\r\nนอนนอง น้ำตาอยู่นาน\r\nไก่ จ๋า\r\nแม้ว่า ฉันต้อง ผิดหวัง\r\nทิ้งฉัน ไว้เพียงรำพัง\r\nจงลืม ความหลัง เมื่อวาน\r\nเปรียบ เหมือน\r\nลบรอย ชอล์กบน กระดาน\r\nโปรดจง รักเขา นานนาน\r\nลืมฉัน เสียเถิด ไก่จ๋า', 1.00, 0, 1, 1, 'img/prod_68e5b953423140.25366653.jpg', '2025-10-08 01:07:31'),
(12, 'สะโพกไก่ต้ม', '', 69.00, 0, 50, 10, 'img/prod_68e5b99dd23610.60500799.webp', '2025-10-08 01:08:45'),
(13, 'วิ้งไก่แซ่บๆ', '', 15.00, 0, 100, 11, 'img/prod_68e5b9b60c8916.43541891.jpg', '2025-10-08 01:09:10'),
(14, 'สะโพกไก่ย่าง', '', 69.00, 0, 500, 2, 'img/prod_68e5ba06edee58.04225330.jpg', '2025-10-08 01:10:30'),
(15, 'ปีกไก่ทอด', '', 20.00, 0, 45, 3, 'img/prod_68e5c6ca944756.65907919.webp', '2025-10-08 01:10:52'),
(16, 'น่องไก่ทอด', '', 15.00, 0, 100, 3, 'img/prod_68e5ba31a8a367.36527632.webp', '2025-10-08 01:11:13'),
(17, 'สะโพกไก่ทอด', '', 45.00, 0, 44, 3, 'img/prod_68e5ba4d026ec4.18503902.webp', '2025-10-08 01:11:41');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `shipping_status` enum('not_shipped','shipped','delivered') DEFAULT 'not_shipped'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT 'member',
  `profile_image` varchar(255) DEFAULT 'img/book.png',
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `full_name`, `role`, `profile_image`, `address`, `city`, `province`, `zip`, `phone`, `created_at`) VALUES
(1, 'admin1', 'admin_pass', 'admin1@example.com', 'Admin One', 'admin', 'img/book.png', NULL, NULL, NULL, NULL, NULL, '2025-08-07 03:24:49'),
(10, 'wit', '$2y$10$x/sYNUUZkV0IylgzQy1MeeCfPGt/Wvu9UmgDJfcslXJQJEG7qpHF.', 'wewe20489@gmail.com', 'wit ch', 'admin', 'img/profiles/profile_10_68e5bb00a76a1.png', NULL, NULL, NULL, NULL, NULL, '2025-09-04 02:32:21'),
(11, 'wit2', '$2y$10$d.yhDTqgBqTgSjx0VbVoNuJql/.K6yGGe5Xyy60yzzIKbaOoqQ8bK', 'we155@mail.com', 'wot', 'member', 'img/book.png', NULL, NULL, NULL, NULL, NULL, '2025-09-10 09:39:44'),
(14, 'wit1', '$2y$10$76WnV1K.QziB6iZtezEPbuDTl833BLKOeUfa.f/KiWamAEN.TKEre', 'wewe3@mail.com', 'PHET CH', 'member', 'img/profiles/profile_14_68dbfd185104d.png', NULL, NULL, NULL, NULL, NULL, '2025-09-11 03:22:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
