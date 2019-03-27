-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 27-Mar-2019 às 20:34
-- Versão do servidor: 5.7.24
-- versão do PHP: 7.2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demoshop`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_digest` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_users_on_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `password_digest`) VALUES
(1, 'Joao Viana', 'jmviana126@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b');

-- --------------------------------------------------------

--
-- Estrutura da tabela `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `total` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `orders`
--

INSERT INTO `orders` (`id`, `client_id`, `created_at`, `status`, `total`) VALUES
(1, 1, '2019-03-21 22:21:22', 1, 3406.97),
(2, 1, '2019-03-25 18:53:06', 1, 526.96);

-- --------------------------------------------------------

--
-- Estrutura da tabela `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(2, 1, 2, 1),
(7, 1, 1, 2),
(8, 1, 4, 3),
(19, 2, 7, 1),
(20, 2, 13, 1),
(21, 2, 4, 1),
(22, 2, 10, 1),
(23, 2, 8, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `warranty` varchar(255) DEFAULT NULL,
  `price` float NOT NULL,
  `new_price` float DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `brand`, `warranty`, `price`, `new_price`, `image`, `category`) VALUES
(1, 'Macbook Air 13\'\' APPLE MQD32', 'Intel Core i7 - 8 GB RAM - 128 GB SSD - Silver', 'APPLE', '2 years', 1300, 1049, 'SP753macbook-air.jpeg', 'PCs'),
(2, 'Smartphone SAMSUNG Galaxy S10+', '6.4\'\' - 8 GB - 512 GB - White Ceramic', 'SAMSUNG', '2 years', 1279, NULL, 'Smartphone_SAMSUNG_Galaxy_S10+.jpg', 'Smart Devices'),
(3, 'TV LG 55UK6200', 'LCD - 55\'\' - 140 cm - 4K Ultra HD - Smart TV', 'LG', '2 years', 649.99, 469.99, 'TV_LG_55UK6200.jpg', 'Televisions'),
(4, 'Mouse TRUST Primo', 'Wireless - Optical - 1600 dpi - Black', 'TRUST', '2 years', 9.99, NULL, 'Rato_TRUST_Primo.jpg', 'PC Acessories'),
(5, 'Columns HIDITEC Hiditec H450 Black', NULL, 'Hiditec', '2 years', 39.99, NULL, 'Colunas_HIDITEC_Hiditec_H450_Preto.jpg', NULL),
(6, 'Bluetooth Keyboard KL-TECH KTB0026 Mini in Black', NULL, 'KL-TECH', '2 years', 23, NULL, 'Teclado_Bluetooth_KL-TECH_KTB0026_Mini_em_Preto.jpg', 'PC Acessories'),
(7, 'Nintendo Switch Game The Legend of Zelda: Breath of the Wild', NULL, 'NINTENDO', '2 years', 69.99, NULL, 'The-Legend-of-Zelda-Breath-of-the-Wild-Nintendo-Switch.jpg', 'Games'),
(8, 'Gaming Headphones HYPER X Cloud II', 'Wired - PC', 'HYPERX', '2 years', 104.99, NULL, 'Microauscultadores_Gaming_HYPERX_Cloud_II.jpg', NULL),
(9, 'Rato LOGITECH M570', 'Optical - Grey', 'LOGITECH', '2 years', 75.99, NULL, 'Rato_LOGITECH_M570.jpg', 'PC Acessories'),
(10, 'Mousepad CREATIVE Creative Labs Sound Blasterx', NULL, 'CREATIVE', '2 years', 12, NULL, 'Tapete_de_Rato_CREATIVE_Creative_Labs_Sound_Blasterx.jpg', 'PC Acessories'),
(11, 'PC ACER Aspire Es1-572-33D5', '15.6\'\' - Intel Core i3-6006U - 4 GB RAM - 1 TB HDD - Intel HD Graphics 520', 'ACER', '2 years', 449.99, 399.99, 'Portatil_ACER_Aspire_Es1-572-33D5.jpg', 'PCs'),
(12, 'PS4 Game God of War Normal Edition', NULL, 'SONY-COMPUTER', '2 years', 69.99, NULL, 'Jogo_PS4_God_of_War_Edicao_Normal.jpg', 'Games'),
(13, 'Nintendo Switch Console', '32 GB - Neon Blue and Red', 'NINTENDO', '2 years', 329.99, NULL, 'Nintendo_Switch.jpg', 'Game Consoles'),
(14, 'Mouse LOGITECH G402 Black', NULL, 'LOGITECH', '2 years', 65, NULL, 'Rato_LOGITECH_G402_em_Preto.jpg', 'PC Acessories'),
(15, 'Smartphone HUAWEI P20 Pro', '6.1\'\' - 6 GB - 128 GB - Black', 'HUAWEI', '2 years', 799.99, 579.99, 'Smartphone_HUAWEI_P20_Pro.jpg', 'Smart Devices');

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

--
-- Limitadores para a tabela `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
