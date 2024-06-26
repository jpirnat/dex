create table if not exists `versions`
(
`id` tinyint unsigned not null,
`identifier` varchar(17) not null,

`version_group_id` tinyint unsigned not null,
`abbreviation` varchar(3) not null,
`background_color` varchar(7) not null,
`text_color` varchar(5) not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
