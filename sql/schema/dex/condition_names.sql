create table if not exists `condition_names`
(
`language_id` tinyint unsigned not null,
`condition_id` tinyint unsigned not null,

`name` varchar(12) not null,

primary key (
	`language_id`,
	`condition_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`condition_id`) references `conditions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
