create table if not exists `vg_move_flags`
(
`version_group_id` tinyint unsigned not null,
`flag_id` tinyint unsigned not null,

`is_functional` bool not null,

primary key (`version_group_id`, `flag_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`flag_id`) references `move_flags` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
