create table if not exists `forms`
(
`id` smallint unsigned not null,
`identifier` varchar(50) not null,
`form_identifier` varchar(50) not null,

`pokemon_id` smallint unsigned not null,
`is_default_form` bool not null,
`is_battle_only` bool not null,
`height_m` decimal(3, 1) not null,
`weight_kg` decimal(4, 1) not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
