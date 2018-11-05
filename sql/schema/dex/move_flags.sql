create table if not exists `move_flags`
(
`generation_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,
`flag_id` tinyint unsigned not null,

primary key (
	`generation_id`,
	`move_id`,
	`flag_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`flag_id`) references `flags` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
