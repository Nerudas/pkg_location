CREATE TABLE IF NOT EXISTS `#__location_regions` (
	`id`           INT(11)      NOT NULL AUTO_INCREMENT,
	`name`         VARCHAR(255) NOT NULL DEFAULT '',
	`parent_id`    INT(11)      NOT NULL DEFAULT '0',
	`lft`          INT(11)      NOT NULL DEFAULT '0',
	`rgt`          INT(11)      NOT NULL DEFAULT '0',
	`level`        INT(10)      NOT NULL DEFAULT '0',
	`path`         VARCHAR(400) NOT NULL DEFAULT '',
	`alias`        VARCHAR(400) NOT NULL DEFAULT '',
	`abbreviation` VARCHAR(255) NOT NULL DEFAULT '',
	`icon`         TEXT         NOT NULL DEFAULT '',
	`default`      TINYINT(3)   NOT NULL DEFAULT '0',
	`show_all`     TINYINT(3)   NOT NULL DEFAULT '0',
	`state`        TINYINT(3)   NOT NULL DEFAULT '0',
	`access`       INT(10)      NOT NULL DEFAULT '0',
	`latitude`     DOUBLE(20, 6),
	`longitude`    DOUBLE(20, 6),
	`zoom`         INT(11)      NOT NULL DEFAULT '0',
	`items_tags`   MEDIUMTEXT   NOT NULL DEFAULT '',
	UNIQUE KEY `id` (`id`)
)
	ENGINE = MyISAM
	DEFAULT CHARSET = utf8
	AUTO_INCREMENT = 0;

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
