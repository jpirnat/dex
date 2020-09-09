create table if not exists `target_names`
(
`language_id` tinyint unsigned not null,
`target_id` tinyint unsigned not null,

`name` varchar(50) not null,

primary key (`language_id`, `target_id`),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`target_id`) references `targets` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
