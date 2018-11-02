create table if not exists `moveset_rated_items`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`item_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`item_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
