create table if not exists `pokemon_types`
(
`generation_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`slot` tinyint unsigned not null,

`type_id` tinyint unsigned not null,

primary key (
	`generation_id`,
	`pokemon_id`,
	`slot`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
