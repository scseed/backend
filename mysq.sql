CREATE TABLE IF NOT EXISTS `user_data` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(120) NOT NULL,
  `first_name` varchar(120) NOT NULL,
  `patronymic` varchar(120) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company` varchar(120) DEFAULT NULL,
  `position` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `users`
  ADD COLUMN `user_data_id` smallint(5) unsigned default NULL AFTER `id`,
  ADD COLUMN `is_active` tinyint(1) unsigned default 1,
  ADD CONSTRAINT `user_data` FOREIGN KEY (`user_data_id`) REFERENCES `user_data` (`id`) ON UPDATE CASCADE;