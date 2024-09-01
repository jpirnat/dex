create table if not exists `moveset_rated_items`
(
`usage_rated_pokemon_id` int unsigned not null,
`item_id` smallint unsigned not null,

`percent` decimal(6, 3) not null,

primary key (`usage_rated_pokemon_id`, `item_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
