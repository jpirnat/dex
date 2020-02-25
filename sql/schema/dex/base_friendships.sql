create table if not exists `base_friendships`
(
`generation_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`base_friendship` tinyint unsigned not null,

primary key (
	`generation_id`,
	`pokemon_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
