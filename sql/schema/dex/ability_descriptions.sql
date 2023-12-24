create table if not exists `ability_descriptions`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`ability_id` smallint unsigned not null,

`name` varchar(16) not null,
`description` varchar(255) not null,

primary key (
	`generation_id`,
	`language_id`,
	`ability_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
