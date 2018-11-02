create table if not exists `characteristics`
(
`id` tinyint unsigned not null,

`identifier` varchar(23) not null,
`highest_stat_id` tinyint unsigned not null,
`iv_mod_five` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`highest_stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
