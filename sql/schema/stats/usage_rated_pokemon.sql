create table if not exists `usage_rated_pokemon`
(
`id` int unsigned not null auto_increment,

`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`rank` smallint unsigned not null,
`usage_percent` decimal(8, 5) not null,

primary key (`id`),
unique key (`month`,`format_id`,`rating`,`pokemon_id`),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
