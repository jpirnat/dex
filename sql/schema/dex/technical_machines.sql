create table if not exists `technical_machines`
(
`version_group_id` tinyint unsigned not null,
`is_hidden` bool not null,
`number` tinyint unsigned not null,

`item_id` smallint unsigned not null,
`move_id` smallint unsigned not null,

primary key (
	`version_group_id`,
	`is_hidden`,
	`number`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
