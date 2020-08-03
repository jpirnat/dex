create table if not exists `version_names`
(
`language_id` tinyint unsigned not null,
`version_id` tinyint unsigned not null,

`name` varchar(20) not null,

primary key (
	`language_id`,
	`version_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_id`) references `versions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
