create table if not exists `region_names`
(
`language_id` tinyint unsigned not null,
`region_id` tinyint unsigned not null,

`name` varchar(7) not null,

primary key (
	`language_id`,
	`region_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`region_id`) references `regions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
