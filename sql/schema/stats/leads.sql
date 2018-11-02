create table if not exists `leads`
(
`month` date not null,
`format_id` tinyint unsigned not null,

`total_leads` mediumint unsigned not null, # `usage`.`total_battles` * 2

primary key (
	`month`,
	`format_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
