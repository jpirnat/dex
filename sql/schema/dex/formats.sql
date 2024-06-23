create table if not exists `formats`
(
`id` tinyint unsigned not null,
`identifier` varchar(30) not null,

`generation_id` tinyint unsigned not null,
`version_group_id` tinyint unsigned not null,
`level` tinyint unsigned not null,
`field_size` tinyint unsigned not null,
`team_size` tinyint unsigned not null,
`in_battle_team_size` tinyint unsigned not null,
`smogon_dex_identifier` varchar(20) not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
