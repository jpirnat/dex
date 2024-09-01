create table if not exists `leads_rated_pokemon`
(
`usage_rated_pokemon_id` int unsigned not null,

`rank` smallint unsigned not null,
`usage_percent` decimal(8, 5) not null,

primary key (`usage_rated_pokemon_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
