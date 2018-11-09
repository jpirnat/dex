create table if not exists `natures`
(
`id` tinyint unsigned not null,

`identifier` varchar(7) not null,
`increased_stat_id` tinyint unsigned null, # nullable
`decreased_stat_id` tinyint unsigned null, # nullable

primary key (`id`),
unique key (`identifier`),
foreign key (`increased_stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`decreased_stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
