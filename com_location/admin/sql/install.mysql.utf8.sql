CREATE TABLE IF NOT EXISTS `#__location_geolocations` (
	`id`        INT(11)      NOT NULL AUTO_INCREMENT,
	`region_id` INT(11)      NOT NULL DEFAULT '0',
	`state`     TINYINT(3)   NOT NULL DEFAULT '0',
	`country`   VARCHAR(255) NOT NULL DEFAULT '',
	`district`  VARCHAR(255) NOT NULL DEFAULT '',
	`region`    VARCHAR(255) NOT NULL DEFAULT '',
	`city`      VARCHAR(255) NOT NULL DEFAULT '',
	`latitude`  DOUBLE(20, 6),
	`longitude` DOUBLE(20, 6),
	`created`   DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00',
	UNIQUE KEY `id` (`id`)
)
	ENGINE = MyISAM
	DEFAULT CHARSET = utf8
	AUTO_INCREMENT = 0;