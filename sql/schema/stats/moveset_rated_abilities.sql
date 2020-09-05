create table if not exists `moveset_rated_abilities`
(
`usage_rated_pokemon_id` int unsigned not null,
`ability_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (`usage_rated_pokemon_id`, `ability_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
