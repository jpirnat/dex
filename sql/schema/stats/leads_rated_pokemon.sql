create table if not exists `leads_rated_pokemon`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`rank` smallint unsigned not null,
`usage_percent` decimal(8, 5) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
