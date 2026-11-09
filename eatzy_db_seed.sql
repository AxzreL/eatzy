-- =======================================================
-- EATZY: Database Seed Script for XAMPP / MySQL
-- Direct import for phpMyAdmin or MySQL CLI
-- Database: eatzy_db
-- =======================================================

CREATE DATABASE IF NOT EXISTS `eatzy_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `eatzy_db`;

-- Menu Items Table
CREATE TABLE IF NOT EXISTS `eatzy_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Mains',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `dietary_tags` varchar(255) DEFAULT '',
  `spice_level` tinyint(2) NOT NULL DEFAULT 0,
  `is_special` tinyint(1) NOT NULL DEFAULT 0,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table
CREATE TABLE IF NOT EXISTS `eatzy_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL UNIQUE,
  `customer_name` varchar(150) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `delivery_address` text NOT NULL,
  `delivery_notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'gcash',
  `payment_status` varchar(50) NOT NULL DEFAULT 'Paid',
  `order_status` varchar(50) NOT NULL DEFAULT 'Kitchen Preparing',
  `transaction_ref` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Items Table
CREATE TABLE IF NOT EXISTS `eatzy_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  INDEX (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table Reservations
CREATE TABLE IF NOT EXISTS `eatzy_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reservation_code` varchar(50) NOT NULL UNIQUE,
  `guest_name` varchar(150) NOT NULL,
  `guest_phone` varchar(30) NOT NULL,
  `guest_email` varchar(150) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `time_slot` varchar(30) NOT NULL,
  `party_size` int(11) NOT NULL DEFAULT 2,
  `seating_zone` varchar(50) NOT NULL DEFAULT 'Indoor Main Dining',
  `status` varchar(50) NOT NULL DEFAULT 'Confirmed',
  `special_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Physical Tables
CREATE TABLE IF NOT EXISTS `eatzy_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_number` varchar(20) NOT NULL UNIQUE,
  `zone` varchar(50) NOT NULL DEFAULT 'Indoor Main Dining',
  `capacity` int(11) NOT NULL DEFAULT 4,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Philippine Fast-Casual Dishes
TRUNCATE TABLE `eatzy_menu`;
INSERT INTO `eatzy_menu` (`name`, `category`, `price`, `description`, `image_url`, `dietary_tags`, `spice_level`, `is_special`, `is_available`) VALUES
('Smoked Angus Truffle Burger', 'Mains', 380.00, 'Handcrafted brioche, sharp cheddar, black truffle aioli, wagyu-blend smoked patty.', 'media/com_eatzy/images/angus-truffle-burger.webp', 'Chef Special', 1, 1, 1),
('Double Smash Cheeseburger', 'Mains', 290.00, 'Crispy-edged twin smashed patties, double American cheese, caramelized onions, secret house relish.', 'media/com_eatzy/images/double-smash-burger.webp', '', 0, 0, 1),
('Creamy Pesto Fettuccine', 'Mains', 320.00, 'Hand-rolled fettuccine pasta, vibrant Genovese basil pesto, toasted pine nuts, aged parmigiano reggiano.', 'media/com_eatzy/images/creamy-pesto-pasta.webp', 'Vegetarian', 0, 0, 1),
('Spicy Garlic Gambas Bowl', 'Mains', 390.00, 'Black tiger prawns pan-roasted in bird''s eye chili, sweet roasted garlic, and Spanish olive oil over jasmine rice.', 'media/com_eatzy/images/garlic-gambas-bowl.webp', 'Gluten-Free', 3, 1, 1),
('Crispy Pork Belly Lechon Bowl', 'Mains', 340.00, '24-hour slow-cooked pork belly with blistered crackling, spiced liver glaze, garlic rice, and house atchara.', 'media/com_eatzy/images/crispy-pork-belly.webp', '', 1, 0, 1),
('Loaded Truffle Parmesan Fries', 'Starters', 210.00, 'Hand-cut russet fries tossed in white truffle essence, freshly shaved parmesan, sea salt, and fresh chives.', 'media/com_eatzy/images/truffle-fries.webp', 'Vegetarian', 0, 0, 1),
('Honey Sriracha Glazed Wings', 'Starters', 260.00, 'Double-fried crisp chicken wings glazed in wild honey sriracha sauce with toasted white sesame.', 'media/com_eatzy/images/sriracha-wings.webp', '', 2, 0, 1),
('Artisanal Garlic Sourdough', 'Starters', 170.00, 'Warm grilled levain sourdough slices brushed with roasted confit garlic, grass-fed butter, and parsley.', 'media/com_eatzy/images/garlic-sourdough.webp', 'Vegetarian', 0, 0, 1),
('Calamansi Honey Sparkling Spritzer', 'Drinks', 130.00, 'Freshly pressed Philippine calamansi, Palawan wild floral honey, and chilled sparkling club soda.', 'media/com_eatzy/images/calamansi-spritzer.webp', 'Signature Drink', 0, 1, 1),
('Cold Brew Barako Latte', 'Drinks', 150.00, '16-hour steeped Batangas Liberica coffee, oat milk float, and light muscovado syrup.', 'media/com_eatzy/images/barako-latte.webp', 'Vegan Option', 0, 0, 1),
('Roselle Hibiscus Berry Iced Tea', 'Drinks', 140.00, 'Artisan-brewed roselle hibiscus flowers, crushed strawberries, mint leaves, and cane syrup.', 'media/com_eatzy/images/hibiscus-berry-tea.webp', 'Gluten-Free', 0, 0, 1),
('Ube Halaya Crème Brûlée', 'Desserts', 220.00, 'Velvety purple yam custard topped with torched amber caramelized sugar crust and macapuno strings.', 'media/com_eatzy/images/ube-creme-brulee.webp', 'Chef Special', 0, 1, 1),
('Molten Valrhona Chocolate Cake', 'Desserts', 250.00, 'Warm chocolate cake with decadent oozing center, served with vanilla bean gelato and Maldon flakes.', 'media/com_eatzy/images/molten-chocolate-cake.webp', 'Vegetarian', 0, 0, 1);

-- Tables
TRUNCATE TABLE `eatzy_tables`;
INSERT INTO `eatzy_tables` (`table_number`, `zone`, `capacity`, `is_active`) VALUES
('T01', 'Indoor Main Dining', 2, 1),
('T02', 'Indoor Main Dining', 2, 1),
('T03', 'Indoor Main Dining', 4, 1),
('T04', 'Indoor Main Dining', 4, 1),
('T05', 'Indoor Main Dining', 6, 1),
('T06', 'Indoor Main Dining', 8, 1),
('P01', 'Al Fresco Garden Patio', 2, 1),
('P02', 'Al Fresco Garden Patio', 4, 1),
('P03', 'Al Fresco Garden Patio', 4, 1),
('P04', 'Al Fresco Garden Patio', 6, 1),
('P05', 'Al Fresco Garden Patio', 8, 1);

-- Demo Live Order
INSERT INTO `eatzy_orders` (`order_number`, `customer_name`, `customer_phone`, `delivery_address`, `delivery_notes`, `subtotal`, `delivery_fee`, `discount`, `total`, `payment_method`, `payment_status`, `order_status`, `transaction_ref`) VALUES
('EATZ-8921', 'Mark Santos', '09178829101', 'Unit 14B, One Bonifacio High Street, BGC, Taguig', 'Leave with lobby reception.', 700.00, 0.00, 0.00, 700.00, 'gcash', 'Paid', 'Kitchen Preparing', 'GCASH-TXN-902819');

INSERT INTO `eatzy_order_items` (`order_id`, `item_id`, `item_name`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 'Smoked Angus Truffle Burger', 1, 380.00, 380.00),
(1, 3, 'Creamy Pesto Fettuccine', 1, 320.00, 320.00);

-- Demo Reservation
INSERT INTO `eatzy_reservations` (`reservation_code`, `guest_name`, `guest_phone`, `reservation_date`, `time_slot`, `party_size`, `seating_zone`, `status`, `special_notes`) VALUES
('EATZ-RES-7712', 'Bianca Reyes', '09285514022', CURDATE(), '7:00 PM', 4, 'Indoor Main Dining', 'Confirmed', 'Anniversary dinner table requested.');

