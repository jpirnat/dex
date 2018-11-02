create table if not exists `pokemon_moves`
(
`pokemon_id` smallint unsigned not null,
`version_group_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,
`move_method_id` tinyint unsigned not null,
`level` tinyint unsigned not null,

`sort` tinyint unsigned not null,

primary key (
	`pokemon_id`,
	`version_group_id`,
	`move_id`,
	`move_method_id`,
	`level`
),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_method_id`) references `move_methods` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
