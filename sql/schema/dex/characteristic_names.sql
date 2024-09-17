create table if not exists `characteristic_names`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`characteristic_id` tinyint unsigned not null,

`name` varchar(44) not null,

primary key (
	`version_group_id`,
	`language_id`,
	`characteristic_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`characteristic_id`) references `characteristics` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
