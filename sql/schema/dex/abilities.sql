create table if not exists `abilities`
(
`id` smallint unsigned not null,

`identifier` varchar(16) not null,
`introduced_in_version_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
