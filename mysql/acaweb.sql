-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 13, 2008 at 06:53 AM
-- Server version: 5.0.27
-- PHP Version: 5.2.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `acaweb`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `search_cache`
-- 

CREATE TABLE IF NOT EXISTS `search_cache` (
  `id` int(11) NOT NULL auto_increment,
  `search_terms` varchar(255) NOT NULL,
  `vendor_dbs` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=180 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `search_cache_results`
-- 

CREATE TABLE IF NOT EXISTS `search_cache_results` (
  `id` int(11) NOT NULL auto_increment,
  `search_id` int(11) NOT NULL,
  `title` varchar(255) character set latin1 NOT NULL,
  `authors` varchar(255) character set latin1 NOT NULL,
  `abstract` mediumtext,
  `source` varchar(255) default NULL,
  `issn` varchar(20) character set latin1 NOT NULL,
  `volume` varchar(50) character set latin1 NOT NULL,
  `issue` varchar(50) character set latin1 NOT NULL,
  `pages` varchar(255) character set latin1 NOT NULL,
  `start_page` varchar(10) character set latin1 NOT NULL,
  `date` varchar(20) character set latin1 NOT NULL,
  `url` varchar(255) character set latin1 NOT NULL,
  `resolver_url` mediumtext character set latin1,
  `doc_id` varchar(50) character set latin1 NOT NULL,
  `full_text_available` varchar(10) character set latin1 NOT NULL,
  `normalized_author` varchar(255) character set latin1 NOT NULL,
  `normalized_date` varchar(20) character set latin1 NOT NULL,
  `html_full_text` longblob,
  `rank` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=4055 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `search_cache_vendors`
-- 

CREATE TABLE IF NOT EXISTS `search_cache_vendors` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `vendor_id` varchar(255) character set latin1 NOT NULL,
  `total_results` varchar(20) NOT NULL,
  `load_time` int(11) default NULL,
  `datestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=243 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `vendor_dbs`
-- 

CREATE TABLE IF NOT EXISTS `vendor_dbs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `ss_id` varchar(10) default NULL,
  `custom_loader` varchar(255) default NULL,
  `description` mediumtext,
  `date_added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `active` char(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='Core table of PSU databases' AUTO_INCREMENT=75 ;

