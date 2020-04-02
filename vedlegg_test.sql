-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `vedlegg_test`
--

CREATE TABLE `vedlegg_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `size` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `dato` date NOT NULL DEFAULT '0000-00-00',
  `mimetype` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `filnavn` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `kode` mediumblob NOT NULL,
  PRIMARY KEY (`id`))
ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;