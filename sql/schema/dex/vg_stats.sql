create table if not exists `vg_stats`
(
`version_group_id` tinyint unsigned not null,
`stat_id` tinyint unsigned not null,

primary key (`version_group_id`, `stat_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
