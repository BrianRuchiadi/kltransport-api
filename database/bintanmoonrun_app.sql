-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 25, 2018 at 10:59 AM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `bintanmoonrun_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `t9901_log_error_master`
--

CREATE TABLE `t9901_log_error_master` (
  `id` bigint(20) NOT NULL,
  `channel` varchar(255) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `message` longtext,
  `time` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t9901_log_error_master`
--
ALTER TABLE `t9901_log_error_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `channel` (`channel`) USING HASH,
  ADD KEY `level` (`level`) USING HASH,
  ADD KEY `time` (`time`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `t9901_log_error_master`
--
ALTER TABLE `t9901_log_error_master`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;
