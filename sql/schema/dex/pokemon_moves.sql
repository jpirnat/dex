create table if not exists `pokemon_moves`
(
`version_group_id` tinyint unsigned not null,
`pokemon_id` mediumint unsigned not null,
`move_id` smallint unsigned not null,
`move_method_id` tinyint unsigned not null,
`level` tinyint unsigned not null,
`mastery_level` tinyint unsigned not null,

`sort` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`pokemon_id`,
	`move_id`,
	`move_method_id`,
	`level`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_method_id`) references `move_methods` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
