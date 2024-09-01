create table if not exists `moveset_rated_counters`
(
`usage_rated_pokemon_id` int unsigned not null,
`counter_id` smallint unsigned not null,

`number1` decimal(6, 3) not null,
`number2` decimal(5, 2) not null,
`number3` decimal(5, 2) not null,
`percent_knocked_out` decimal(4, 1) not null,
`percent_switched_out` decimal(4, 1) not null,

primary key (`usage_rated_pokemon_id`, `counter_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`counter_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
