create table if not exists `ability_flag_descriptions`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`flag_id` tinyint unsigned not null,

`name` varchar(28) not null,
`description_singular` varchar(207) not null,
`description_plural` varchar(210) not null,

primary key (`version_group_id`, `language_id`, `flag_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`flag_id`) references `ability_flags` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
