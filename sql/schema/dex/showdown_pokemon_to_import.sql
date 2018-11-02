create table if not exists `showdown_pokemon_to_import`
(
`name` varchar(50) not null,

`pokemon_id` smallint unsigned not null,

primary key (`name`),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
