create table if not exists `nature_names`
(
`language_id` tinyint unsigned not null,
`nature_id` tinyint unsigned not null,

`name` varchar(7) not null,
`description` varchar(89) not null,

primary key (
	`language_id`,
	`nature_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
