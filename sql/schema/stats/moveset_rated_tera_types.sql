create table if not exists `moveset_rated_tera_types`
(
`usage_rated_pokemon_id` int unsigned not null,
`type_id` tinyint unsigned not null,

`percent` decimal(6, 3) not null,

primary key (`usage_rated_pokemon_id`, `type_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
