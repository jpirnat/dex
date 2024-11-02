create table if not exists `evolutions_incense`
(
`version_group_id` tinyint unsigned not null,
`form_id` smallint unsigned not null,

`item_id` smallint unsigned not null,

primary key (`version_group_id`, `form_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
