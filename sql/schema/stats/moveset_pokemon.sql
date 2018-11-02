create table if not exists `moveset_pokemon`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`raw_count` mediumint unsigned not null,
`viability_ceiling` tinyint unsigned null, # nullable

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
