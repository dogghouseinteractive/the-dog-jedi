CREATE DATABASE IF NOT EXISTS `phpcalendar` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `phpcalendar`;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `color` varchar(10) NOT NULL,
  `datestart` datetime NOT NULL,
  `dateend` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `submit_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;