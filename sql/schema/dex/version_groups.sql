create table if not exists `version_groups`
(
`id` tinyint unsigned not null,
`identifier` varchar(31) not null,

`generation_id` tinyint unsigned not null,
`icon` varchar(35) not null,
`abbreviation` varchar(4) not null,
`sort` tinyint unsigned not null,
`breeding_priority` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
