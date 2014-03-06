-- phpMyAdmin SQL Dump
-- version 3.3.7deb3build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 06, 2011 at 07:53 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nagtrap`
--

-- --------------------------------------------------------

--
-- Table structure for table `snmptt`
--
DROP TABLE `snmptt`;
CREATE TABLE IF NOT EXISTS `snmptt` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `eventname` varchar(50) DEFAULT NULL,
  `eventid` varchar(50) DEFAULT NULL,
  `trapoid` varchar(100) DEFAULT NULL,
  `enterprise` varchar(100) DEFAULT NULL,
  `community` varchar(20) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `agentip` varchar(16) DEFAULT NULL,
  `category` varchar(20) DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `uptime` varchar(20) DEFAULT NULL,
  `traptime` varchar(30) DEFAULT NULL,
  `formatline` varchar(255) DEFAULT NULL,
  `trapread` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1820395 ;

--
-- Dumping data for table `snmptt`
--


-- --------------------------------------------------------

--
-- Table structure for table `snmptt_archive`
--
DROP TABLE `snmptt_archive`;
CREATE TABLE IF NOT EXISTS `snmptt_archive` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `snmptt_id` mediumint(9) NOT NULL DEFAULT '0',
  `eventname` varchar(50) DEFAULT NULL,
  `eventid` varchar(50) DEFAULT NULL,
  `trapoid` varchar(100) DEFAULT NULL,
  `enterprise` varchar(100) DEFAULT NULL,
  `community` varchar(20) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `agentip` varchar(16) DEFAULT NULL,
  `category` varchar(20) DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `uptime` varchar(20) DEFAULT NULL,
  `traptime` varchar(30) DEFAULT NULL,
  `formatline` varchar(255) DEFAULT NULL,
  `trapread` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=131 ;

--
-- Dumping data for table `snmptt_archive`
--


-- --------------------------------------------------------

--
-- Table structure for table `snmptt_jobs`
--
drop table `snmptt_jobs`;
CREATE TABLE IF NOT EXISTS `snmptt_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `jobstate` int(5) NOT NULL,
  `count` int(11) NOT NULL,
  `jobtime` int(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `snmptt_jobs`
--

INSERT INTO `snmptt_jobs` (`id`, `type`, `jobstate`, `count`, `jobtime`, `message`) VALUES
(1, 'archive', 0, 0, 0, ''),
(2, 'delete', 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `snmptt_statistics`
--
DROP TABLE `snmptt_statistics`;
CREATE TABLE IF NOT EXISTS `snmptt_statistics` (
  `stat_time` varchar(30) DEFAULT NULL,
  `total_received` bigint(20) DEFAULT NULL,
  `total_translated` bigint(20) DEFAULT NULL,
  `total_ignored` bigint(20) DEFAULT NULL,
  `total_unknown` bigint(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `snmptt_statistics`
--


-- --------------------------------------------------------

--
-- Table structure for table `snmptt_unknown`
--
DROP TABLE `snmptt_unknown`;
CREATE TABLE IF NOT EXISTS `snmptt_unknown` (
  `id` mediumint(9) NOT NULL,
  `trapoid` varchar(100) DEFAULT NULL,
  `enterprise` varchar(100) DEFAULT NULL,
  `community` varchar(20) DEFAULT NULL,
  `hostname` varchar(100) DEFAULT NULL,
  `agentip` varchar(16) DEFAULT NULL,
  `uptime` varchar(20) DEFAULT NULL,
  `traptime` varchar(30) DEFAULT NULL,
  `formatline` varchar(255) DEFAULT NULL,
  `severity` varchar(20) DEFAULT NULL,
  `trapread` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
