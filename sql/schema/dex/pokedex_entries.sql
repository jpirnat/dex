create table if not exists `pokedex_entries`
(
`language_id` tinyint unsigned not null,
`form_id` smallint unsigned not null,
`version_id` tinyint unsigned not null,
`is_female` bool not null,

`entry` text not null,

primary key (
	`language_id`,
	`form_id`,
	`version_id`,
	`is_female`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_id`) references `versions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
