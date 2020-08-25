create table if not exists `generation_flags`
(
`generation_id` tinyint unsigned not null,
`flag_id` tinyint unsigned not null,

primary key (`generation_id`, `flag_id`),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`flag_id`) references `flags` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
