-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 08, 2020 at 02:52 AM
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
-- Table structure for table `Catalogs`
--

CREATE TABLE `Catalogs` (
  `catalogId` int(11) NOT NULL,
  `parentId` int(11) DEFAULT NULL,
  `catalogName` varchar(45) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `access` tinyint(4) NOT NULL DEFAULT 1,
  `owner` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='	';

--
-- Dumping data for table `Catalogs`
--

INSERT INTO `Catalogs` (`catalogId`, `parentId`, `catalogName`, `date`, `access`, `owner`) VALUES
(1, NULL, 'main', '2020-04-19', 1, NULL),
(35, 1, 'Test', '2020-05-08', 1, 'donald');

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
(17, 'rtyrty', '2020-04-24 00:00:00', '12:21:46', 'delete'),
(21, '123', '2020-05-04 00:00:00', '03:44:17', 'delete'),
(22, 'nice', '2020-05-05 00:00:00', '02:55:16', 'delete'),
(23, 'Test test test', '2020-05-04 00:00:00', '19:19:31', 'delete'),
(24, 'asd asd asd asd asd asd sad sd dsadasdasd ads asd øjfd ljsdg wpej weldfkj fnoiwsn swdf  sdf', '2020-05-04 00:00:00', '19:38:10', 'delete'),
(26, '22222222222', '2020-05-04 00:00:00', '22:39:23', 'delete'),
(31, 'nice', '2020-05-06 00:00:00', '15:51:51', 'delete'),
(32, 'new test\r\n', '2020-05-06 00:00:00', '16:35:01', 'delete'),
(33, 'hi', '2020-05-06 00:00:00', '21:39:25', 'delete'),
(34, 'ghjgjhgj', '2020-05-08 00:00:00', '02:21:19', 'delete');

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

--
-- Dumping data for table `Files`
--

INSERT INTO `Files` (`fileId`, `filename`, `type`, `description`, `uploadedDate`, `title`, `size`, `catalogId`, `owner`, `impressions`, `access`, `data`) VALUES
(54, 'image007.jpg', 'image/jpeg', 'loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo', '2020-05-08', 'Atom', '7666', 1, 'donald', 2, 0, 0xffd8ffe000104a46494600010101006000600000fffe001c536f6674776172653a204d6963726f736f6674204f6666696365ffdb0043000a07070807060a0808080b0a0a0b0e18100e0d0d0e1d15161118231f2524221f2221262b372f26293429212230413134393b3e3e3e252e4449433c48373d3e3bffdb0043010a0b0b0e0d0e1c10101c3b2822283b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3b3bffc000110800c300f303012200021101031101ffc4001b00010100020301000000000000000000000006050702030408ffc4004c10000103030300030b0a0108090500000001020304000511061221071331141617224151576695a5d115233246477686c2c4e261354256718194c1d22433345255629193d3253644a1d4ffc40014010100000000000000000000000000000000ffc40014110100000000000000000000000000000000ffdd00040028ffda000c03010002110311003f00acbfc9d4b70e9151a7ecda87e488e9b4098a3dc4dbfb95d7141fa5c8e08f2f93b39aefef635dfa45f72b1f1a7db7fe1bfd4d59d04677b1aefd22fb958f8d3bd8d77e917dcac7c6ace94119dec6bbf48bee563e34ef635dfa45f72b1f1ab3a504677b1aefd22fb958f8d3bd8d77e917dcac7c6ace94119dec6bbf48bee563e34ef635dfa45f72b1f1ab3a504677b1aefd22fb958f8d3bd8d77e917dcac7c6ace94119dec6bbf48bee563e34ef635dfa45f72b1f1ab3a504677b1aefd22fb958f8d3bd8d77e917dcac7c6ace94119dec6bbf48bee563e34ef635dfa45f72b1f1ab3a504677b1aefd22fb958f8d3bd8d77e917dcac7c6ace94119dec6bbf48bee563e34ef635dfa45f72b1f1ab3a504677b1aefd22fb958f8d3bd8d77e917dcac7c6ace94119dec6bbf48bee563e34ef635dfa45f72b1f1ab3a504677b1aefd22fb958f8d3bd8d77e917dcac7c6ace94119dec6bbf48bee563e358bbb8d69a627589e95acbe528f3aef1e1bac7c98cb394ac927c6193d89c718ededad8f519d23fd55fbc90ff3d059d294a08cfb6ffc37fa9ab3a8cfb6ff00c37fa9ab3a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a0546748ff557ef243fcf567519d23fd55fbc90ff003d059d294a08cfb6ff00c37fa9ab3a8cfb6ffc37fa9ab3a05294a05295a7fa6fd5d72b5ceb6d9ed57099057d52a4bea8ebeaf78276a06e07771b5791d9c8edc701b829505d0f6a376ffa37aa99256fcd82fadb756f3e5c75c4a8ef4ad59e40f18a4673f40ff50bda05294a05294a05294a05294a0fffd0dcd4a52814a56b5e93ef0ee9fd5da3eecd465c811552d6eb68415abaad880e103239082a392703193c66836552b4fd8f5eca4f7f7aae44778756d44ee18ef20f8a8575818ca4ab849de95ab69c1dca23b6bcba6842bdf4577fd2635046eaa02995b5707dbea63a43852e046558563ad4b89dca19f18100fd101baa95a97e498684ea0d3d7516ed25749505975d9f6f90e086fb05d0924345494a3180d9c90495a88e09ce6b435bc69fd4d22d1334e336c9ee414b825dbe43aa872db414a7842cf0b4ee482482a24a8923778c1b0294a50294a502a33a47faabf7921fe7ab3a8ce91feaafde487f9e82ce94a50467db7fe1bfd4d59d467db7fe1bfd4d59d0294a502be726ef936edd2bccd596ed3ef5fe3c5924a1a8cc2ca4a424b6cacf8aa29561295723b52718f27d0d363776419117af798ebda537d6b0bdae37918dc93e450ce41f3d4fe8dd056ad11dd9f264898f7766ceb3ba56956366ec636a47fbc7ff00aa0d53d12dd95a6ba4095659ccc9b735724f5698d2c00b4383c6682ca824e769501803715a78e463785c6cd6abbf57f29db21ceeab3d5f74b097366719c6e0719c0ffa0a9fba746d63baeaf635438e4c6a732eb4e94b4e27ab714d91b4a81493d8948382381e7c9aada0c7c0d3f65b53ea7edd688309d52761723c6436a29c838ca40e32071fc2bcbde6695fe8d5a3fb8b5fe5a89e99a26a187098d4365bc4e8b1a3a43531962629a48055e22c2463272a292739fa3c60135a73bf3d55fd25bbff007e77fcd41f51cfd3f65babe97ee3688335d4a76072446438a09c938ca81e324f1fc68de9fb2b56e72dcdda20a213cadee464c640696ae3929c609f1473fc079aa33a22d70ad4d6455aee0f2dcba5bd23738eac15486c938579c94f095139fe69249556c3a0c5c3d3360b7ca44a8563b74590de763acc46d0b4e460e08191c123fb6b83da4f4dc97dc7dfd3d6b75d75456e38b84da94b513924923924f96b48748fd25dcae9a996c586e93214085b994aa349da242813b9cca0f293800727819e3711525df9eaafe92ddffbf3bfe6a0fa8e569fb2ce623b132d106435153b186dd8c85a5a4e00c2411e28c01c0f30aeb6b4cd8188afc566c76e6e3c9dbd734888d843bb4e53b8630707919ecaf1e88b3dcacba65862f171993e7bdf3d21529eeb0b4a2065b49c9f1538c769c9c9f2e050506219d27a6e33edbec69eb5b4eb4a0b6dc4426d2a4281c82081c107cb597a5281531a9ef163b7df6cf0af76e65f4ce6a525b94eb2970470129de159076a14851dcaec0078de2e48a7a8fd5fa595a935569c5c9808976a8c99899bbd400485b60238c839dc3829e4100f1db41d173b9596d3399691a620ad5f2f43b6256108494aba84adb74789da80bda91e40382338aefd5f77b2dafe559775b0469e9851221796b6d0b5b8d3b2149d9e30e424b7bc02704e3b3b6b0b2347ea366c4a2b2bbadc2dd7e8d70637bc80a9e869a69be5671b090147c6c91b7195e7716a4b26aad4f63d52b72cc88aecd4c48d6f8864365d536cbc5656b5056c04f5878cf18c73805419d7e2e8e8770b4dba3586d0efcbfbd282d456f638ca11d69564248527725bc0cf9411d953fa3f55db8396fb99d0b0ec106eeef71c5b8c55b4b2e3a54406d494a52b0925079c6320678e473d37d1fdd2cfa9a1dc26bcb9a8b6cb54582e2dc054d40ea5e29c9c8dc4add4271b729d9c78b589d3fa22fb199d28c3960798976b9ca7a5cc97390eb0db5bd4a2969adeb014a053e304a485241cff003805cdab525f6e77c723f7afd55a5125f63e52eef42b3d52968cf558ddca918c79339e4563f4a6bfb8ea25dbde95a65702df73538d459899a87429c405a8a4a3014061b5f38ed03cf9acbe9f5dc20a9db749b2cc42153a5ba25f58c16b6ade71c49c0737f21407d1ed3fdb51fa2f415decafe959f3185f590d32da991de965698a565650e3490ada0a8612a0091e303b739500da14a5281519d23fd55fbc90ff003d59d46748ff00557ef243fcf41674a528233edbff000dfea6acea33edbff0dfea6ace814a52814a52814a5283ade65a92c38c3ed21d69d49438dad21495a48c1041ed04792be56d6fa55fd1fa99fb63be332af9d8ab2b0a2b6492124e00f1b820f039071c609fab6a4ba47d1a8d63a656c34313e26e7a228253952b07e6c938c255c0ed1c84939c6283e70b0dee6e9cbdc6bbdbd48126328a93bd3b92a0410411e62091c73cf041e6b766bee93e12741c67ec1356dcdbc27e670763cc36090b511838394940391c92524edcd6867997633ee30fb4b69d69450e36b494a90a0704107b083e4ae140adb3d09e8beed9cbd4d7289ba347f160f5a8f156ee79713cff0033181c119570729ad79a5f4e4dd557f8d6a848592ea8175c4a77065bc8dcb3c8e003e7193803922bea9b3da62d8acf16d7091b63c568368c800ab1daa38001513924e39249a0f6d294a05294a0562ef3a8ed3a7fb985ce5f54b98ef551da436b71c755e64a100a8f681d9da40f28aca5466bf559a34ab3dc6ece5de0184eb8e3575b7b7b91138482977850dab25290369ce31d99c8666d5ac34f5efb93e4eba32f99bd6f509c14a965bdbbc60804280524e0f241c818e6b9b5aa2cef5c63dbd1297dd325f7d8650a61c48716cffad0094e084f6673824100920d405fef570bb744336ef31b58996b968ee09e58533dd29dc94225210a1e29521d56319c2bc64e081b5d2140b85a2e5a22269d52d72ad6c493192b2a2a792cb4da8a0ecc151525053818ceec719a0bfb8ea9b25a513d7367a1b16d4b4a97b52a59643848464241e491d9db8209e083481aa2cf715cf433296daada94ae626530e472c2540a81575894e060139f3569353d7097a475fcdb9ba87254d4dae5a8a14a294a5d5871291bb9012952538f2631938accb0c3baa5cbbce5bd2758c54c48ad393998861ab622525d76321af177a8a06ec8391803f9c28363db3a41d2b78626bd02ee8753018322402d389525b0092a0952415018e700f68f38ace4298c5c20b1362b9d64792d25d6978237254320e0f2383e5ad59a9eed0f58cc76eda6cbce43b7d92e289f3bb9dc69b7829ac218dc5237292a217b4f001c8e6a83416bad353ad363d3f1ae5bee6982d3458ea1c1e321a0543714ede369f2f9282d9e79a8cc38fbeea1a69a495b8e2d41294240c9249ec00796b0768d75a6afb399856fb975922434a759438c38d75a904825056901582957667e8abcc71ecd4d0dfb8695bb428adf59224c179a69190372948200c9e0727cb53306fb66d4d7fb0c7b1c0783b6675c54a4ae2753f2720b0b6fa95640c28aca46d4e47cd93d89a0a0b46adb45fba955b1731f69fddd5bfdc0fa5a56339f9c52027c8476f6f1db586e91feaafde487f9ebc5d0c459ed6848d2245c7af88fefee68bd4253dcd875c0bf1c72bdc79e7b3b2bdbd23fd55fbc90ff003d059d294a0fffd1b3fb6ffc37fa9ab3a8cfb6ff00c37fa9ab3a05294a05294a05294a052953fadf5531a3f4cbf7377c6795f35150505416f104a41c11e2f049e4700e39c021a3fa6666d6c7480f22dad21b70b085cc4a12402fab2a27cd92928248e09273ce6a0eb9bcf3b25f71f7dd5baebaa2b71c5a8a94b51392493da49f2d70a0ddfd0244b47c9d7298cb8b72ebb92dbe95b40065b392908579428824f23948e38055b5a64b6e0c55c9792f2908c6432cadd5f271c2500a8f6f9057cada3352bba4f5443bb20acb4856c90da73f38d1e143191938e40271b824f92bea985318b841626c573ac8f25a4bad2f046e4a8641c1e4707cb41e26b50427a2bf252c5c4218dbbc2ed9252b3b8e06d4946e5ff1da0e3cb8ae0cea580fbedb288f740a714120aed32909049c72a536001fc49c0acbd28317df042eeeee3ea2e3d6f5bd56ef9324f579ce33d66cdbb7fe6ce31ce71594a5281539a92f17a8b7bb2d9ac71a329db8a9d5bd264a16b44769b09dc4a524724ac004903381fcec8a3a95d651ade66d9ae2eea08363b8407d6e467e614a92e20a76badec52d390414e48e479319cd0157ebbd90c7ef9ddb5c769eba2e3094825a6951fb9d6e217e32ced515a76904f9c0cf0a38c735fbafcbd4620cbb59856c7ededc79a72e3412fa929716b2160109c9ec29c639af6d8e04743567549d628bcca72e0fcd69e5b882249ea9c6d486521476a5217921390083c0cf1e5bc5b2d56dbe5ea54ad556eb7cbbbbb064b4d4cda3aaee65020905c495a54518e36e39eda0f55e353dc2c5a2e66a413ed77a69a5345930d953485a4ba10b1bbac5e4e09008ec23907b2bdb075842baead8f66b73887da5dacdc1c700e40529bea876e524a545442803828fe347203ba8adab8171bb5aee0d38a8b28262c4212a683a1cf1829d5ee4ac36520f03b7e97656234fe99b068771bbabba859523e7e22a4cd75b495e54d8435d6647fab4c729dbe7dd809fa341df0750ea7be4ab4c7b6b36e8ed3d68627cf96fb6b70214e856d436d85a4f6a15c951e3cb903775ea3d57a874c68345c275be3397d5bea68351d0a718c24ad4578ddbb6f52d95673907b40e71c2df648aa7ac572d39aa2dc64c083f2438f7562437302501411b52e0daa1b54bc24e70793815ce5d8ad6cbf6f99a9354a1e16a4bac97dd9262bbdd4f14b8a3d621c4ed1b384b60709576918a0f6bbab9d76f9a45884ca042d40c3efacbc93d6a129652e200c1c03e373dbfc2aaab5e59b46b4b5db66e9cd5306446b2cb9861a131c4869943e13f324a5d0494e49c9393b879055e4344a4454266bccbd20677b8cb45a41e78c24a944718f29ff0a0efa8ce91feaafde487f9eacea33a47faabf7921fe7a0b3a5294119f6dff86ff53567519f6dff0086ff005356740a52940a52940a52940af99ba4ed6ddf8ea2ff00455eeb5c2ca21e5bdaa5671bd673cf24719c700700e6b6674cdadbe44b3f7bf09789d7168f5c4b79088eadc938278dca208f2e005761da6b40507a6db6d9b78b8b16fb7c75c895215b5b6d1da4ff008003924f00024d6f0d57d13b08e8e235bed27ad9f69eb24073aa3be5150cb89da9fe71c276e428f8894e7926b1fd06e8f53687355cd6d043a92d40c90a206487178c78a7236839071bf23041adc741f19d6dce8535c261be74adc5e42187945709c716787091968678015c91d9e36472542b1fd33689f912f1df0424620dc5d3d702e64a242b728e01e76a8027cb8215d8368ad6ccbcec67db7d8756d3ad282db710a2952140e410476107cb41f64d2a63416b26359e9d6e6e596e735e24c8eda8fcdab9c1c1e76a80c8edf28c920d53d0294a502b5974abff00b9f49ff247ff0033f967fd93e823e9ff0087fcd8ad9b5e2b8d9ad577eafe53b6439dd567abee9612e6cce338dc0e3381ff0041410b7b9b60b7eb1d0b2a149b745b437f286c75971b4474e5b00e08f1478c48feb3e7ac66a59b6ab874af6e9464e9c956c72d08eb1dba3895b2a4092a0bea8fd12ef040f270acd6c6774cd81f8ac457ac76e723c6ddd4b4b88d9435b8e55b4630327938edae0e693d36ea1b439a7ad6b4b29d8da5509b2109c956071c0ca89c79c9f3d041eacbfcad27a98dfec8db2fda1ab2311d4d320a9a1bcc831d602484ec0a404e73c25784f2aaf296a6b1d1ae9b66e26d666b779929906fabdf18b83bac2bac564e4e7383939562b69a6d96f421284418c94a12da1290d240096cee6c0e3b127948f21ecae12acd6a9d1445976c87223874bc1a7584ad1bc924ab0463712a5127b793e7a085badb215df4ed82269f7ed11e5fca41d2eda36773226b711d701e01f177a519c8276e2ba50f2351e85d712ae96e67ba23c9923ab75b4abab79988db7bd232a09565248c28e3380a3da76043b35aadeda1b856c87150dba5e4259612809594ed2a000e15b4919edc1c5772e14571b90dae332a44acf7424b6087b29093b87f3bc50073e4005061b42428b0f43d9bb96332c75f05879dea9b09eb165b4e54ac76a8e39279aa0aeb6596a330db0c34869a69210db68484a5090300003b001e4aeca0546748ff00557ef243fcf567519d23fd55fbc90ff3d059d294a08cfb6ffc37fa9ab3a8cfb6ff00c37fa9ab3a05294a0568fd71d1ae89d1b607663d74ba198ea54986c175a5175cc7191b078a090547238fe2403b9ae73dab55aa5dc5f4ad4d44616fb894005452949510338e702be60bdea56b57eb2377bf992c42714125a898716d3491c211bc8193e53c0ca94ac79282bfa16d1b72937d63553a3a88113ac4b4569e6428a548213ff28c9cabce3033ceddf55aff0047f4a1a62ef70b7e99b3c0b8c7f9aeaa387908d884b6824027793f4538f2d59dc5dbab5d5fc990a1c9ce7acee996a676f6631b5b5e7cbe6c71dbe4092d47d11d8353df64de66ccb8b7224edde965c6c206d4848c02827b123cb58cf00ba57fe2177ffbcd7fe3abd80f5e9c7d42e36f831dadb90b8f356f28ab238c29a4718cf39fecaf2f756aaff835a3daaeff00f9e832ccb2d4661b618690d34d2421b6d09094a1206000076003c95d958f9ef5e9b7d22dd6f8321adb92b91356ca82b278c25a5f18c739feca36f5e8db9c5b96f8299a1586d94cd596949e392e754083dbc6d3d839e7805fac90b51d924da2e09598d25212ad8adaa49041041f38201e78e390471505e0174aff00c42eff00f79aff00c75730e45fd729099b6cb7331ce77b8cdc1c75638e3092ca41e71e51fe15c1e93a912fb818b4dad6d0510dad773712a5273c120307071e4c9feb34189d21d1cdab454e7e55b275c5cee86bab71a90ea4b679042b0948f187201f2051f3d56d63e53d7a4311cc3b7c179d5272fa1d9ab6d2dab038490d2b70ce79213d838e78eb6a45fcc57d4f5b2dc8909dbd4b68b838a42f9f1b72ba905381d980acff000eda0fffd2dcd4ac433275229f6c3f69b5a1a2a01c5a2e6e29494e792016064e3c991fd62b2f40a9fd6da957a5b4eae6c68fdd539f7531e147daa575af2fb0612327804e38ce319048aa0a95bf5a4dd7595a44fba223406587571a2b13571e448938c28e1241294b64f29208dc73c12280fea2b84fd2b69bf599f82ca66aa321d6de69520254f38db64052568e5054ac8239231e2d74dd2fba91bbcc7d336816b7ef1dc8a9d224cb6dc6a3f55d66c094a12a52b7648ce4e38fe381d10ac6d58ec6e587e578298cddf985406dc7420b48eb9a90239ce4973e9e0124a8149e01c0f6ea2b74845f537db1ceb5b178662261b8ddc779438db8ea7abced50283b92b09383b8923c9c0788748298b6ab7cab908cd389bcaad1742d93d5b2e252b1bd2a5edc27210ac9ce12a23922bcda83a4595059d5aab6b30ddef7fb910dad6a2e25c5babc39bb6918dbf4719c829393e41ceefd1e459b626acf2a6c6eb67dd244e90f614df58fb8d3db4b6def392825076eec14b649f2e7051f4945bae96ba58acda86d13ee7708315521c4cd0eadc7d1216ebce2ca41514fce25295104f001f3d0555caedace55def2c69e856bee6b5a50db6661529729f284b85230a48400958e4f19c738276d4c275f7e0b0f4a8ddcb21c692a758de17d5288c94ee1c1c1e323b7150d736e53fa9ee3174c6adb421dbce1a9d15f92552222d08ead4b610850f1f60c90ac105b1ce3b2a6c777b2c9619b75bafd1ae8ec66120a84b43cf2d29013bd7b4f249c64f9cd065e94a502a33a47faabf7921fe7ab3a8ce91feaafde487f9e82ce94a50467db7fe1bfd4d59d467db7fe1bfd4d59d0294a5075bccb52587187da43ad3a92871b5a4292b49182083da08f25627bccd2bfd1ab47f716bfcb59aa5062e1e99b05be522542b1dba2c86f3b1d6623685a72307040c8e091fdb594a52814a52814a52814a52814a52814a52815af3a448b709bad348b16a9488b39499e63bcbddb50b0d248ced238c8fe23ce950ca4ec3ae8761457e53129e8ccb92236eea5d5b60adadc30ada7b4647071db41aa6e17455da147bdbf11705b6f58417a621d5822204c6652beb1438480ae0eec107821272073e906f16dbcd9358bb6c9accc698836c696e32adc8dddd2e2b014383c287667cdda0d6d0916cb7cb61f624c18cfb525416fb6e349525d500002a0478c404a793fee8f3574b7a7ecad5b9cb7376882884f2b7b91931901a5ab8e4a71827c51cff01e6a0d6b63bcde7536b7d3722e50fb9cd8dd7edd316b192ecc530f75852a4f88520329240c6d2bc72083533a563ca856fd1535e4da2247957709664446c8b8bde3ad2a4b872016893b558270149e0f61decd5b2dec2cad98319b517d520a90d2412ea814a9ce07d2209055da4135e587a66c16f948950ac76e8b21bcec75988da169c8c1c103238247f6d060345d9bff0050badd25db2d1fca52fb8a5b4c7fa5ff00b43c973ac591fd4060fd1e0d41745ed3addc74b26642b5c661f4cb910a633189952968eb10b69c5e78002cab918c252339ecdc0d69fb2b17137166d105b9a54a599288c80e952b3b8ee033939393e5c9aee8f6cb7c4618623418cc3519456c36db494a5a51041290078a4852b91fef1f3d07aa94a502a33a47faabf7921fe7ab3a8ce91feaafde487f9e82ce94a50467db7fe1bfd4d59d467db7fe1bfd4d59d0294a50294a50294a50294a50294a50294a50294a50294a50294a50294a50294a50294a50294a502a33a47faabf7921fe7ab3a8ce91feaafde487f9e82ce94a507fffd3b3fb6ffc37fa9ab3a8cfb6ff00c37fa9ab3a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a05294a0546748ff557ef243fcf567519d23fd55fbc90ff003d059d294a08cfb6ff00c37fa9ab3ad7f7f9d2ac3d29a2f3f215dee51176411b75be217b6acbe5582781d83cf9e457b7c23fa95abbd97fba82ce9519e11fd4ad5decbfdd4f08fea56aef65feea0b3a5467847f52b577b2ff00753c23fa95abbd97fba82ce9519e11fd4ad5decbfdd4f08fea56aef65feea0b3a5467847f52b577b2ff753c23fa95abbd97fba82ce9519e11fd4ad5decbfdd4f08fea56aef65feea0b3a5467847f52b577b2ff00753c23fa95abbd97fba82ce9519e11fd4ad5decbfdd4f08fea56aef65feea0b3a5467847f52b577b2ff753c23fa95abbd97fba82ce9519e11fd4ad5decbfdd4f08fea56aef65feea0b3a5467847f52b577b2ff00753c23fa95abbd97fba82ce9519e11fd4ad5decbfdd4f08fea56aef65feea0b3a5467847f52b577b2ff753c23fa95abbd97fba82ce9519e11fd4ad5decbfdd4f08fea56aef65feea0b3a8ce91feaafde487f9e9e11fd4ad5decbfdd585d41a8256aa9da762c5d29a8e2f735ee34975d996f286d284920924138fa59e78c0341b3694a50294a50294a50294a50294a50294a50294a50294a50294a507ffd4dcd4a52814a52814a52814a52814a52814a52814a52814a5283fffd9);

-- --------------------------------------------------------

--
-- Table structure for table `FilesAndTags`
--

CREATE TABLE `FilesAndTags` (
  `fileId` int(11) NOT NULL,
  `tag` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `FilesAndTags`
--

INSERT INTO `FilesAndTags` (`fileId`, `tag`) VALUES
(54, 'atom');

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
('abd.karagoz@gmail.com', '$2y$10$/8XJRqsc1GObwUwwtHyivOP24fyzZE5j5B7uAB.3JoWS2KymTVj0u', 'donald2', 'Abdullah', 'Karagoez', '2020-05-08', '642e7798161369468589e5032ae1bd1c', 1, 0),
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
  MODIFY `commentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
