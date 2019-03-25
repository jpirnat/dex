create table if not exists `stat_names`
(
`language_id` tinyint unsigned not null,
`stat_id` tinyint unsigned not null,

`name` varchar(11) not null,
`abbreviation` varchar(3) not null,

primary key (
	`language_id`,
	`stat_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
