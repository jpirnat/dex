create table if not exists `pokemon_egg_groups`
(
`generation_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`egg_group_id` tinyint unsigned not null,

primary key (
	`generation_id`,
	`pokemon_id`,
	`egg_group_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`egg_group_id`) references `egg_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
