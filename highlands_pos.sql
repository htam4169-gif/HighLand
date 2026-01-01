-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 27, 2025 lúc 04:24 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `highlands_pos`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `color_code` varchar(20) DEFAULT '#FF8C00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `color_code`) VALUES
(1, 'Phin Coffee', '#d35400'),
(2, 'Espresso', '#e67e22'),
(3, 'Tea', '#27ae60'),
(4, 'Freeze', '#2980b9'),
(5, 'Bánh & Snack', '#9b59b6');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_type` varchar(20) DEFAULT 'Eat-in',
  `pager_number` int(11) DEFAULT NULL,
  `total_amount` decimal(10,0) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_type`, `pager_number`, `total_amount`, `created_at`) VALUES
(1, 'Eat-in', 0, 173000, '2025-12-26 08:15:03'),
(2, 'Eat-in', 1, 173000, '2025-12-27 03:22:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price_at_time` decimal(10,0) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `price_at_time`, `note`) VALUES
(1, 1, 1, 'Phin Sữa Đá S', 1, 29000, ''),
(2, 1, 17, 'Espresso Single', 1, 29000, ''),
(3, 1, 5, 'Trà Sen Vàng S', 1, 45000, ''),
(4, 1, 9, 'Freeze Trà Xanh S', 1, 55000, ''),
(5, 1, 11, 'Bánh Mì Que', 1, 15000, ''),
(6, 2, 1, 'Phin Sữa Đá S', 1, 29000, ''),
(7, 2, 17, 'Espresso Single', 1, 29000, ''),
(8, 2, 5, 'Trà Sen Vàng S', 1, 45000, ''),
(9, 2, 9, 'Freeze Trà Xanh S', 1, 55000, ''),
(10, 2, 11, 'Bánh Mì Que', 1, 15000, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `price`) VALUES
(1, 1, 'Phin Sữa Đá S', 29000),
(2, 1, 'Phin Sữa Đá L', 39000),
(3, 1, 'Phin Đen Đá S', 29000),
(4, 1, 'Phin Đen Đá L', 35000),
(5, 3, 'Trà Sen Vàng S', 45000),
(6, 3, 'Trà Sen Vàng L', 55000),
(7, 3, 'Trà Thạch Đào S', 45000),
(8, 3, 'Trà Thạch Đào L', 55000),
(9, 4, 'Freeze Trà Xanh S', 55000),
(10, 4, 'Chocolate Freeze S', 55000),
(11, 5, 'Bánh Mì Que', 15000),
(12, 1, 'Bạc Xỉu Đá', 29000),
(13, 1, 'Phin Di Hạnh Nhân S', 39000),
(14, 1, 'Phin Di Hạnh Nhân L', 49000),
(15, 1, 'Phin Di Kem Sữa S', 39000),
(16, 1, 'Phin Di Kem Sữa L', 49000),
(17, 2, 'Espresso Single', 29000),
(18, 2, 'Espresso Double', 39000),
(19, 2, 'Americano Đá', 35000),
(20, 2, 'Cappuccino Đá', 55000),
(21, 2, 'Latte Đá', 55000),
(22, 2, 'Mocha Đá', 59000),
(23, 2, 'Caramel Macchiato', 59000),
(24, 3, 'Trà Thạch Vải S', 45000),
(25, 3, 'Trà Thạch Vải L', 55000),
(26, 3, 'Trà Thanh Đào S', 45000),
(27, 3, 'Trà Thanh Đào L', 55000),
(28, 3, 'Trà Xanh Đậu Đỏ S', 45000),
(29, 3, 'Trà Xanh Đậu Đỏ L', 55000),
(30, 3, 'Trà Sen Vàng L', 55000),
(31, 4, 'Classic Phin Freeze S', 49000),
(32, 4, 'Classic Phin Freeze L', 59000),
(33, 4, 'Caramel Phin Freeze S', 49000),
(34, 4, 'Caramel Phin Freeze L', 59000),
(35, 4, 'Cookies & Cream S', 55000),
(36, 4, 'Cookies & Cream L', 65000),
(37, 4, 'Freeze Trà Xanh L', 65000),
(38, 5, 'Bánh Chuối', 19000),
(39, 5, 'Mousse Đào', 35000),
(40, 5, 'Mousse Cacao', 35000),
(41, 5, 'Phô Mai Cà Phê', 29000),
(42, 5, 'Tiramisu', 35000),
(43, 5, 'Bánh Su Kem', 15000),
(44, 5, 'Bánh Mì Thịt Nướng', 29000),
(45, 5, 'Bánh Mì Xíu Mại', 29000),
(46, 5, 'Bánh Mì Gà Xé', 29000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`) VALUES
(1, 'admin', '123456', 'Quản lý Highlands');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
