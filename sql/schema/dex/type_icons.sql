create table if not exists `type_icons`
(
`generation` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`type_id` tinyint unsigned not null,

`image` varchar(19) not null,

primary key (
	`generation`,
	`language_id`,
	`type_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
