create table if not exists `vg_forms`
(
`version_group_id` tinyint unsigned not null,
`form_id` smallint unsigned not null,

primary key (
	`version_group_id`,
	`form_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
