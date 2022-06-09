-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2022 at 08:20 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(100) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `firstname`, `lastname`, `address`, `phone`, `email`, `birthday`) VALUES
(1, 'อนัญญา', 'เสนาะ', 'หนองไม้แดง ชลบุรี', '0878475985', 'ananya33106@hotmail.com', '2000-09-05'),
(2, 'Ruby', 'Rose', 'USA', '0665465656', 'rbrb6061@gmail.com', '1990-06-08');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `cusname` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `empname` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `total` float NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `cusname`, `phone`, `address`, `email`, `empname`, `description`, `total`, `date`) VALUES
(87, 'กวิน ไววิ่งรบ', '0885486061', '42/1 ซ.14 ลงหาดบางแสน', '61160084@go.buu.ac.th', 'กวิน ไววิ่งรบ', 'RAZER OROCHI V2 (WHITE) (1)', 1000, '2022-04-19 03:34:16'),
(88, 'ธนากร ราศรีชัย', '0855485123', 'อ่าวอุดม จังหวัดชลบุรี', 'thanakorn22@hotmail.com', 'กวิน ไววิ่งรบ', 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2 (1)', 46900, '2022-04-19 03:36:12'),
(89, 'Ruby Rose', '0665465656', 'USA', 'rbrb6061@gmail.com', 'กวิน ไววิ่งรบ', 'AM4 ASROCK X570 TAICHI (RAZER EDITION) (1)', 12200, '2022-04-19 04:28:15'),
(90, 'Ruby Rose', '0665465656', 'USA', 'rbrb6061@gmail.com', 'นิติธร กอบธรรม', 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2 (9)', 422100, '2022-04-19 04:47:14'),
(91, 'อนัญญา เสนาะ', '0878475985', 'หนองไม้แดง ชลบุรี', 'ananya33106@hotmail.com', 'นิติธร กอบธรรม', 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2 (9)', 422100, '2022-04-19 04:48:12'),
(92, 'Ruby Rose', '0665465656', 'USA', 'rbrb6061@gmail.com', 'นิติธร กอบธรรม', 'MSI GTX 1650 Super Gaming X (12)', 106806, '2022-04-19 04:56:50');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `id` int(11) NOT NULL,
  `cusname` varchar(255) NOT NULL,
  `empname` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `price` float NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`id`, `cusname`, `empname`, `name`, `qty`, `price`, `date`, `image`) VALUES
(165, 'กวิน ไววิ่งรบ', 'กวิน ไววิ่งรบ', 'RAZER OROCHI V2 (WHITE)', 1, 1000, '2022-04-19 03:34:16', '2021051709565146639_1.jpg'),
(166, 'ธนากร ราศรีชัย', 'กวิน ไววิ่งรบ', 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2', 1, 46900, '2022-04-19 03:36:12', '2022040811004752591_1.jpg'),
(167, 'Ruby Rose', 'กวิน ไววิ่งรบ', 'AM4 ASROCK X570 TAICHI (RAZER EDITION)', 1, 12200, '2022-04-19 04:28:15', '2021021109324145274_1.jpg'),
(168, 'Ruby Rose', 'นิติธร กอบธรรม', 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2', 9, 422100, '2022-04-19 04:47:14', '2022040811004752591_1.jpg'),
(169, 'อนัญญา เสนาะ', 'นิติธร กอบธรรม', 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2', 9, 422100, '2022-04-19 04:48:12', '2022040811004752591_1.jpg'),
(170, 'Ruby Rose', 'นิติธร กอบธรรม', 'MSI GTX 1650 Super Gaming X', 12, 106806, '2022-04-19 04:56:50', 'A0129214OK_BIG_1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) NOT NULL,
  `product_num` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `amount` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_num`, `name`, `description`, `type`, `price`, `amount`, `status`, `image`, `created_at`) VALUES
(1, '1198111174849', 'MSI GTX 1650 Super Gaming X', 'Boost Clock • Memory Speed 1755 MHz • 12 Gbps 4GB GDDR6 • DisplayPort x 3 / HDMI x 1', 'GPU', 8900.5, 3, 'พร้อมขาย', 'A0129214OK_BIG_1.jpg', '2022-03-14 13:30:02'),
(3, '6778111174847', 'Intel i5-10400F', ' 6 Cores • 12 Threads • Discrete Graphics Required, No Integrated Graphics.', 'CPU', 5050, 0, 'ไม่พร้อมขาย', '2020060310001739905_1.jpg', '2022-03-15 04:03:00'),
(4, '6518111174846', 'Intel i9-11900K', '8 Cores • 16 Threads • Intel UHD Graphics 750 • CPU Cooler Not Included', 'CPU', 19900, 0, 'ไม่พร้อมขาย', '20210825151309_46265_21_2.jpg', '2022-03-15 04:38:36'),
(5, '1598111174841', 'MSI OPTIX G24C4 23.6', 'Panel Size : 23.6', 'MONITOR', 8000, 0, 'ไม่พร้อมขาย', '20200115111856_35932_287_1.jpg', '2022-03-22 16:28:43'),
(6, '7598111174847', 'KINGSTON HyperX FURY (BLACK) 16GB', '16GB (8GBx2) • DDR4 • 2666MHz', 'RAM', 2590, 0, 'ไม่พร้อมขาย', '2019091313312935219_1.jpg', '2022-03-26 16:48:05'),
(8, '6528111174846', 'AM4 ASROCK X570 TAICHI (RAZER EDITION)', 'AMD AM4 • AMD X570 • 4 x DDR4 DIMM • ATX', 'MAINBOARD', 12200, 99, 'พร้อมขาย', '2021021109324145274_1.jpg', '2022-03-26 17:15:46'),
(13, '6598111174845', 'RAZER KRAKEN BT (KITTY EDITION) (QUARTZ)', 'Bluetooth 5.0 • 40ms low latency connection', 'HEADSET', 3590, 0, 'ไม่พร้อมขาย', '2020112111254843825_1.jpg', '2022-03-26 17:25:07'),
(22, '7577111186748', 'G.SKILL TRIDENT Z5 RGB (MATTE BLACK) 32GB', '32GB (16GBx2) • DDR5 • 5600MHz • F5-5600J4040C16GX2-TZ5RK', 'RAM', 15900, 0, 'ไม่พร้อมขาย', '2022040809265052588_1.jpg', '2022-04-10 18:11:03'),
(23, '6614111167752', 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2', 'GeForce RTX 3070 (LHR) • 8GB GDDR6 • 3 x DP • 2 x HDMI', 'GPU', 46900, 0, 'ไม่พร้อมขาย', '2022040811004752591_1.jpg', '2022-04-11 19:50:05'),
(24, '9057111188399', 'AM4 AMD RYZEN 5 5600X', '6 Cores • 12 Threads • Discrete Graphics Required, No Integrated Graphics.', 'CPU', 9590, 0, 'ไม่พร้อมขาย', '20210825104649_43471_21_2.jpg', '2022-04-11 19:50:56'),
(32, '4060111169621', 'GALAX GEFORCE RTX 3090 TI EX GAMER', '24GB GDDR6X • 3 x DP • 1 x HDMI', 'GPU', 87500, 0, 'ไม่พร้อมขาย', '2022040816520052608_1.jpg', '2022-04-18 17:32:13'),
(34, '4476111159930', 'RAZER OROCHI V2 (WHITE)', 'DPI : 18,000 • Button : 6 • Lighting : N/A • Connectivity : Bluetooth, Wireless 2.4GHz (USB Receiver Included)', 'MOUSE', 1000, 0, 'ไม่พร้อมขาย', '2021051709565146639_1.jpg', '2022-04-19 02:09:37');

-- --------------------------------------------------------

--
-- Table structure for table `stockpd`
--

CREATE TABLE `stockpd` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `emp_name` varchar(255) NOT NULL,
  `amount` int(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `stockpd`
--

INSERT INTO `stockpd` (`id`, `name`, `emp_name`, `amount`, `date`) VALUES
(55, 'G.SKILL TRIDENT Z5 RGB (MATTE BLACK) 32GB', 'Admin POS', 2, '2022-04-17 18:30:36'),
(56, 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2', 'Admin POS', 1, '2022-04-17 18:30:42'),
(57, 'MSI OPTIX G24C4 23.6\" 144Hz', 'Admin POS', 11, '2022-04-17 18:30:48'),
(59, 'MSI GTX 1650 Super Gaming X', 'กวิน ไววิ่งรบ', 1, '2022-04-18 15:09:45'),
(60, 'Intel i5-10400F', 'กวิน ไววิ่งรบ', 1, '2022-04-18 15:11:58'),
(61, 'KINGSTON HyperX FURY (BLACK) 16GB', 'กวิน ไววิ่งรบ', 1, '2022-04-18 15:12:22'),
(62, 'RAZER OROCHI V2 (WHITE)', 'Admin POS', 5, '2022-04-19 02:10:39'),
(63, 'AM4 ASROCK X570 TAICHI (RAZER EDITION)', 'กวิน ไววิ่งรบ', 100, '2022-04-19 04:02:26'),
(64, 'MSI GTX 1650 Super Gaming X', 'กวิน ไววิ่งรบ', 2, '2022-04-19 04:33:27'),
(65, 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2', 'นิติธร กอบธรรม', 9, '2022-04-19 04:47:56'),
(66, 'ASUS ROG-STRIX-RTX3070-O8G-WHITE-V2', 'นิติธร กอบธรรม', 9, '2022-04-19 04:48:02'),
(67, 'MSI GTX 1650 Super Gaming X', 'นิติธร กอบธรรม', 11, '2022-04-19 04:57:55'),
(68, 'MSI GTX 1650 Super Gaming X', 'นิติธร กอบธรรม', 2, '2022-04-20 08:39:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `urole` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `address`, `phone`, `email`, `password`, `birthday`, `urole`, `image`, `create_at`) VALUES
(1, 'admin', 'Admin', 'POS', '42/1 ซอย14 ถนนลงหาดบางแสน', '0885486061', 'adminpos@hotmail.com', 'e10adc3949ba59abbe56e057f20f883e', '2000-01-07', 'Admin', '140420391_870383383750805_1251306474811257881_n.jpg', '2022-02-13 16:52:04'),
(11, 'kawin', 'กวิน', 'ไววิ่งรบ', '42/1 ซ.14 ถนนลงหาดบางแสน', '0885486061', '61160084@go.buu.ac.th', 'e10adc3949ba59abbe56e057f20f883e', '2000-01-07', 'Employee', '61160084getstudentimage.jpg', '2022-03-26 15:24:23'),
(12, 'nitithon', 'นิติธร', 'กอบธรรม', 'บางแสน', '0958926670', '61160100@go.buu.ac.th', 'e10adc3949ba59abbe56e057f20f883e', '2022-03-26', 'Employee', '61160100getstudentimage.jpg', '2022-03-26 16:19:47'),
(13, 'watcharakorn', 'วัชรากรณ์', 'จงวิบูลย์', '42/1 ซ.14 ถนนลงหาดบางแสน', '0997980807', '61160116@go.buu.ac.th', 'e10adc3949ba59abbe56e057f20f883e', '2022-03-19', 'Employee', '61160116getstudentimage.jpg', '2022-03-26 16:26:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stockpd`
--
ALTER TABLE `stockpd`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `stockpd`
--
ALTER TABLE `stockpd`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
