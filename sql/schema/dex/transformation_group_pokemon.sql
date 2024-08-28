create table if not exists `transformation_group_pokemon`
(
`transformation_group_id` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

primary key (
	`transformation_group_id`,
	`pokemon_id`
),
foreign key (`transformation_group_id`) references `transformation_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
