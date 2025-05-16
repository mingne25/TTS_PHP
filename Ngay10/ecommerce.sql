-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th5 16, 2025 lúc 05:48 AM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ecommerce`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `polls`
--

CREATE TABLE `polls` (
  `id` int NOT NULL,
  `option_name` varchar(50) DEFAULT NULL,
  `votes` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `polls`
--

INSERT INTO `polls` (`id`, `option_name`, `votes`) VALUES
(1, 'Interface', 3),
(2, 'Speed', 2),
(3, 'Customer Service', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int DEFAULT '0',
  `category` varchar(50) DEFAULT NULL,
  `brand` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `category`, `brand`, `image_url`) VALUES
(4, 'iPhone 14 Pro', 'Latest Apple smartphone', 999.99, 50, 'Electronics', 'Apple', 'https://shopdunk.com/images/thumbs/0027289_iphone-14-pro-max-chinh-hang-cu-dep_550.png'),
(5, 'Samsung TV', '4K Smart TV', 799.99, 30, 'Electronics', 'Samsung', 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEh4_nT3jfaXjVEPYD502mUBUKudpkPuNNXLti_coEnD3QqbGFYGoFv9FUKxKIvKZYCE6DCEeJnjkvjloqh3vPAR-Ez-kzXeSelHb8ux6X3aShT-Njhq_QJK15BrB5-bhrTas8fglOK4270/s640/gia-thay-man-hinh-tivi-samsung-chinh-hang.jpg'),
(6, 'Nike Air Max', 'Running shoes', 129.99, 100, 'Fashion', 'Nike', 'https://supersports.com.vn/cdn/shop/files/FD9082-106-1.jpg?v=1709806783&width=1000');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `product_id` int DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(4, 4, 'Ming', 5, 'Sản phẩm tốt', '2025-05-16 04:01:00'),
(5, 5, 'Giang', 4, 'Good but expensive', '2025-05-16 04:01:00');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
