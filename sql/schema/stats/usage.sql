create table if not exists `usage`
(
`month` date not null,
`format_id` tinyint unsigned not null,

`total_battles` mediumint unsigned not null,

primary key (
	`month`,
	`format_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
