-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2018 at 09:53 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `auth` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `access` varchar(225) NOT NULL DEFAULT 'z',
  `flags` varchar(5) NOT NULL DEFAULT 'a',
  `lastonline` int(11) NOT NULL,
  `email` varchar(225) NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'default.png',
  `warns` int(11) NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL DEFAULT 'user'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='AMX Mod X Admins';

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `auth`, `password`, `access`, `flags`, `lastonline`, `email`, `avatar`, `warns`, `type`) VALUES
(1, 'admin', 'admin', 'z', 'a', 0, 'cristea_gabriel@yahoo.com', 'default.png', 0, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `admin_access`
--

CREATE TABLE `admin_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `access` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_access`
--

INSERT INTO `admin_access` (`id`, `access`, `name`) VALUES
(1, 'z', 'Nume Rezervat');

-- --------------------------------------------------------

--
-- Table structure for table `advanced_bans`
--

CREATE TABLE `advanced_bans` (
  `victim_name` varchar(32) NOT NULL,
  `victim_steamid` varchar(35) NOT NULL,
  `banlength` varchar(32) NOT NULL,
  `unbantime` varchar(32) NOT NULL,
  `reason` varchar(128) NOT NULL,
  `admin_name` varchar(64) NOT NULL,
  `admin_steamid` varchar(35) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
) ENGINE=InnoDB;

-- --------------------------------------------------------

--
-- Table structure for table `shop_items`
--

CREATE TABLE `shop_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `produs` varchar(255) NOT NULL,
  `descriere` mediumtext NOT NULL,
  `pret` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `by_auth` varchar(255) NOT NULL,
  `query` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shop_buy_log`
--

CREATE TABLE `shop_buy_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(255) NOT NULL,
  `pret` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'eroare / frauda',
  `paymentid` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user` varchar(32) NOT NULL,
  `actiune` mediumtext NOT NULL,
  `time` datetime NOT NULL,
  `user_ip` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(200) NOT NULL,
  `msg` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `panel_suspend`
--

CREATE TABLE `panel_suspend` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(32) NOT NULL,
  `suspend` int(11) NOT NULL,
  `suspenddown` int(11) NOT NULL,
  `reason` varchar(32) NOT NULL,
  `by_auth` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
