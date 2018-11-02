create table if not exists `pokemon_shapes`
(
`generation` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`shape_id` tinyint unsigned not null,

primary key (
	`generation`,
	`pokemon_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`shape_id`) references `shapes` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
