create table if not exists `species`
(
`id` smallint unsigned not null,
`identifier` varchar(12) not null,

`introduced_in_version_group_id` tinyint unsigned not null,
`base_egg_cycles` tinyint unsigned not null,
`base_friendship` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
