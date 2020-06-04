create table if not exists `evolutions`
(
`version_group_id` tinyint unsigned not null,
`evo_from_id` smallint unsigned not null,
`evo_method_id` tinyint unsigned not null,
`evo_into_id` smallint unsigned not null,

`level` tinyint unsigned not null,
`item_id` smallint unsigned null, # nullable
`friendship` tinyint unsigned not null,
`move_id` smallint unsigned null, # nullable
`pokemon_id` smallint unsigned null, # nullable
`type_id` tinyint unsigned null, # nullable
`version_id` tinyint unsigned null, # nullable
`other_parameter` smallint unsigned not null,

primary key (
	`version_group_id`,
	`evo_from_id`,
	`evo_method_id`,
	`evo_into_id`
),

foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`evo_from_id`) references `forms` (`id`)
	on delete restrict
	on update cascade,
foreign key (`evo_method_id`) references `evolution_methods` (`id`)
	on delete restrict
	on update cascade,
foreign key (`evo_into_id`) references `forms` (`id`)
	on delete restrict
	on update cascade,

foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_id`) references `versions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;

/*
`other_parameter` is a smallint rather than a tinyint, despite it currently
holding values only up to 170, because in the game data structure this field
also holds item id, move id, etc. (which I have given their own columns in this
table for foreign key integrity). So, in future games this field COULD hold
larger values.
*/
