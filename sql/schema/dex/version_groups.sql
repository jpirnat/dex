create table if not exists `version_groups`
(
`id` tinyint unsigned not null,
`identifier` varchar(4) not null,

`generation_id` tinyint unsigned not null,
`abbreviation` varchar(4) not null,
`has_breeding` bool not null,
`steps_per_egg_cycle` smallint unsigned not null,
`stat_formula_type` varchar(7) not null,
`has_iv_based_stats` bool not null,
`max_iv` tinyint unsigned not null,
`has_iv_based_hidden_power` bool not null,
`has_ev_based_stats` bool not null,
`has_ev_yields` bool not null,
`max_evs_per_stat` tinyint unsigned not null,
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
