create table if not exists `usage_rated`
(
`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,

`average_weight_per_team` decimal(6, 3) unsigned not null,

primary key (
	`month`,
	`format_id`,
	`rating`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
