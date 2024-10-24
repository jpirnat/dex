create table if not exists `vg_pokemon`
(
`version_group_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`icon` varchar(40) not null,
`sprite` varchar(42) not null,

`type1_id` tinyint unsigned not null,
`type2_id` tinyint unsigned null,

`ability1_id` smallint unsigned null,
`ability2_id` smallint unsigned null,
`ability3_id` smallint unsigned null,

`base_hp` tinyint unsigned not null,
`base_atk` tinyint unsigned not null,
`base_def` tinyint unsigned not null,
`base_spa` tinyint unsigned not null,
`base_spd` tinyint unsigned not null,
`base_spe` tinyint unsigned not null,
`base_spc` tinyint unsigned not null,

`egg_group1_id` tinyint unsigned null,
`egg_group2_id` tinyint unsigned null,

`base_experience` smallint unsigned not null,
`ev_hp` tinyint unsigned not null,
`ev_atk` tinyint unsigned not null,
`ev_def` tinyint unsigned not null,
`ev_spa` tinyint unsigned not null,
`ev_spd` tinyint unsigned not null,
`ev_spe` tinyint unsigned not null,

`catch_rate` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`pokemon_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type1_id`) references `types` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type2_id`) references `types` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability1_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability2_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability3_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade,
foreign key (`egg_group1_id`) references `egg_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`egg_group2_id`) references `egg_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
