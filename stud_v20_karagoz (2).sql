-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 01, 2020 at 03:00 PM
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
-- Table structure for table `CatalogRelation`
--

CREATE TABLE `CatalogRelation` (
  `depth` int(11) NOT NULL,
  `Parent` int(11) DEFAULT NULL,
  `Son` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Catalogs`
--

CREATE TABLE `Catalogs` (
  `catalogId` int(11) NOT NULL,
  `parentId` int(11) DEFAULT NULL,
  `catalogName` varchar(45) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `access` tinyint(4) NOT NULL DEFAULT 1,
  `owner` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='	';

--
-- Dumping data for table `Catalogs`
--

INSERT INTO `Catalogs` (`catalogId`, `parentId`, `catalogName`, `date`, `access`, `owner`) VALUES
(1, NULL, 'main', '2020-04-19 07:14:39', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE `Comments` (
  `commentId` int(11) NOT NULL,
  `comment` text NOT NULL,
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
(17, 'rtyrty', '2020-04-24 00:00:00', '12:21:46', 'delete');

-- --------------------------------------------------------

--
-- Stand-in structure for view `Elements`
-- (See below for the actual view)
--
CREATE TABLE `Elements` (
`id` int(11)
,`Title` varchar(45)
,`Date` datetime
,`Author` varchar(45)
,`Size` varchar(10)
,`Filename` varchar(60)
,`Access` tinyint(4)
,`Type` varchar(30)
,`Catalog` int(11)
,`Description` text
,`Data` mediumblob
,`isFile` bigint(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `Files`
--

CREATE TABLE `Files` (
  `fileId` int(11) NOT NULL,
  `filename` varchar(60) NOT NULL,
  `type` varchar(30) DEFAULT NULL,
  `description` tinytext DEFAULT NULL,
  `uploadedDate` datetime NOT NULL,
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
  `fileId` int(11) DEFAULT NULL,
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
,`Date` datetime
,`Author` varchar(45)
,`Size` varchar(10)
,`Filename` varchar(60)
,`Access` tinyint(4)
,`Type` varchar(30)
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
('bab'),
('h'),
('picture'),
('tag1'),
('tag2'),
('tag3'),
('tag4'),
('tat'),
('tttt');

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
('aka160@post.uit.no', '$2y$10$df169wMYCIdHZiiTruBP.uzArMMMRpOx9t7xrc19HUmloG2bb3tK6', 'donald', 'myfirstname', 'mylastname', '2020-05-01', '3920302d01f002ee5bc41830b29d3686', 1, 1),
('duongnvt94@hotmail.com', '$2y$10$lGGxPjN2.7UWaxH6gOeKmuqwmMutMEqBZFqKoup5vbvH2MMqsejom', 'Duong', 'Duong', 'Nguyen', '2020-04-29', '301df828be4af426645e4bf2500665b0', 1, 0),
('tya003@uit.no', '$2y$10$n8HzWj7.lp9AsXl6XqWDMeC0ZdEebhE.3IS9TTRg6tx8jf7fN1thK', 'tya003', 'Ting', 'Yang', '2020-04-29', '2f954b0d2f481980ec47ac751696188e', 1, 0);

-- --------------------------------------------------------

--
-- Structure for view `Elements`
--
DROP TABLE IF EXISTS `Elements`;

CREATE ALGORITHM=UNDEFINED DEFINER=`stud_v20_karagoz`@`%` SQL SECURITY DEFINER VIEW `Elements`  AS  select `Catalogs`.`catalogId` AS `id`,`Catalogs`.`catalogName` AS `Title`,`Catalogs`.`date` AS `Date`,`Catalogs`.`owner` AS `Author`,0 AS `Size`,'' AS `Filename`,`Catalogs`.`access` AS `Access`,'Catalog' AS `Type`,`Catalogs`.`parentId` AS `Catalog`,'' AS `Description`,'' AS `Data`,0 AS `isFile` from `Catalogs` union (select `Files`.`fileId` AS `id`,`Files`.`title` AS `Title`,`Files`.`uploadedDate` AS `Date`,`Files`.`owner` AS `Author`,`Files`.`size` AS `Size`,`Files`.`filename` AS `Filename`,`Files`.`access` AS `Access`,`Files`.`type` AS `Type`,`Files`.`catalogId` AS `Catalog`,`Files`.`description` AS `Description`,`Files`.`data` AS `Data`,1 AS `isFile` from `Files`) order by `isFile`,`Title` ;

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
-- Indexes for table `CatalogRelation`
--
ALTER TABLE `CatalogRelation`
  ADD KEY `fk_CatalogueRelation_Catalogues1_idx` (`Parent`),
  ADD KEY `fk_CatalogueRelation_Catalogues2_idx` (`Son`);

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
  MODIFY `catalogId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `Comments`
--
ALTER TABLE `Comments`
  MODIFY `commentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `Files`
--
ALTER TABLE `Files`
  MODIFY `fileId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `CatalogRelation`
--
ALTER TABLE `CatalogRelation`
  ADD CONSTRAINT `fk_CatalogRelation_Catalogs_Parent` FOREIGN KEY (`Parent`) REFERENCES `Catalogs` (`catalogId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_CatalogRelation_Catalogs_Son` FOREIGN KEY (`Son`) REFERENCES `Catalogs` (`catalogId`) ON DELETE CASCADE ON UPDATE CASCADE;

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
