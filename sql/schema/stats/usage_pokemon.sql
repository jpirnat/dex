create table if not exists `usage_pokemon`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`raw` mediumint unsigned not null,
`raw_percent` decimal(6, 3) unsigned not null,
`real` mediumint unsigned not null,
`real_percent` decimal(6, 3) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
