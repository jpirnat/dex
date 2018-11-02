create table if not exists `exclusive_z_moves`
(
`pokemon_id` smallint unsigned not null,
`z_crystal_id` smallint unsigned not null,

`move_id` smallint unsigned not null,
`z_move_id` smallint unsigned not null,

primary key (
	`pokemon_id`,
	`z_crystal_id`
),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_crystal_id`) references `items` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
