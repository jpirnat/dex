create table if not exists `move_descriptions`
(
`generation_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`description` varchar(255) not null,

primary key (
	`generation_id`,
	`language_id`,
	`move_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
