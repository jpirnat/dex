create table if not exists `pokemon_abilities`
(
`generation` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`slot` tinyint unsigned not null,

`ability_id` smallint unsigned not null,
`is_hidden_ability` tinyint unsigned not null,

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
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
