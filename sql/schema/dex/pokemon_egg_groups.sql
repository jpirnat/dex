create table if not exists `pokemon_egg_groups`
(
`pokemon_id` smallint unsigned not null,
`egg_group_id` tinyint unsigned not null,

primary key (
	`pokemon_id`,
	`egg_group_id`
),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`egg_group_id`) references `egg_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
