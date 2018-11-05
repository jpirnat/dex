create table if not exists `flags`
(
`id` tinyint unsigned not null,

`identifier` varchar(14) not null,
`introduced_in_generation_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
