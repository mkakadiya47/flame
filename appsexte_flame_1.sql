-- phpMyAdmin SQL Dump
-- version 4.0.10.18
-- https://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2017 at 01:30 PM
-- Server version: 5.6.35
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `appsexte_flame`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`) VALUES
(1, 'Food & Drinks'),
(2, 'Desserts'),
(3, 'Coffee'),
(4, 'Activities'),
(5, 'Health'),
(6, 'Beauty'),
(7, 'Services'),
(8, 'Retail');

-- --------------------------------------------------------

--
-- Table structure for table `flame`
--

CREATE TABLE IF NOT EXISTS `flame` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `address` text,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `flame`
--

INSERT INTO `flame` (`id`, `title`, `website`, `mobile`, `address`, `latitude`, `longitude`, `category_id`) VALUES
(1, 'Test flame', 'www.flame.com', '1234567890', 'Muli, Gujarat 363510, India', '22.690784', '71.219177', NULL),
(2, 'Test flame', 'www.flame.com', '1234567890', 'Muli, Gujarat 363510, India', '22.690784', '71.219177', NULL),
(3, 'Test flame', 'www.flame.com', '1234567890', 'Muli, Gujarat 363510, India', '22.690784', '71.219177', NULL),
(4, 'Test Flame', 'www.flame.com', '1234567890', 'Muli, Gujarat 363310, India', '22.821661', '71.248917', 1);

-- --------------------------------------------------------

--
-- Table structure for table `flame_audio`
--

CREATE TABLE IF NOT EXISTS `flame_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_flame_id` int(11) NOT NULL,
  `audio` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_flame_id` (`user_flame_id`),
  KEY `user_id_2` (`user_id`),
  KEY `user_flame_id_2` (`user_flame_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `flame_image`
--

CREATE TABLE IF NOT EXISTS `flame_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_flame_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_flame_id` (`user_flame_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `flame_image`
--

INSERT INTO `flame_image` (`id`, `user_id`, `user_flame_id`, `image`) VALUES
(1, 4, 1, '57e77aa9276ae_6954bbe1e879ca71de7085d22acef721.png'),
(2, 4, 2, '57e77c0a4c036_0ffcc2f25679560bbc950d028b45471e.png'),
(3, 4, 3, '57e77ca7d2adb_e790638f005ed76d27d096b4579726ae.png'),
(4, 4, 4, '57e77d6c43aec_0700bd74a5a2bf4b2b2d8b4e82219949.png');

-- --------------------------------------------------------

--
-- Table structure for table `flame_video`
--

CREATE TABLE IF NOT EXISTS `flame_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_flame_id` int(11) NOT NULL,
  `video` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_flame_id` (`user_flame_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `follower`
--

CREATE TABLE IF NOT EXISTS `follower` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `follower_id` (`follower_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `following`
--

CREATE TABLE IF NOT EXISTS `following` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `following_id` (`following_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `address` text,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `address`, `username`, `email`, `password`, `image`) VALUES
(2, 'manoj', 'kakadiya', 'rajkot', 'manoj', 'mkakadiya47@gmail.com', 'b6e30e95fc08141c3ad3132e2f9259c1', NULL),
(3, 'Bhavesh', 'Lathigara', 'Rajkot AppsExtent', 'bhavesh', 'bhaveshsoni13@gmail.com', '2c23ec98041d2d78c90db61bee5c3652', '57e37eacafa71_6edccadb2d1c0616bae5e00c8f8d4f84.png'),
(4, 'Flame', 'Test', 'Test Address', 'flame', 'flame@test.com', '098f6bcd4621d373cade4e832627b4f6', '57e39726b12b5_d1151714bb1a58caadcacfda0e2b4600.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_comment`
--

CREATE TABLE IF NOT EXISTS `user_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_flame_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`user_flame_id`),
  KEY `user_flame_id` (`user_flame_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_flame`
--

CREATE TABLE IF NOT EXISTS `user_flame` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `flame_id` int(11) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `flame_id` (`flame_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user_flame`
--

INSERT INTO `user_flame` (`id`, `user_id`, `flame_id`, `description`) VALUES
(1, 4, 1, 'Test'),
(2, 4, 2, 'Test'),
(3, 4, 3, 'Test'),
(4, 4, 4, 'Test Flamed by Bhavesh');

-- --------------------------------------------------------

--
-- Table structure for table `user_like`
--

CREATE TABLE IF NOT EXISTS `user_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_flame_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_flame_id` (`user_flame_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flame_audio`
--
ALTER TABLE `flame_audio`
  ADD CONSTRAINT `user_flame` FOREIGN KEY (`user_flame_id`) REFERENCES `user_flame` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_flame_image` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `flame_image`
--
ALTER TABLE `flame_image`
  ADD CONSTRAINT `user_audeo` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_flame_audeo` FOREIGN KEY (`user_flame_id`) REFERENCES `user_flame` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `flame_video`
--
ALTER TABLE `flame_video`
  ADD CONSTRAINT `user_flame_flame_video` FOREIGN KEY (`user_flame_id`) REFERENCES `user_flame` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_flame_video` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `follower`
--
ALTER TABLE `follower`
  ADD CONSTRAINT `user_forllower` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_forllower_id` FOREIGN KEY (`follower_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `following`
--
ALTER TABLE `following`
  ADD CONSTRAINT `user_following` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_following_id` FOREIGN KEY (`following_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_comment`
--
ALTER TABLE `user_comment`
  ADD CONSTRAINT `user_comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_comment_ibfk_2` FOREIGN KEY (`user_flame_id`) REFERENCES `user_flame` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_flame`
--
ALTER TABLE `user_flame`
  ADD CONSTRAINT `flame_id` FOREIGN KEY (`flame_id`) REFERENCES `flame` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_like`
--
ALTER TABLE `user_like`
  ADD CONSTRAINT `user_like_user_flame_id` FOREIGN KEY (`user_flame_id`) REFERENCES `user_flame` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_like_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
