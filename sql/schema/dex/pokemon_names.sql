create table if not exists `pokemon_names`
(
`language_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`name` varchar(31) not null,
`category` varchar(21) not null,

primary key (
	`language_id`,
	`pokemon_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
