create table if not exists `moveset_rated_counters`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`counter_id` smallint unsigned not null,

`number1` decimal(6, 3) unsigned not null,
`number2` decimal(5, 2) unsigned not null,
`number3` decimal(5, 2) unsigned not null,
`percent_knocked_out` decimal(4, 1) unsigned not null,
`percent_switched_out` decimal(4, 1) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`counter_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`counter_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
