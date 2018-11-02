create table if not exists `version_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(25) not null,
`generation` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;
