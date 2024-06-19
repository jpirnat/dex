create table if not exists `form_icons`
(
`version_group_id` tinyint unsigned not null,
`form_id` smallint unsigned not null,
`is_female` bool not null,
`is_right` bool not null,

`image` varchar(50) not null,

primary key (
	`version_group_id`,
	`form_id`,
	`is_female`,
	`is_right`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
