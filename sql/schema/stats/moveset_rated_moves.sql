create table if not exists `moveset_rated_moves`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`move_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`move_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
