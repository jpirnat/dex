create table if not exists `moveset_rated_teammates`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`teammate_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,
# `percent` % of teams that have `pokemon_id` also have `teammate_id`.

primary key (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`teammate_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`teammate_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
