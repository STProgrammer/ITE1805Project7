-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 09, 2020 at 12:37 AM
-- Server version: 10.2.31-MariaDB-log
-- PHP Version: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP VIEW IF EXISTS `stud_v20_karagoz`.`Elements`, `stud_v20_karagoz`.`FilesWithTagsView`;

DROP TABLE IF EXISTS `stud_v20_karagoz`.`commentsAudit`, `stud_v20_karagoz`.`Action`,
`stud_v20_karagoz`.`FilesAndTags`, `stud_v20_karagoz`.`Tags`, `stud_v20_karagoz`.`Comments`; 

DROP TABLE IF EXISTS `stud_v20_karagoz`.`Files`;

DROP TABLE IF EXISTS `stud_v20_karagoz`.`Catalogs`;

DROP TABLE IF EXISTS `stud_v20_karagoz`.`Users`;

--
-- Database: `stud_v20_karagoz`
--

-- --------------------------------------------------------

--
-- Table structure for table `Action`
--

CREATE TABLE `Action` (
  `action` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Action`
--

INSERT INTO `Action` (`action`) VALUES
('Delete'),
('Update');

-- --------------------------------------------------------

--
-- Table structure for table `Catalogs`
--

CREATE TABLE `Catalogs` (
  `catalogId` int(11) NOT NULL,
  `parentId` int(11) DEFAULT 1,
  `catalogName` varchar(45) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `access` tinyint(4) NOT NULL DEFAULT 1,
  `owner` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='	';

--
-- Dumping data for table `Catalogs`
--

INSERT INTO `Catalogs` (`catalogId`, `parentId`, `catalogName`, `date`, `access`, `owner`) VALUES
(1, NULL, 'main', '2020-04-19', 1, 'donald');

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE `Comments` (
  `commentId` int(11) NOT NULL,
  `comment` tinytext NOT NULL,
  `date` datetime NOT NULL,
  `username` varchar(45) DEFAULT NULL,
  `fileId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Triggers `Comments`
--
DELIMITER $$
CREATE TRIGGER `Comments_BEFORE_DELETE` BEFORE DELETE ON `Comments` FOR EACH ROW BEGIN
	INSERT INTO commentsAudit
    SET action = "delete",
    commentLogId = OLD.commentId,
    comment = OLD.comment,
    date = curdate(),
    time = curtime();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `commentsAudit`
--

CREATE TABLE `commentsAudit` (
  `commentLogId` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `date` datetime NOT NULL,
  `time` time NOT NULL,
  `action` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='		';

--
-- Dumping data for table `commentsAudit`
--

INSERT INTO `commentsAudit` (`commentLogId`, `Comment`, `date`, `time`, `action`) VALUES
(1, '', '2020-04-19 07:30:52', '00:00:00', 'delete'),
(2, '', '2020-04-19 07:32:09', '00:00:00', 'delete'),
(3, '', '2020-04-19 07:32:58', '00:00:00', 'delete'),
(4, '', '2020-04-19 07:34:19', '00:00:00', 'delete'),
(5, '', '2020-04-19 21:51:44', '00:00:00', 'delete'),
(6, '', '2020-04-19 21:51:50', '00:00:00', 'delete'),
(8, '', '2020-04-19 22:03:45', '00:00:00', 'delete'),
(9, '', '2020-04-20 23:20:23', '00:00:00', 'delete'),
(10, '', '2020-04-22 13:25:39', '00:00:00', 'delete'),
(11, 'test', '2020-04-24 00:00:00', '11:47:05', 'delete'),
(12, 'Test', '2020-04-24 00:00:00', '11:48:39', 'delete'),
(13, 'Ter', '2020-04-24 00:00:00', '12:10:35', 'delete'),
(14, 'Test', '2020-04-24 00:00:00', '12:11:19', 'delete'),
(16, 'asdasd', '2020-04-24 00:00:00', '12:11:32', 'delete'),
(17, 'rtyrty', '2020-04-24 00:00:00', '12:21:46', 'delete'),
(21, '123', '2020-05-04 00:00:00', '03:44:17', 'delete'),
(22, 'nice', '2020-05-05 00:00:00', '02:55:16', 'delete'),
(23, 'Test test test', '2020-05-04 00:00:00', '19:19:31', 'delete'),
(24, 'asd asd asd asd asd asd sad sd dsadasdasd ads asd øjfd ljsdg wpej weldfkj fnoiwsn swdf  sdf', '2020-05-04 00:00:00', '19:38:10', 'delete'),
(26, '22222222222', '2020-05-04 00:00:00', '22:39:23', 'delete'),
(31, 'nice', '2020-05-06 00:00:00', '15:51:51', 'delete'),
(32, 'new test\r\n', '2020-05-06 00:00:00', '16:35:01', 'delete'),
(33, 'hi', '2020-05-06 00:00:00', '21:39:25', 'delete'),
(34, 'ghjgjhgj', '2020-05-08 00:00:00', '02:21:19', 'delete'),
(36, 'looooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo', '2020-05-09 00:00:00', '02:23:12', 'delete');

-- --------------------------------------------------------

--
-- Stand-in structure for view `Elements`
-- (See below for the actual view)
--
CREATE TABLE `Elements` (
`id` int(11)
,`Title` varchar(45)
,`Date` date
,`Author` varchar(45)
,`Size` varchar(10)
,`Filename` varchar(60)
,`Access` tinyint(4)
,`Type` varchar(60)
,`Catalog` int(11)
,`Description` text
,`Data` mediumblob
,`Views` int(11)
,`isFile` bigint(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `Files`
--

CREATE TABLE `Files` (
  `fileId` int(11) NOT NULL,
  `filename` varchar(60) NOT NULL,
  `type` varchar(60) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `uploadedDate` date NOT NULL,
  `title` varchar(30) NOT NULL,
  `size` varchar(10) NOT NULL,
  `catalogId` int(11) DEFAULT 1,
  `owner` varchar(45) NOT NULL,
  `impressions` int(11) NOT NULL DEFAULT 0,
  `access` tinyint(4) NOT NULL DEFAULT 1,
  `data` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `FilesAndTags`
--

CREATE TABLE `FilesAndTags` (
  `fileId` int(11) NOT NULL,
  `tag` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `FilesWithTagsView`
-- (See below for the actual view)
--
CREATE TABLE `FilesWithTagsView` (
`id` int(11)
,`Title` varchar(30)
,`Date` date
,`Author` varchar(45)
,`Size` varchar(10)
,`Filename` varchar(60)
,`Access` tinyint(4)
,`Type` varchar(60)
,`Catalog` int(11)
,`isFile` int(1)
,`tag` varchar(45)
);

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE `Tags` (
  `tag` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Tags`
--

INSERT INTO `Tags` (`tag`) VALUES
('animal'),
('atom'),
('bab'),
('cpt'),
('fyrstikk'),
('h'),
('icon'),
('jpg'),
('pic'),
('picture'),
('tag1'),
('tag2'),
('tag3'),
('tag4'),
('tat'),
('tttt'),
('txt'),
('txt2');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `email` varchar(45) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(45) NOT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `verCode` varchar(60) DEFAULT NULL,
  `verified` tinyint(4) NOT NULL DEFAULT 0,
  `admin` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`email`, `password`, `username`, `firstname`, `lastname`, `date`, `verCode`, `verified`, `admin`) VALUES
('aka160@post.uit.no', '$2y$10$z6tce5QFGnooknXei9Ioxu0ONhR4XiGfNLLxOw1p0mHpV9.dUNy0S', 'donald', 'Abdullah', 'Karagoezzz', '2020-05-05', 'a338165c9c2c909a560a88e6950629f3', 1, 1),
('duongnvt94@gmail.com', '$2y$10$5bH16fT7XuKn6kuscKlQ..BqMaX15J07x/XEVAq5vBTCl/lYnrbhi', 'duong007', 'duong', 'nguyen', '2020-05-07', 'c91dbdb24da274a87d79400094541cbd', 0, 0),
('duongnvt94@hotmail.com', '$2y$10$m9MBb/MIa917Pns1WGAmXeO9EXm7ItGSt7Rtt9ZvMfqCwa0rbeHC2', 'duong123', 'Duong', 'Nguyen', '2020-05-06', '804d11ff979963a19a4568ec1fca6c63', 1, 1),
('tingyang0206@gmail.com', '$2y$10$iT1Rl2D7GfD8D21UcMD.2u1RQS2GuS01EeIDQgLw7ttgWa2HWmzsK', 'tya003', 'Tina', 'Yang', '2020-05-02', 'f87e66dfa28cef3407b2d355eec875dd', 1, 0),
('tya003@uit.no', '$2y$10$wFhcalbiLtZPbjYULiGnSu8S7EQ26uhU.NPnomEF9p/rwUqtl0t2G', 'tya007', 'Ting', 'Yang', '2020-05-05', '5a6ad9b4ff08e7b1b3ceba5685c85e1d', 1, 1);

-- --------------------------------------------------------

--
-- Structure for view `Elements`
--
DROP TABLE IF EXISTS `Elements`;

CREATE ALGORITHM=UNDEFINED DEFINER=`stud_v20_karagoz`@`%` SQL SECURITY DEFINER VIEW `Elements`  AS  select `Catalogs`.`catalogId` AS `id`,`Catalogs`.`catalogName` AS `Title`,`Catalogs`.`date` AS `Date`,`Catalogs`.`owner` AS `Author`,0 AS `Size`,'' AS `Filename`,`Catalogs`.`access` AS `Access`,'Catalog' AS `Type`,`Catalogs`.`parentId` AS `Catalog`,'' AS `Description`,'' AS `Data`,NULL AS `Views`,0 AS `isFile` from `Catalogs` union (select `Files`.`fileId` AS `id`,`Files`.`title` AS `Title`,`Files`.`uploadedDate` AS `Date`,`Files`.`owner` AS `Author`,`Files`.`size` AS `Size`,`Files`.`filename` AS `Filename`,`Files`.`access` AS `Access`,`Files`.`type` AS `Type`,`Files`.`catalogId` AS `Catalog`,`Files`.`description` AS `Description`,`Files`.`data` AS `Data`,`Files`.`impressions` AS `Views`,1 AS `isFile` from `Files`) order by `isFile`,`Title` ;

-- --------------------------------------------------------

--
-- Structure for view `FilesWithTagsView`
--
DROP TABLE IF EXISTS `FilesWithTagsView`;

CREATE ALGORITHM=UNDEFINED DEFINER=`stud_v20_karagoz`@`%` SQL SECURITY DEFINER VIEW `FilesWithTagsView`  AS  select `Files`.`fileId` AS `id`,`Files`.`title` AS `Title`,`Files`.`uploadedDate` AS `Date`,`Files`.`owner` AS `Author`,`Files`.`size` AS `Size`,`Files`.`filename` AS `Filename`,`Files`.`access` AS `Access`,`Files`.`type` AS `Type`,`Files`.`catalogId` AS `Catalog`,1 AS `isFile`,`FilesAndTags`.`tag` AS `tag` from (`Files` join `FilesAndTags` on(`FilesAndTags`.`fileId` = `Files`.`fileId`)) where 1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Action`
--
ALTER TABLE `Action`
  ADD PRIMARY KEY (`action`),
  ADD UNIQUE KEY `Action_UNIQUE` (`action`);

--
-- Indexes for table `Catalogs`
--
ALTER TABLE `Catalogs`
  ADD PRIMARY KEY (`catalogId`),
  ADD KEY `parentId_idx` (`parentId`),
  ADD KEY `fk_Catalogs_Users1_idx` (`owner`);

--
-- Indexes for table `Comments`
--
ALTER TABLE `Comments`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `fileId_idx` (`fileId`),
  ADD KEY `fk_Comments_Users_idx` (`username`);

--
-- Indexes for table `commentsAudit`
--
ALTER TABLE `commentsAudit`
  ADD PRIMARY KEY (`commentLogId`),
  ADD KEY `action_idx` (`action`);

--
-- Indexes for table `Files`
--
ALTER TABLE `Files`
  ADD PRIMARY KEY (`fileId`),
  ADD KEY `catalogId_idx` (`catalogId`),
  ADD KEY `userId_idx` (`owner`);

--
-- Indexes for table `FilesAndTags`
--
ALTER TABLE `FilesAndTags`
  ADD UNIQUE KEY `tag` (`tag`,`fileId`),
  ADD KEY `FileId_idx` (`fileId`),
  ADD KEY `tag_idx` (`tag`);

--
-- Indexes for table `Tags`
--
ALTER TABLE `Tags`
  ADD PRIMARY KEY (`tag`),
  ADD UNIQUE KEY `tag` (`tag`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Catalogs`
--
ALTER TABLE `Catalogs`
  MODIFY `catalogId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `Comments`
--
ALTER TABLE `Comments`
  MODIFY `commentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `Files`
--
ALTER TABLE `Files`
  MODIFY `fileId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Catalogs`
--
ALTER TABLE `Catalogs`
  ADD CONSTRAINT `fk_Catalogs_Catalogs` FOREIGN KEY (`parentId`) REFERENCES `Catalogs` (`catalogId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Catalogs_Users` FOREIGN KEY (`owner`) REFERENCES `Users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Comments`
--
ALTER TABLE `Comments`
  ADD CONSTRAINT `fk_Comments_Files` FOREIGN KEY (`fileId`) REFERENCES `Files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Comments_Users` FOREIGN KEY (`username`) REFERENCES `Users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `commentsAudit`
--
ALTER TABLE `commentsAudit`
  ADD CONSTRAINT `fk_commentsAudit_Action` FOREIGN KEY (`action`) REFERENCES `Action` (`action`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `Files`
--
ALTER TABLE `Files`
  ADD CONSTRAINT `fk_Files_Catalogs` FOREIGN KEY (`catalogId`) REFERENCES `Catalogs` (`catalogId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Files_Users` FOREIGN KEY (`owner`) REFERENCES `Users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `FilesAndTags`
--
ALTER TABLE `FilesAndTags`
  ADD CONSTRAINT `fk_FilesAndTags_Files` FOREIGN KEY (`fileId`) REFERENCES `Files` (`fileId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_FilesAndTags_Tags` FOREIGN KEY (`tag`) REFERENCES `Tags` (`tag`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
