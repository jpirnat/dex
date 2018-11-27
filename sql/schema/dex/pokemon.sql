create table if not exists `pokemon`
(
`id` smallint unsigned not null,
`identifier` varchar(26) not null,
`pokemon_identifier` varchar(17) not null,

`species_id` smallint unsigned not null,
`is_default_pokemon` bool not null,
`introduced_in_version_group_id` tinyint unsigned not null,
`height_m` decimal(3, 1) not null,
`weight_kg` decimal(4, 1) not null,
`gender_ratio` decimal(4, 1) null, # nullable
`smogon_dex_identifier` varchar(20) not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade,
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
