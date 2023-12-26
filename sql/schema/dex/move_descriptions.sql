create table if not exists `move_descriptions`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`name` varchar(28) not null,
`description` varchar(255) not null,

primary key (
	`version_group_id`,
	`language_id`,
	`move_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
