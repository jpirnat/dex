create table if not exists `form_names`
(
`language_id` tinyint unsigned not null,
`form_id` smallint unsigned not null,

`name` varchar(20) not null,

primary key (
	`language_id`,
	`form_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
