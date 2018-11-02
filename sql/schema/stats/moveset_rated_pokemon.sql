create table if not exists `moveset_rated_pokemon`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`average_weight` decimal(18, 15) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
