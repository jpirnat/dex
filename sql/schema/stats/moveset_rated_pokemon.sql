create table if not exists `moveset_rated_pokemon`
(
`usage_rated_pokemon_id` int unsigned not null,

`average_weight` decimal(18, 15) unsigned not null,

primary key (`usage_rated_pokemon_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
