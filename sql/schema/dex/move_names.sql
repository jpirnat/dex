create table if not exists `move_names`
(
`language_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`name` varchar(28) not null,

primary key (
	`language_id`,
	`move_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
