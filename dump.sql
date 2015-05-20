DROP TABLE IF EXISTS `s_banners`;
CREATE TABLE `s_banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `image` varchar(50) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `s_banners_categories`;
CREATE TABLE `s_banners_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mnemonic` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sorted` enum('DESC','ASC','RND') NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `limited` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
