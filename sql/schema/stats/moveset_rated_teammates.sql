create table if not exists `moveset_rated_teammates`
(
`usage_rated_pokemon_id` int unsigned not null,
`teammate_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,
# `percent` % of teams that have `pokemon_id` also have `teammate_id`.

primary key (`usage_rated_pokemon_id`, `teammate_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`teammate_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
