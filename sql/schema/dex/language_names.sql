create table if not exists `language_names`
(
`in_language_id` tinyint unsigned not null,
`named_language_id` tinyint unsigned not null,

`name` varchar(21) not null,

primary key (
	`in_language_id`,
	`named_language_id`
),
foreign key (`in_language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`named_language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
