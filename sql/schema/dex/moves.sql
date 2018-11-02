create table if not exists `moves`
(
`id` smallint unsigned not null,

`identifier` varchar(31) not null,
`introduced_in_version_group_id` tinyint unsigned not null,
`is_z_move` bool not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
