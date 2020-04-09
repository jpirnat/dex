create table if not exists `version_group_moves`
(
`version_group_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

primary key (
	`version_group_id`,
	`move_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
