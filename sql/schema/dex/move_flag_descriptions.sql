create table if not exists `move_flag_descriptions`
(
`language_id` tinyint unsigned not null,
`flag_id` tinyint unsigned not null,

`name` varchar(100) not null,
`description` varchar(200) not null,

primary key (`language_id`, `flag_id`),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`flag_id`) references `move_flags` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
