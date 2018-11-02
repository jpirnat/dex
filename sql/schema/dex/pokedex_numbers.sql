create table if not exists `pokedex_numbers`
(
`pokedex_id` tinyint unsigned not null,
`number` smallint unsigned not null,

`species_id` smallint unsigned not null,

primary key (
	`pokedex_id`,
	`number`
),
foreign key (`pokedex_id`) references `pokedexes` (`id`)
	on delete restrict
	on update cascade,
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
