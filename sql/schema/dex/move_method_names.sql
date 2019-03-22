create table if not exists `move_method_names`
(
`language_id` tinyint unsigned not null,
`move_method_id` tinyint unsigned not null,

`name` varchar(20) not null,
`description` varchar(100) not null,

primary key (
	`language_id`,
	`move_method_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_method_id`) references `move_methods` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
