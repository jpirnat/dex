create table if not exists `vg_languages`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`language_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
