create table if not exists `move_flag_descriptions`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`flag_id` tinyint unsigned not null,

`name` varchar(32) not null,
`description_singular` varchar(559) not null,
`description_plural` varchar(556) not null,

primary key (`version_group_id`, `language_id`, `flag_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`flag_id`) references `move_flags` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
