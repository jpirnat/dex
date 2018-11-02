create table if not exists `catch_rates`
(
`version_group_id` tinyint unsigned not null,
`species_id` smallint unsigned not null,

`catch_rate` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`species_id`
),
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
