
-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `main_title` varchar(255) NOT NULL,
  `main_description` text NOT NULL,
  `feature1_title` varchar(255) NOT NULL,
  `feature1_description` text NOT NULL,
  `feature1_icon` varchar(50) NOT NULL,
  `feature2_title` varchar(255) NOT NULL,
  `feature2_description` text NOT NULL,
  `feature2_icon` varchar(50) NOT NULL,
  `feature3_title` varchar(255) NOT NULL,
  `feature3_description` text NOT NULL,
  `feature3_icon` varchar(50) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `main_title`, `main_description`, `feature1_title`, `feature1_description`, `feature1_icon`, `feature2_title`, `feature2_description`, `feature2_icon`, `feature3_title`, `feature3_description`, `feature3_icon`, `updated_at`) VALUES
(1, 'About Us', 'HAHAHA', 'Fast Delivery', 'Quick and reliable shipping nationwide', 'fas fa-shipping-fast', 'Secure Shopping', '100% secure payment processing charot', 'fas fa-shield-alt', '24/7 Support', 'Always here to help our customers', 'fas fa-headset', '2025-02-17 12:16:44');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$2qd4RbSEINPhhpCjEQiexOJYUinbs4gVLuckHnbnhDbwQmDZ8eI8y', '2025-02-17 07:54:50'),
(7, 'user', '$2y$10$OadVQCOMOmzEcK/Q3snpteEl99jTF1HHFVMLYjScrgB8/c1wi1YJy', '2025-02-17 08:13:42');

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` text DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`id`, `title`, `subtitle`, `background_image`, `button_text`, `button_link`, `created_at`, `updated_at`) VALUES
(1, 'Commeee', 'dasdasd', 'banner_1739793601.jpg', 'Order Now', 'choose.php', '2025-02-17 12:00:01', '2025-02-17 12:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `address`, `phone`, `email`, `facebook_url`, `tiktok`, `instagram_url`, `updated_at`) VALUES
(1, 'Balais, Lagangilang, Abra', '09351455907', 'onlineshopjmy@gmail.com', 'https://www.facebook.com/johnrix.domaoal.1', 'https://www.tiktok.com/johnrix.domaoal.1', '', '2025-02-17 12:30:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `user_id` int(11) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `total_amount`, `shipping_address`, `phone_number`, `email`, `status`, `created_at`, `payment_method`, `payment_status`, `user_id`, `payment_proof`) VALUES
(1, 1346.00, 'AHAHAHA', '09351455907', 'janggisdump@gmail.com', 'pending', '2025-02-17 13:25:31', '', 'pending', NULL, NULL),
(2, 1123.00, 'Balais, Lagangilang, Abra', '09351455907', 'janggisdump@gmail.com', 'pending', '2025-02-17 14:36:12', 'cod', 'pending', 1, NULL),
(3, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:53:32', 'gcash', 'pending', 1, NULL),
(4, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:53:32', 'gcash', 'pending', 1, NULL),
(5, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:53:46', 'cod', 'pending', 1, NULL),
(6, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:53:46', 'cod', 'pending', 1, NULL),
(7, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:53:54', 'cod', 'pending', 1, NULL),
(8, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:53:54', 'cod', 'pending', 1, NULL),
(9, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:02', 'cod', 'pending', 1, NULL),
(10, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:02', 'cod', 'pending', 1, NULL),
(11, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:02', 'cod', 'pending', 1, NULL),
(12, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:22', 'cod', 'pending', 1, NULL),
(13, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:22', 'cod', 'pending', 1, NULL),
(14, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:22', 'cod', 'pending', 1, NULL),
(15, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:36', 'cod', 'pending', 1, NULL),
(16, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:36', 'cod', 'pending', 1, NULL),
(17, NULL, NULL, NULL, NULL, 'pending', '2025-02-17 14:59:36', 'cod', 'pending', 1, NULL),
(18, NULL, NULL, NULL, NULL, '', '2025-02-17 15:01:26', 'gcash', 'pending', 1, '67b34f46b027a_1_AtG6hjP_AaSPN0boemNy4A.webp'),
(19, NULL, NULL, NULL, NULL, '', '2025-02-17 15:01:26', 'gcash', 'pending', 1, '67b34f46b2f73_1_AtG6hjP_AaSPN0boemNy4A.webp'),
(20, NULL, NULL, NULL, NULL, '', '2025-02-17 15:01:26', 'gcash', 'pending', 1, '67b34f46b540b_1_AtG6hjP_AaSPN0boemNy4A.webp');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 7, 2, 223.00),
(2, 1, 8, 1, 900.00),
(3, 2, 7, 1, 223.00),
(4, 2, 8, 1, 900.00),
(5, 3, NULL, 1, NULL),
(6, 4, NULL, 1, NULL),
(7, 5, NULL, 1, NULL),
(8, 6, NULL, 1, NULL),
(9, 7, NULL, 1, NULL),
(10, 8, NULL, 1, NULL),
(11, 9, NULL, 1, NULL),
(12, 10, NULL, 1, NULL),
(13, 11, 7, 1, 223.00),
(14, 12, NULL, 1, NULL),
(15, 13, 7, 1, 223.00),
(16, 14, NULL, 1, NULL),
(17, 15, NULL, 1, NULL),
(18, 16, NULL, 2, NULL),
(19, 17, 7, 2, 223.00),
(20, 18, NULL, 1, NULL),
(21, 19, NULL, 1, NULL),
(22, 20, 7, 1, 223.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_status` enum('Pending','Completed','Failed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `discount` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('available','not_available') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `price`, `description`, `image`, `discount`, `created_at`, `updated_at`, `status`) VALUES
(7, 'Daniel Padilla', 223.00, 'dasda', '67b31310ec975.webp', NULL, '2025-02-17 10:44:32', '2025-02-17 10:44:32', 'available'),
(8, 'Daniel Padilla', 1000.00, 'ahahaha', '67b31464d454a.jpg', 10.00, '2025-02-17 10:50:12', '2025-02-17 13:01:13', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone_number`, `address`, `profile_picture`) VALUES
(1, 'John Rix Domaoal', 'janggisdump@gmail.com', '$2y$10$q/MpuhknPCatA3NJjFpxeOkvT5F3mxcmKfN0lOcjWcp5Wibw7MzpO', '09351455907', 'Balais, Lagangilang, Abra', 'profile_67b347736cabb.jpg');

--
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_users` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);


-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `new_table_name` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `new_table_name` (`order_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
