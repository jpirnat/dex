create table if not exists `species_names`
(
`language_id` tinyint unsigned not null,
`species_id` smallint unsigned not null,

`name` varchar(12) not null,

primary key (
	`language_id`,
	`species_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
