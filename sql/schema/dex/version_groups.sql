create table if not exists `version_groups`
(
`id` tinyint unsigned not null,
`identifier` varchar(25) not null,

`generation_id` tinyint unsigned not null,
`icon` varchar(29) not null,
`breeding_priority` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
