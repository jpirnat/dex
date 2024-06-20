create table if not exists `vg_types`
(
`version_group_id` tinyint unsigned not null,
`type_id` tinyint unsigned not null,

primary key (`version_group_id`, `type_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
