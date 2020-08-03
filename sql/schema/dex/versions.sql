create table if not exists `versions`
(
`id` tinyint unsigned not null,
`identifier` varchar(15) not null,

`version_group_id` tinyint unsigned not null,
`icon` varchar(19) not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
