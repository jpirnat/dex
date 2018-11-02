create table if not exists `format_names`
(
`language_id` tinyint unsigned not null,
`format_id` tinyint unsigned not null,

`name` varchar(30) not null,

primary key (
	`language_id`,
	`format_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
