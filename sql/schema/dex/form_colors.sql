create table if not exists `form_colors`
(
`generation_id` tinyint unsigned not null,
`form_id` smallint unsigned not null,

`color_id` tinyint unsigned not null,

primary key (
	`generation_id`,
	`form_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade,
foreign key (`color_id`) references `colors` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
