create table if not exists `experience_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,
`introduced_in_generation_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
