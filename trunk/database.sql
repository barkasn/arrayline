-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 09, 2010 at 05:10 PM
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `dataset_processors`
--

INSERT INTO `dataset_processors` (`id`, `internal_name`, `name`, `has_no_accept_states`) VALUES
(1, 'RawDataUpload', 'Raw Data Upload', 1),
(2, 'FileNameRandomiser', 'File Name Randomiser', 0),
(3, 'AffymetrixUpload', 'Affymetrix Upload', 1),
(4, 'AffymetrixImporter', 'Import Affymetrix Data into R/Bioconductor', 0),
(6, 'AffymetrixRawQC', 'Affymetrix Raw QC Analysis', 0),
(7, 'AffymetrixNormalisation', 'Affymetrix Normalisation', 0);

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
(6, 5),
(7, 5);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `dataset_states`
--

INSERT INTO `dataset_states` (`id`, `internal_name`, `name`, `description`) VALUES
(1, 'rawData', 'Raw Data', 'Dataset resulting from the upload of raw data. For Development purposes.'),
(2, 'randomizedData', 'Randomised Data', 'Data from the experimental filename randomiser module'),
(3, 'affymetrixCelDataIncomplete', 'Affymetrix Non Finalised', 'Affymetric Raw .CEL Data - Not Finalised Dataset'),
(4, 'affymetrixCelDataComplete', 'Affymetrix Finalised Raw Data', 'Affymetrix Finalised Raw Data'),
(5, 'AffymetrixImportedData', 'Affymetrix Imported Data', 'Affymetrix Microarray Data in imported format'),
(6, 'affymetrixRawQC', 'Affymetrix Raw Data QC', 'Quality Control Plots for Raw Affymetrix Data'),
(7, 'affymetrixNormalised', 'Affymetrix Normalised Data', 'Affymetrix Normalised Microarray data');

-- --------------------------------------------------------

--
-- Table structure for table `datasets`
--

CREATE TABLE IF NOT EXISTS `datasets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `parent_dataset_id` int(11) DEFAULT NULL,
  `dataset_state_id` int(11) NOT NULL,
  `owner_user_id` int(11) NOT NULL,
  `dataset_processor_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=173 ;

--
-- Dumping data for table `datasets`
--

INSERT INTO `datasets` (`id`, `name`, `job_id`, `parent_dataset_id`, `dataset_state_id`, `owner_user_id`, `dataset_processor_id`, `created`, `deleted`) VALUES
(171, NULL, 212, 170, 6, 1, 6, '2010-07-09 16:57:00', 0),
(170, NULL, 211, 169, 5, 1, 4, '2010-07-09 16:51:00', 0),
(169, NULL, NULL, NULL, 4, 1, 3, '2010-07-09 16:40:00', 0),
(168, NULL, NULL, NULL, 3, 1, 3, '2010-07-09 16:32:00', 1),
(167, NULL, NULL, NULL, 3, 1, 3, '2010-07-09 16:15:00', 1),
(172, NULL, 213, 170, 7, 1, 7, '2010-07-09 16:58:00', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=214 ;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `job_state_id`, `description`, `autorun`, `run_start`, `run_end`, `comment`, `script_set_id`, `input_dataset_id`, `output_dataset_id`, `output_dataset_process_state_id`, `user_id`, `dataset_processor_id`, `data_cleared`, `process_id`) VALUES
(213, 9, 'Affymetrix Normalisation', 0, '2010-07-09 16:57:31', '2010-07-09 16:58:24', '0', 123, 170, NULL, 7, 1, 7, 1, 4840),
(212, 9, 'Affymetrix Raw QC Background Job', 0, '2010-07-09 16:51:54', '2010-07-09 16:57:12', '0', 122, 170, NULL, 6, 1, 6, 1, 4828),
(211, 9, 'Affymetrix Importer Background Job', 0, '2010-07-09 16:50:33', '2010-07-09 16:51:37', '0', 121, 169, NULL, 5, 1, 4, 1, 4820);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE IF NOT EXISTS `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `internal_name`, `name`) VALUES
(1, 'RawDataUpload', 'Raw Data Upload Module'),
(2, 'FileNameRandomiser', 'Randomises File Names'),
(3, 'AffymetrixUpload', 'Affymetrix Upload'),
(4, 'AffymetrixImporter', 'Affymetrix Data Importer in R'),
(5, 'AffymetrixRawQC', 'Affymetrix Raw QC Analysis'),
(6, 'AffymetrixNormalisation', 'Affymetrix Normalisation');

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
(5, 6),
(6, 7);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

--
-- Dumping data for table `script_sets`
--

INSERT INTO `script_sets` (`id`, `description`, `entry_script_id`) VALUES
(123, 'Temporary Scriptset', 11),
(122, 'Temporary Scriptset', 5),
(121, 'Temporary Scriptset', 4);

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
(121, 3),
(121, 4),
(122, 5),
(122, 6),
(123, 8),
(123, 11);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `scripts`
--

INSERT INTO `scripts` (`id`, `internal_name`, `filename`, `execution_command`, `can_be_called_directly`) VALUES
(1, 'randomizer', 'randomizer.sh', './randomizer.sh', 1),
(2, 'randomizerhelper', 'randomizerhelper.sh', '', 0),
(3, 'affyLoaderRscript', 'affyloader.R', '', 0),
(4, 'affyLoaderInit', 'affyloaderinit.sh', './affyloaderinit.sh', 1),
(5, 'affyRawQCInit', 'affyrawqcinit.sh', './affyrawqcinit.sh', 1),
(6, 'affyRawQCRscript', 'affyrawqcrscrinpt.R', '', 0),
(7, 'norm_gcrma', 'normalise.R', '', 0),
(8, 'norm_rma', 'normalise.R', '', 0),
(9, 'norm_vsnrma', 'normalise.R', '', 0),
(10, 'norm_mas5', 'normalise.R', '', 0),
(11, 'affyNormaliseInit', 'affynormaliseinit.sh', './affynormaliseinit.sh', 1),
(12, 'norm_quantiles', 'normalise.R', '', 0),
(13, 'norm_invariantset', 'normalise.R', '', 0),
(14, 'norm_cyclicloess', 'normalise.R', '', 0),
(15, 'norm_contrast', 'normalise.R', '', 0);

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
(3, 'library(affy)\r\ncovariates <- read.table("../input_data/covariates.csv",header=1, sep=",", quote="\\"")\r\nfilenames <- paste(c(''../input_data/''), covariates$Filename, sep='''')\r\nadf <- new("AnnotatedDataFrame",data=covariates)\r\nData <- ReadAffy(filenames=filenames,sampleNames=as.character(covariates$Unique.Sample.Identifier), phenoData= adf)\r\nsave(Data,file="../output_data/Data.Rdata")'),
(4, '#! /bin/bash\r\nR --vanilla < affyloader.R\r\ncd ..\r\ntouch JOB_COMPLETE\r\n'),
(5, ' #! /bin/bash\r\nR --vanilla < affyrawqcrscrinpt.R\r\ncd ..\r\ntouch JOB_COMPLETE'),
(6, 'library(affy)\r\nload(''../input_data/Data.Rdata'')\r\ncovariates <- pData(Data)\r\n\r\n# Avoid recalculating\r\npmData <- pm(Data)\r\nmmData <- mm(Data)\r\n\r\n# Array pseudo images\r\nfor( i in sampleNames(Data) ) {\r\n	png(filename=paste(''../output_data/'',i,''.png'', sep=''''), width=1000, height=1000)\r\n	image( Data[,i] )\r\n	dev.off()\r\n}\r\n\r\n# PM density plot\r\npng(filename=''../output_data/pm_density.png'', width=700, height=700)\r\nplotDensity(log2(pmData),lty=(covariates$Replicate.Identifier),col=1+as.numeric(covariates$Variable.Value.Identifier),main="Log2 PM intensities", ylab="Density",xlab="Log2 PM Intensity")\r\nlegend("topright",legend=covariates$Unique.Sample.Identifier, lty=(covariates$Replicate.Identifier),col=1+as.numeric(covariates$Variable.Value.Identifier))\r\ndev.off()\r\n\r\n# MM density plot\r\npng(filename=''../output_data/mm_density.png'', width=700, height=700)\r\nplotDensity(log2(mmData),lty=(covariates$Replicate.Identifier),col=1+as.numeric(covariates$Variable.Value.Identifier),main="Log2 MM intensities", ylab="Density",xlab="Log2 MM Intensity")\r\nlegend("topright",legend=covariates$Unique.Sample.Identifier, lty=(covariates$Replicate.Identifier),col=1+as.numeric(covariates$Variable.Value.Identifier))\r\ndev.off()\r\n\r\n# PM / MM density plot\r\npng(filename=''../output_data/pm_mm_density.png'', width=700, height=700)\r\nplotDensity(log2(pmData/mmData),lty=(covariates$Replicate.Identifier),col=1+as.numeric(covariates$Variable.Value.Identifier), main="Log2 (PM/MM) intensities", xlab="Log2 (PM/MM) Intensity")\r\nlegend("topright",legend=covariates$Unique.Sample.Identifier, lty=(covariates$Replicate.Identifier),col=1+as.numeric(covariates$Variable.Value.Identifier))\r\ndev.off()\r\n\r\n# Free some memory\r\nrm(pmData, mmData)\r\n\r\n# affyPLM commands\r\nlibrary(affyPLM)\r\nPset <- fitPLM(Data, PM ~ -1 + samples + probes)\r\n\r\n# Nuse plot\r\npng(filename=''../output_data/nuse.png'', width=700, height=700)\r\nNUSE(Pset, main="NUSE", las=3, col=1+as.numeric(covariates$Variable.Value.Identifier));\r\ndev.off()\r\n\r\n# RLE plot\r\npng(filename=''../output_data/rle.png'', width=700, height=700)\r\nRLE(Pset, main="RLE", las=3, col=1+as.numeric(covariates$Variable.Value.Identifier));\r\ndev.off()\r\n'),
(11, '#! /bin/bash\r\nR --vanilla < normalise.R\r\ncd ..\r\ntouch JOB_COMPLETE'),
(7, 'library(affy)\r\nlibrary(gcrma)\r\nload(''../input_data/Data.Rdata'')\r\nnormalisedData <- gcrma(Data)\r\nsave(normalisedData,file=''../output_data/Data.Rdata'')'),
(8, 'library(affy)\r\nload(''../input_data/Data.Rdata'')\r\nnormalisedData <- rma(Data)\r\nsave(normalisedData,file=''../output_data/Data.Rdata'')'),
(9, 'library(affy)\r\nlibrary(vsn)\r\nload(''../input_data/Data.Rdata'')\r\nnormalisedData <- normalize(Data,method="vsn")\r\nsave(normalisedData,file=''../output_data/Data.Rdata'')'),
(12, 'library(affy)\r\nload(''../input_data/Data.Rdata'')\r\nnormalisedData <- normalize(Data,method="quantiles")\r\nsave(normalisedData,file=''../output_data/Data.Rdata'')'),
(13, 'library(affy)\r\nload(''../input_data/Data.Rdata'')\r\nnormalisedData <- normalize(Data,method="invariantset")\r\nsave(normalisedData,file=''../output_data/Data.Rdata'')'),
(14, 'library(affy)\r\nload(''../input_data/Data.Rdata'')\r\nnormalisedData <- normalize(Data,method="loess")\r\nsave(normalisedData,file=''../output_data/Data.Rdata'')'),
(15, 'library(affy)\r\nload(''../input_data/Data.Rdata'')\r\nnormalisedData <- normalize(Data,method="contrast")\r\nsave(normalisedData,file=''../output_data/Data.Rdata'')');

-- --------------------------------------------------------

--
-- Table structure for table `system_log`
--

CREATE TABLE IF NOT EXISTS `system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=216 ;

--
-- Dumping data for table `system_log`
--

INSERT INTO `system_log` (`id`, `created`, `message`) VALUES
(215, '2010-07-09 16:58:25', 'Cron.php running complete'),
(214, '2010-07-09 16:58:23', 'Cron.php running'),
(213, '2010-07-09 16:57:32', 'Cron.php running complete'),
(212, '2010-07-09 16:57:30', 'Cron.php running'),
(211, '2010-07-09 16:57:14', 'Cron.php running complete'),
(210, '2010-07-09 16:57:11', 'Cron.php running'),
(209, '2010-07-09 16:51:55', 'Cron.php running complete'),
(208, '2010-07-09 16:51:53', 'Cron.php running'),
(207, '2010-07-09 16:51:39', 'Cron.php running complete'),
(206, '2010-07-09 16:51:36', 'Cron.php running'),
(205, '2010-07-09 16:50:43', 'Cron.php running complete'),
(204, '2010-07-09 16:50:42', 'Cron.php running'),
(203, '2010-07-09 16:50:34', 'Cron.php running complete'),
(202, '2010-07-09 16:50:33', 'Cron.php running');

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
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `passwordsha1`, `created`, `last_access`, `real_name`, `notes`, `room`, `telephone`, `email`, `deleted`) VALUES
(1, 'admin', 'dc6e038eca7bb16c5c84109e0100ae18f17dd8bb', '2010-05-26 00:00:00', '2010-07-09 13:13:00', 'System Administrator', '', '', '', '', 0);

