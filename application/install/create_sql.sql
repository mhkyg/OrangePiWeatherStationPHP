CREATE TABLE types (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
  `type_name` VARCHAR(30),
  `unit` VARCHAR(10),
  PRIMARY KEY (`id`) 
  
)ENGINE=MyISAM DEFAULT CHARSET=utf8; 

CREATE TABLE weather_data (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT, 
  `timestamp` int(11) NOT NULL, 
  `value` decimal(6,2) DEFAULT NULL, 
  `type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8; 