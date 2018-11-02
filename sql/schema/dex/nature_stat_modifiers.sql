create table if not exists `nature_stat_modifiers`
(
`nature_id` tinyint unsigned not null,
`stat_id` tinyint unsigned not null,

`modifier` decimal(2, 1) unsigned not null,

primary key (
	`nature_id`,
	`stat_id`
),
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
