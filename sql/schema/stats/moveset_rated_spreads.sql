create table if not exists `moveset_rated_spreads`
(
`id` int unsigned not null auto_increment,

`usage_rated_pokemon_id` int unsigned not null,

`nature_id` tinyint unsigned not null,
`hp` tinyint unsigned not null,
`atk` tinyint unsigned not null,
`def` tinyint unsigned not null,
`spa` tinyint unsigned not null,
`spd` tinyint unsigned not null,
`spe` tinyint unsigned not null,
`percent` decimal(6, 3) not null,

primary key (`id`),
/*
# This unique key has been disabled because it gains the application no
# performance benefits, at the cost of a significantly larger table index.
unique key (
	`usage_rated_pokemon_id`,
	`nature_id`,
	`hp`,
	`atk`,
	`def`,
	`spa`,
	`spd`,
	`spe`
),
*/
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
