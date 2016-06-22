-- phpMyAdmin SQL Dump
-- version 4.6.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 22, 2016 at 11:47 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.6.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portalmangueiral`
--

-- --------------------------------------------------------

--
-- Table structure for table `categoria`
--

CREATE TABLE `categoria` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cor` varchar(100) NOT NULL DEFAULT 'color',
  `icone` varchar(100) NOT NULL,
  `ordem` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categoria`
--

INSERT INTO `categoria` (`id`, `nome`, `cor`, `icone`, `ordem`) VALUES
(1, 'Veículos', 'blue', 'fa-automobile', 2),
(2, 'Animais', 'green', 'fa-paw', 11),
(4, 'Música e Instrumentos', 'light-red', 'fa-music', 7),
(5, 'Para sua casa', 'light-red', 'fa-home', 3),
(6, 'Eletrônicos e Celulares', 'light-orange', 'fa-mobile-phone', 8),
(7, 'Imóveis', 'green', 'fa-building-o', 5),
(8, 'Negócios e Empregos', 'color', 'fa-users', 4),
(9, 'Esportes', 'blue', 'fa-bicycle', 9),
(10, 'Comércio Local', 'blue', 'fa-dollar', 10),
(11, 'teste', 'color', 'fa-bicycle', 6),
(12, 'aaaa', 'color', 'fa-google-plus-circle', 12),
(15, 'ccc', 'color', 'fa-ambulance', 13),
(16, 'asdfadfs', 'color', 'fa-file', 14),
(17, 'Desapego', 'light-orange', 'fa-hand-stop-o', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
