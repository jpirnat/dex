create table if not exists `moveset_rated_spreads`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`nature_id` tinyint unsigned not null,
`hp` tinyint unsigned not null,
`atk` tinyint unsigned not null,
`def` tinyint unsigned not null,
`spa` tinyint unsigned not null,
`spd` tinyint unsigned not null,
`spe` tinyint unsigned not null,
`percent` decimal(6, 3) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`nature_id`,
	`hp`,
	`atk`,
	`def`,
	`spa`,
	`spd`,
	`spe`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
