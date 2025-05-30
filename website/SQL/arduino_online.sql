CREATE TABLE `arduino_online` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`model` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`datetime` DATETIME NOT NULL,
	`mac` VARCHAR(17) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`password` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci',
	`status` ENUM('disconnected','connected') NULL DEFAULT 'disconnected' COLLATE 'utf8mb4_unicode_ci',
	PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_unicode_ci'
ENGINE=InnoDB
AUTO_INCREMENT=11
;
