create table if not exists `type_icons`
(
`language_id` tinyint unsigned not null,
`type_id` tinyint unsigned not null,

`icon` varchar(19) not null,

primary key (
	`language_id`,
	`type_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
