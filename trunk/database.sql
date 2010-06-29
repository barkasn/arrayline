-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 29, 2010 at 02:06 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `arrayline`
--
CREATE DATABASE `arrayline` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `arrayline`;

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE IF NOT EXISTS `attributes` (
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`key`, `value`) VALUES
('jobSchedulerLock', '0');

-- --------------------------------------------------------

--
-- Table structure for table `dataset_processors`
--

CREATE TABLE IF NOT EXISTS `dataset_processors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `has_no_accept_states` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `dataset_processors`
--

INSERT INTO `dataset_processors` (`id`, `internal_name`, `name`, `has_no_accept_states`) VALUES
(1, 'RawDataUpload', 'Raw Data Upload', 1),
(2, 'FileNameRandomiser', 'File Name Randomiser', 0),
(3, 'AffymetrixUpload', 'Affymetrix Upload', 1),
(4, 'AffymetrixImporter', 'Import Affymetrix Data into R/Bioconductor', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dataset_processors_accept_states`
--

CREATE TABLE IF NOT EXISTS `dataset_processors_accept_states` (
  `dataset_processor_id` int(11) NOT NULL,
  `dataset_state_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dataset_processors_accept_states`
--

INSERT INTO `dataset_processors_accept_states` (`dataset_processor_id`, `dataset_state_id`) VALUES
(2, 1),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `dataset_processors_produce_states`
--

CREATE TABLE IF NOT EXISTS `dataset_processors_produce_states` (
  `dataset_processor_id` int(11) NOT NULL,
  `dataset_state_id` int(11) NOT NULL,
  UNIQUE KEY `dataset_processor_id` (`dataset_processor_id`,`dataset_state_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dataset_processors_produce_states`
--


-- --------------------------------------------------------

--
-- Table structure for table `dataset_states`
--

CREATE TABLE IF NOT EXISTS `dataset_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `dataset_states`
--

INSERT INTO `dataset_states` (`id`, `internal_name`, `name`, `description`) VALUES
(1, 'rawData', 'Raw Data', 'Dataset resulting from the upload of raw data. For Development purposes.'),
(2, 'randomizedData', 'Randomised Data', 'Data from the experimental filename randomiser module'),
(3, 'affymetrixCelDataIncomplete', 'Affymetrix Non Finalised', 'Affymetric Raw .CEL Data - Not Finalised Dataset'),
(4, 'affymetrixCelDataComplete', 'Affymetrix Finalised Raw Data', 'Affymetrix Finalised Raw Data'),
(5, 'AffymetrixImportedData', 'Affymetrix Imported Data', 'Affymetrix Microarray Data in imported format');

-- --------------------------------------------------------

--
-- Table structure for table `datasets`
--

CREATE TABLE IF NOT EXISTS `datasets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_id` int(11) DEFAULT NULL,
  `parent_dataset_id` int(11) DEFAULT NULL,
  `dataset_state_id` int(11) NOT NULL,
  `owner_user_id` int(11) NOT NULL,
  `dataset_processor_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=97 ;

--
-- Dumping data for table `datasets`
--

INSERT INTO `datasets` (`id`, `job_id`, `parent_dataset_id`, `dataset_state_id`, `owner_user_id`, `dataset_processor_id`) VALUES
(96, NULL, NULL, 1, 1, 1),
(95, NULL, NULL, 3, 1, 3),
(94, NULL, NULL, 4, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `job_states`
--

CREATE TABLE IF NOT EXISTS `job_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `job_states`
--

INSERT INTO `job_states` (`id`, `internal_name`, `name`) VALUES
(1, 'toBeSetup', 'To be setup'),
(2, 'toBePreprocessed', 'To be preprocessed'),
(3, 'preprocessing', 'Preprocessing'),
(4, 'preprocessedHalted', 'Preprocessed Halted'),
(5, 'toBeRun', 'To be Run'),
(6, 'processRunning', 'Process Running'),
(7, 'processComplete', 'Process Complete'),
(8, 'postProcessing', 'Post-processing'),
(9, 'complete', 'Job Complete'),
(10, 'failed', 'Job Failed');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_state_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `autorun` tinyint(1) NOT NULL,
  `run_start` date NOT NULL,
  `run_end` date NOT NULL,
  `comment` varchar(255) NOT NULL,
  `script_set_id` int(11) NOT NULL,
  `input_dataset_id` int(11) DEFAULT NULL,
  `output_dataset_id` int(11) DEFAULT NULL,
  `output_dataset_process_state_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `dataset_processor_id` int(11) DEFAULT NULL,
  `data_cleared` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=148 ;

--
-- Dumping data for table `jobs`
--


-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `internal_name`, `name`) VALUES
(1, 'RawDataUpload', 'Raw Data Upload Module'),
(2, 'FileNameRandomiser', 'Randomises File Names'),
(3, 'AffymetrixUpload', 'Affymetrix Upload'),
(4, 'AffymetrixImporter', 'Affymetrix Data Importer in R');

-- --------------------------------------------------------

--
-- Table structure for table `modules_dataset_processors`
--

CREATE TABLE IF NOT EXISTS `modules_dataset_processors` (
  `module_id` int(11) NOT NULL,
  `dataset_processor_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `modules_dataset_processors`
--

INSERT INTO `modules_dataset_processors` (`module_id`, `dataset_processor_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `internal_name`, `name`, `description`) VALUES
(1, 'manageusers', 'Manage User Accounts', 'Manage and Administer User Acoounts');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `roles_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  UNIQUE KEY `roles_id` (`roles_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `role_permissions`
--


-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `roles`
--


-- --------------------------------------------------------

--
-- Table structure for table `script_sets`
--

CREATE TABLE IF NOT EXISTS `script_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `entry_script_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=59 ;

--
-- Dumping data for table `script_sets`
--


-- --------------------------------------------------------

--
-- Table structure for table `script_sets_scripts`
--

CREATE TABLE IF NOT EXISTS `script_sets_scripts` (
  `script_set_id` int(11) NOT NULL,
  `script_id` int(11) NOT NULL,
  UNIQUE KEY `script_set_id` (`script_set_id`,`script_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `script_sets_scripts`
--


-- --------------------------------------------------------

--
-- Table structure for table `scripts`
--

CREATE TABLE IF NOT EXISTS `scripts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `execution_command` varchar(255) NOT NULL,
  `can_be_called_directly` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `scripts`
--

INSERT INTO `scripts` (`id`, `internal_name`, `filename`, `execution_command`, `can_be_called_directly`) VALUES
(1, 'randomizer', 'randomizer.sh', './randomizer.sh', 1),
(2, 'randomizerhelper', 'randomizerhelper.sh', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `scripts_bodies`
--

CREATE TABLE IF NOT EXISTS `scripts_bodies` (
  `script_id` int(11) NOT NULL,
  `script_body` text NOT NULL,
  UNIQUE KEY `script_id` (`script_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `scripts_bodies`
--

INSERT INTO `scripts_bodies` (`script_id`, `script_body`) VALUES
(1, '#! /bin/bash\r\n\r\ncp ../input_data/* ../output_data\r\ncd ../output_data\r\nfor file in `ls` \r\ndo\r\n        randomfilename=$RANDOM\r\n        mv $file $randomfilename\r\ndone\r\ncd ..\r\ntouch JOB_COMPLETE\r\n'),
(2, 'Randomizer Helper body');

-- --------------------------------------------------------

--
-- Table structure for table `system_log`
--

CREATE TABLE IF NOT EXISTS `system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `system_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE IF NOT EXISTS `user_permissions` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`user_id`, `permission_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_roles`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `passwordsha1` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_access` datetime NOT NULL,
  `real_name` varchar(255) NOT NULL,
  `notes` text NOT NULL,
  `room` varchar(255) NOT NULL,
  `telephone` text NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `passwordsha1`, `created`, `last_access`, `real_name`, `notes`, `room`, `telephone`, `email`) VALUES
(1, 'nikolas', 'a28cc654d85c1d3cb8418061db20859c322a0bc6', '2010-05-26 00:00:00', '2010-05-26 00:00:00', '', '', '', '', '');

