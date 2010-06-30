-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 30, 2010 at 04:51 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `dataset_processors`
--

INSERT INTO `dataset_processors` (`id`, `internal_name`, `name`, `has_no_accept_states`) VALUES
(1, 'RawDataUpload', 'Raw Data Upload', 1),
(2, 'FileNameRandomiser', 'File Name Randomiser', 0),
(3, 'AffymetrixUpload', 'Affymetrix Upload', 1),
(4, 'AffymetrixImporter', 'Import Affymetrix Data into R/Bioconductor', 0),
(6, 'AffymetrixRawQC', 'Affymetrix Raw QC Analysis', 0);

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
(4, 4),
(6, 5);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `dataset_states`
--

INSERT INTO `dataset_states` (`id`, `internal_name`, `name`, `description`) VALUES
(1, 'rawData', 'Raw Data', 'Dataset resulting from the upload of raw data. For Development purposes.'),
(2, 'randomizedData', 'Randomised Data', 'Data from the experimental filename randomiser module'),
(3, 'affymetrixCelDataIncomplete', 'Affymetrix Non Finalised', 'Affymetric Raw .CEL Data - Not Finalised Dataset'),
(4, 'affymetrixCelDataComplete', 'Affymetrix Finalised Raw Data', 'Affymetrix Finalised Raw Data'),
(5, 'AffymetrixImportedData', 'Affymetrix Imported Data', 'Affymetrix Microarray Data in imported format'),
(6, 'affymetrixRawQC', 'Affymetrix Raw Data QC', 'Quality Control Plots for Raw Affymetrix Data');

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=115 ;

--
-- Dumping data for table `datasets`
--

INSERT INTO `datasets` (`id`, `job_id`, `parent_dataset_id`, `dataset_state_id`, `owner_user_id`, `dataset_processor_id`) VALUES
(114, NULL, NULL, 4, 1, 3),
(113, NULL, NULL, 3, 1, 3),
(112, NULL, NULL, 3, 1, 3),
(111, NULL, NULL, 3, 1, 3),
(110, 166, 108, 6, 1, 6),
(109, 165, 108, 6, 1, 6),
(108, 163, 104, 5, 1, 4),
(107, 162, 104, 5, 1, 4),
(106, 161, 104, 5, 1, 4),
(105, 159, 104, 5, 1, 4),
(104, NULL, NULL, 4, 1, 3);

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
  `run_start` datetime NOT NULL,
  `run_end` datetime NOT NULL,
  `comment` varchar(255) NOT NULL,
  `script_set_id` int(11) NOT NULL,
  `input_dataset_id` int(11) DEFAULT NULL,
  `output_dataset_id` int(11) DEFAULT NULL,
  `output_dataset_process_state_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `dataset_processor_id` int(11) DEFAULT NULL,
  `data_cleared` tinyint(1) NOT NULL,
  `process_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=174 ;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `job_state_id`, `description`, `autorun`, `run_start`, `run_end`, `comment`, `script_set_id`, `input_dataset_id`, `output_dataset_id`, `output_dataset_process_state_id`, `user_id`, `dataset_processor_id`, `data_cleared`, `process_id`) VALUES
(165, 9, 'Affymetrix Raw QC Background Job', 0, '2010-06-30 00:00:00', '0000-00-00 00:00:00', '0', 76, 108, NULL, 6, 1, 6, 1, NULL),
(163, 9, 'Affymetrix Importer Background Job', 0, '2010-06-30 00:00:00', '0000-00-00 00:00:00', '0', 74, 104, NULL, 5, 1, 4, 1, NULL),
(162, 9, 'Affymetrix Importer Background Job', 0, '2010-06-30 00:00:00', '0000-00-00 00:00:00', '0', 73, 104, NULL, 5, 1, 4, 1, NULL),
(161, 9, 'Affymetrix Importer Background Job', 0, '2010-06-30 00:00:00', '0000-00-00 00:00:00', '0', 72, 104, NULL, 5, 1, 4, 1, NULL),
(159, 9, 'Affymetrix Importer Background Job', 0, '2010-06-30 00:00:00', '0000-00-00 00:00:00', '0', 70, 104, NULL, 5, 1, 4, 1, NULL),
(166, 9, 'Affymetrix Raw QC Background Job', 0, '2010-06-30 15:21:08', '0000-00-00 00:00:00', '0', 77, 108, NULL, 6, 1, 6, 1, NULL),
(167, 9, 'Affymetrix Raw QC Background Job', 0, '2010-06-30 15:45:17', '0000-00-00 00:00:00', '0', 78, 108, NULL, 6, 1, 6, 0, NULL),
(168, 9, 'Affymetrix Raw QC Background Job', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 0, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(169, 9, 'Affymetrix Raw QC Background Job', 0, '2010-06-30 15:45:17', '0000-00-00 00:00:00', '0', 79, 108, NULL, 6, 1, 6, 0, NULL),
(170, 5, 'Affymetrix Importer Background Job', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 80, 114, NULL, 5, 1, 4, 0, NULL),
(171, 1, 'Affymetrix Importer Background Job', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 0, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(172, 1, 'Affymetrix Importer Background Job', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 0, NULL, NULL, NULL, NULL, NULL, 0, NULL),
(173, 5, 'Affymetrix Importer Background Job', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', 83, 114, NULL, 5, 1, 4, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `internal_name`, `name`) VALUES
(1, 'RawDataUpload', 'Raw Data Upload Module'),
(2, 'FileNameRandomiser', 'Randomises File Names'),
(3, 'AffymetrixUpload', 'Affymetrix Upload'),
(4, 'AffymetrixImporter', 'Affymetrix Data Importer in R'),
(5, 'AffymetrixRawQC', 'Affymetrix Raw QC Analysis');

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
(4, 4),
(5, 6);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84 ;

--
-- Dumping data for table `script_sets`
--

INSERT INTO `script_sets` (`id`, `description`, `entry_script_id`) VALUES
(82, 'Temporary Scriptset', 4),
(81, 'Temporary Scriptset', 4),
(80, 'Temporary Scriptset', 4),
(79, 'Temporary Scriptset', 5),
(78, 'Temporary Scriptset', 5),
(77, 'Temporary Scriptset', 5),
(76, 'Temporary Scriptset', 5),
(75, 'Temporary Scriptset', 5),
(74, 'Temporary Scriptset', 4),
(73, 'Temporary Scriptset', 4),
(72, 'Temporary Scriptset', 4),
(71, 'Temporary Scriptset', 5),
(70, 'Temporary Scriptset', 4),
(69, 'Temporary Scriptset', 4),
(68, 'Temporary Scriptset', 5),
(67, 'Temporary Scriptset', 4),
(66, 'Temporary Scriptset', 4),
(65, 'Temporary Scriptset', 4),
(64, 'Temporary Scriptset', 4),
(63, 'Temporary Scriptset', 4),
(62, 'Temporary Scriptset', 4),
(61, 'Temporary Scriptset', 4),
(60, 'Temporary Scriptset', 4),
(59, 'Temporary Scriptset', 1),
(83, 'Temporary Scriptset', 4);

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

INSERT INTO `script_sets_scripts` (`script_set_id`, `script_id`) VALUES
(59, 1),
(60, 3),
(60, 4),
(61, 3),
(61, 4),
(62, 3),
(62, 4),
(63, 3),
(63, 4),
(64, 3),
(64, 4),
(65, 3),
(65, 4),
(66, 3),
(66, 4),
(67, 3),
(67, 4),
(68, 5),
(68, 6),
(69, 3),
(69, 4),
(70, 3),
(70, 4),
(71, 5),
(71, 6),
(72, 3),
(72, 4),
(73, 3),
(73, 4),
(74, 3),
(74, 4),
(75, 5),
(75, 6),
(76, 5),
(76, 6),
(77, 5),
(77, 6),
(78, 5),
(78, 6),
(79, 5),
(79, 6),
(80, 3),
(80, 4),
(81, 3),
(81, 4),
(82, 3),
(82, 4),
(83, 3),
(83, 4);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `scripts`
--

INSERT INTO `scripts` (`id`, `internal_name`, `filename`, `execution_command`, `can_be_called_directly`) VALUES
(1, 'randomizer', 'randomizer.sh', './randomizer.sh', 1),
(2, 'randomizerhelper', 'randomizerhelper.sh', '', 0),
(3, 'affyLoaderRscript', 'affyloader.R', '', 0),
(4, 'affyLoaderInit', 'affyloaderinit.sh', './affyloaderinit.sh', 1),
(5, 'affyRawQCInit', 'affyrawqcinit.sh', './affyrawqcinit.sh', 1),
(6, 'affyRawQCRscript', 'affyrawqcrscrinpt.R', '', 0);

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
(2, 'Randomizer Helper body'),
(3, 'library(affy)\r\ncovariates <- read.table("../input_data/covariates.csv",header=1, sep="\\t", quote="\\"")\r\nfilenames <- paste(c(''../input_data/''), covariates$Filename, sep='''')\r\nadf <- new("AnnotatedDataFrame",data=covariates)\r\nData <- ReadAffy(filenames=filenames,sampleNames=as.character(covariates$Unique.Sample.Identifier), phenoData= adf)\r\nsave(Data,file="../output_data/Data.Rdata")'),
(4, '#! /bin/bash\r\nR --vanilla < affyloader.R\r\ncd ..\r\ntouch JOB_COMPLETE\r\n'),
(5, ' #! /bin/bash\r\nR --vanilla < affyrawqcrscrinpt.R\r\ncd ..\r\ntouch JOB_COMPLETE'),
(6, 'library(affy)\r\nload(''../input_data/Data.Rdata'')\r\ncovariates <- pData(Data)\r\n\r\n# PM density plot\r\npng(filename=''../output_data/pm_density.png'', width=700, height=700)\r\nplotDensity(log2(pm(Data)),lty=1,col=1+as.numeric(covariates$Variable.Value.Identifier),main="Log2 PM intensities", ylab="Density",xlab="Log2 PM Intensity")\r\ndev.off()\r\n\r\n# MM density plot\r\npng(filename=''../output_data/mm_density.png'', width=700, height=700)\r\nplotDensity(log2(mm(Data)),lty=1,col=1+as.numeric(covariates$Variable.Value.Identifier),main="Log2 PM intensities", ylab="Density",xlab="Log2 MM Intensity")\r\ndev.off()\r\n\r\n# Array pseudo images\r\nfor( i in sampleNames(Data) ) {\r\n	png(filename=paste(''../output_data/'',i,''.png'', sep=''''), width=700, height=700)\r\n	image( Data[,i] )\r\n	dev.off()\r\n}');

-- --------------------------------------------------------

--
-- Table structure for table `system_log`
--

CREATE TABLE IF NOT EXISTS `system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=76 ;

--
-- Dumping data for table `system_log`
--

INSERT INTO `system_log` (`id`, `created`, `message`) VALUES
(9, '2010-06-29 15:10:41', 'Cron.php running complete'),
(8, '2010-06-29 15:10:40', 'Cron.php running'),
(7, '2010-06-29 15:10:40', 'Cron.php running complete'),
(6, '2010-06-29 15:08:35', 'Cron.php running'),
(10, '2010-06-29 15:42:22', 'Cron.php running'),
(11, '2010-06-29 15:43:18', 'Cron.php running complete'),
(12, '2010-06-29 15:50:56', 'Cron.php running'),
(13, '2010-06-29 15:50:57', 'Cron.php running complete'),
(14, '2010-06-29 15:51:44', 'Cron.php running'),
(15, '2010-06-29 15:52:43', 'Cron.php running complete'),
(16, '2010-06-29 15:58:42', 'Cron.php running'),
(17, '2010-06-29 16:10:15', 'Cron.php running'),
(18, '2010-06-29 16:11:02', 'Cron.php running complete'),
(19, '2010-06-29 16:11:02', 'Cron.php running'),
(20, '2010-06-29 16:11:03', 'Cron.php running complete'),
(21, '2010-06-29 16:16:39', 'Cron.php running'),
(22, '2010-06-29 16:16:39', 'Cron.php running complete'),
(23, '2010-06-29 16:18:06', 'Cron.php running'),
(24, '2010-06-29 16:18:06', 'Cron.php running complete'),
(25, '2010-06-29 16:18:45', 'Cron.php running'),
(26, '2010-06-29 16:18:45', 'Cron.php running complete'),
(27, '2010-06-29 16:19:30', 'Cron.php running'),
(28, '2010-06-29 16:19:30', 'Cron.php running complete'),
(29, '2010-06-29 16:21:06', 'Cron.php running'),
(30, '2010-06-29 16:21:07', 'Cron.php running complete'),
(31, '2010-06-29 16:21:56', 'Cron.php running'),
(32, '2010-06-29 16:22:33', 'Cron.php running complete'),
(33, '2010-06-29 16:23:50', 'Cron.php running'),
(34, '2010-06-29 16:23:52', 'Cron.php running complete'),
(35, '2010-06-29 16:25:02', 'Cron.php running'),
(36, '2010-06-29 16:25:55', 'Cron.php running'),
(37, '2010-06-29 16:25:56', 'Cron.php running complete'),
(38, '2010-06-29 16:27:22', 'Cron.php running'),
(39, '2010-06-29 16:27:27', 'Cron.php running complete'),
(40, '2010-06-30 12:08:34', 'Cron.php running'),
(41, '2010-06-30 12:08:35', 'Cron.php running complete'),
(42, '2010-06-30 12:08:36', 'Cron.php running'),
(43, '2010-06-30 12:08:37', 'Cron.php running complete'),
(44, '2010-06-30 12:09:35', 'Cron.php running'),
(45, '2010-06-30 12:09:40', 'Cron.php running complete'),
(46, '2010-06-30 12:21:51', 'Cron.php running'),
(47, '2010-06-30 12:21:52', 'Cron.php running complete'),
(48, '2010-06-30 12:22:08', 'Cron.php running'),
(49, '2010-06-30 12:22:09', 'Cron.php running complete'),
(50, '2010-06-30 12:23:35', 'Cron.php running'),
(51, '2010-06-30 12:23:36', 'Cron.php running complete'),
(52, '2010-06-30 12:25:14', 'Cron.php running'),
(53, '2010-06-30 12:25:15', 'Cron.php running complete'),
(54, '2010-06-30 12:25:58', 'Cron.php running'),
(55, '2010-06-30 12:25:59', 'Cron.php running complete'),
(56, '2010-06-30 12:26:41', 'Cron.php running'),
(57, '2010-06-30 12:26:45', 'Cron.php running complete'),
(58, '2010-06-30 14:28:45', 'Cron.php running'),
(59, '2010-06-30 14:28:46', 'Cron.php running complete'),
(60, '2010-06-30 15:20:35', 'Cron.php running'),
(61, '2010-06-30 15:20:36', 'Cron.php running complete'),
(62, '2010-06-30 15:21:07', 'Cron.php running'),
(63, '2010-06-30 15:21:09', 'Cron.php running complete'),
(64, '2010-06-30 15:22:15', 'Cron.php running'),
(65, '2010-06-30 15:22:16', 'Cron.php running complete'),
(66, '2010-06-30 15:23:04', 'Cron.php running'),
(67, '2010-06-30 15:23:05', 'Cron.php running complete'),
(68, '2010-06-30 15:25:11', 'Cron.php running'),
(69, '2010-06-30 15:25:12', 'Cron.php running complete'),
(70, '2010-06-30 15:26:15', 'Cron.php running'),
(71, '2010-06-30 15:26:17', 'Cron.php running complete'),
(72, '2010-06-30 15:45:17', 'Cron.php running'),
(73, '2010-06-30 15:45:18', 'Cron.php running complete'),
(74, '2010-06-30 15:55:28', 'Cron.php running'),
(75, '2010-06-30 15:55:29', 'Cron.php running complete');

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

