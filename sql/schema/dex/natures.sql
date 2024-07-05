create table if not exists `natures`
(
`id` tinyint unsigned not null,

`identifier` varchar(7) not null,
`increased_stat_id` tinyint unsigned null, # nullable
`decreased_stat_id` tinyint unsigned null, # nullable
`toxel_evo_id` smallint unsigned not null,
`vc_exp_remainder` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`increased_stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`decreased_stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`toxel_evo_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
