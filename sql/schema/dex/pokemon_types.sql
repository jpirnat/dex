create table if not exists `pokemon_types`
(
`generation` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`slot` tinyint unsigned not null,

`type_id` tinyint unsigned not null,

primary key (
	`generation`,
	`pokemon_id`,
	`slot`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
