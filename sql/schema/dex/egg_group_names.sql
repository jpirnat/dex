create table if not exists `egg_group_names`
(
`language_id` tinyint unsigned not null,
`egg_group_id` tinyint unsigned not null,

`name` varchar(14) not null,

primary key (
	`language_id`,
	`egg_group_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`egg_group_id`) references `egg_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
