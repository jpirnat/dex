create table if not exists `ability_names`
(
`language_id` tinyint unsigned not null,
`ability_id` smallint unsigned not null,

`name` varchar(16) not null,

primary key (
	`language_id`,
	`ability_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
