-- EATZY Database Schema for Joomla 6
-- Component: com_eatzy

CREATE TABLE IF NOT EXISTS `#__eatzy_menu` (
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

CREATE TABLE IF NOT EXISTS `#__eatzy_orders` (
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
  `payment_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `order_status` varchar(50) NOT NULL DEFAULT 'Received',
  `transaction_ref` varchar(100) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__eatzy_order_items` (
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

CREATE TABLE IF NOT EXISTS `#__eatzy_reservations` (
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

CREATE TABLE IF NOT EXISTS `#__eatzy_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_number` varchar(20) NOT NULL UNIQUE,
  `zone` varchar(50) NOT NULL DEFAULT 'Indoor Main Dining',
  `capacity` int(11) NOT NULL DEFAULT 4,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed Signature Philippine Fast-Casual Dishes
INSERT INTO `#__eatzy_menu` (`name`, `category`, `price`, `description`, `image_url`, `dietary_tags`, `spice_level`, `is_special`, `is_available`) VALUES
('Smoked Angus Truffle Burger', 'Mains', 380.00, 'Handcrafted brioche, sharp cheddar, black truffle aioli, wagyu-blend smoked patty.', 'images/foods/Burgers-Truffle-Burger-scaled.jpg', 'Chef Special', 1, 1, 1),
('Double Smash Cheeseburger', 'Mains', 290.00, 'Crispy-edged twin smashed patties, double American cheese, caramelized onions, secret house relish.', 'images/foods/Double Smash Cheeseburger.jpg', '', 0, 0, 1),
('Creamy Pesto Fettuccine', 'Mains', 320.00, 'Hand-rolled fettuccine pasta, vibrant Genovese basil pesto, toasted pine nuts, aged parmigiano reggiano.', 'images/foods/Creamy Pesto Fettuccine.jpg', 'Vegetarian', 0, 0, 1),
('Spicy Garlic Gambas Bowl', 'Mains', 390.00, 'Black tiger prawns pan-roasted in bird''s eye chili, sweet roasted garlic, and Spanish olive oil over jasmine rice.', 'images/foods/Spicy Garlic Gambas Bowl.jpg', 'Gluten-Free', 3, 1, 1),
('Crispy Pork Belly Lechon Bowl', 'Mains', 340.00, '24-hour slow-cooked pork belly with blistered crackling, spiced liver glaze, garlic rice, and house atchara.', 'images/foods/Crispy Pork Belly Lechon Bowl.jpg', '', 1, 0, 1),
('Loaded Truffle Parmesan Fries', 'Starters', 210.00, 'Hand-cut russet fries tossed in white truffle essence, freshly shaved parmesan, sea salt, and fresh chives.', 'images/foods/Loaded Truffle Parmesan Fries.jpg', 'Vegetarian', 0, 0, 1),
('Honey Sriracha Glazed Wings', 'Starters', 260.00, 'Double-fried crisp chicken wings glazed in wild honey sriracha sauce with toasted white sesame.', 'images/foods/Honey Sriracha Glazed Wings.jpg', '', 2, 0, 1),
('Artisanal Garlic Sourdough', 'Starters', 170.00, 'Warm grilled levain sourdough slices brushed with roasted confit garlic, grass-fed butter, and parsley.', 'images/foods/Artisanal Garlic Sourdough.jpg', 'Vegetarian', 0, 0, 1),
('Calamansi Honey Sparkling Spritzer', 'Drinks', 130.00, 'Freshly pressed Philippine calamansi, Palawan wild floral honey, and chilled sparkling club soda.', 'images/foods/Calamansi Honey Sparkling Spritzer.jpg', 'Signature Drink', 0, 1, 1),
('Cold Brew Barako Latte', 'Drinks', 150.00, '16-hour steeped Batangas Liberica coffee, oat milk float, and light muscovado syrup.', 'images/foods/Cold Brew Barako Latte.jpg', 'Vegan Option', 0, 0, 1),
('Roselle Hibiscus Berry Iced Tea', 'Drinks', 140.00, 'Artisan-brewed roselle hibiscus flowers, crushed strawberries, mint leaves, and cane syrup.', 'images/foods/Roselle Hibiscus Berry Iced Tea.jpg', 'Gluten-Free', 0, 0, 1),
('Ube Halaya Crème Brûlée', 'Desserts', 220.00, 'Velvety purple yam custard topped with torched amber caramelized sugar crust and macapuno strings.', 'images/foods/Ube Halaya Crème Brûlée.jpg', 'Chef Special', 0, 1, 1),
('Molten Valrhona Chocolate Cake', 'Desserts', 250.00, 'Warm chocolate cake with decadent oozing center, served with vanilla bean gelato and Maldon flakes.', 'images/foods/Molten Valrhona Chocolate Cake.jpg', 'Vegetarian', 0, 0, 1);

-- Seed Restaurant Tables
INSERT INTO `#__eatzy_tables` (`table_number`, `zone`, `capacity`, `is_active`) VALUES
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

