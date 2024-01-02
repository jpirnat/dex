create table if not exists `move_stat_changes`
(
`version_group_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,
`stat_id` tinyint unsigned not null,

`stages` tinyint signed not null,
`percent` tinyint unsigned not null,

primary key (
	`generation_id`,
	`move_id`,
	`stat_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
