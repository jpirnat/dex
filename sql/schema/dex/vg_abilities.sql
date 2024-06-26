create table if not exists `vg_abilities`
(
`version_group_id` tinyint unsigned not null,
`ability_id` smallint unsigned not null,

primary key (`version_group_id`, `ability_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
