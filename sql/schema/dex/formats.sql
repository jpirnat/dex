create table if not exists `formats`
(
`id` tinyint unsigned not null,

`identifier` varchar(20) not null,
`generation` tinyint unsigned not null,
`level` tinyint unsigned not null,
`field_size` tinyint unsigned not null,
`team_size` tinyint unsigned not null,
`in_battle_team_size` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;
