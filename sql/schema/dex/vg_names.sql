create table if not exists `vg_names`
(
`language_id` tinyint unsigned not null,
`version_group_id` tinyint unsigned not null,

`name` varchar(35) not null,

primary key (
	`language_id`,
	`version_group_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
