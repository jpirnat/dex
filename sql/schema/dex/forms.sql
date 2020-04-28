create table if not exists `forms`
(
`id` smallint unsigned not null,
`identifier` varchar(50) not null,
`form_identifier` varchar(50) not null,

`pokemon_id` smallint unsigned not null,
`is_default_form` bool not null,
`introduced_in_version_group_id` tinyint unsigned not null,
`is_battle_only` bool not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
