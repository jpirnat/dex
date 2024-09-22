create table if not exists `version_groups`
(
`id` tinyint unsigned not null,
`identifier` varchar(4) not null,

`generation_id` tinyint unsigned not null,
`abbreviation` varchar(4) not null,
`has_typed_hidden_power` bool not null,
`has_ivs` bool not null,
`has_evs` bool not null,
`has_abilities` bool not null,
`has_natures` bool not null,
`has_characteristics` bool not null,
`sort` tinyint unsigned not null,
`breeding_priority` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
