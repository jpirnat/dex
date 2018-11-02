create table if not exists `pokedexes`
(
`id` tinyint unsigned not null,

`identifier` varchar(15) not null,
`region_id` tinyint unsigned null, # nullable

primary key (`id`),
unique key (`identifier`),
foreign key (`region_id`) references `regions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
