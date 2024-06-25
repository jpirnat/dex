create table if not exists `pokemon`
(
`id` smallint unsigned not null,
`identifier` varchar(29) not null,
`pokemon_identifier` varchar(21) not null,

`species_id` smallint unsigned not null,
`is_default_pokemon` bool not null,
`introduced_in_version_group_id` tinyint unsigned not null,
`experience_group_id` tinyint unsigned not null,
`gender_ratio` tinyint signed not null,
`smogon_dex_identifier` varchar(20) not null,
`sort` smallint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade,
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`experience_group_id`) references `experience_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
