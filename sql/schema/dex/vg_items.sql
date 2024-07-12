create table if not exists `vg_items`
(
`version_group_id` tinyint unsigned not null,
`item_id` smallint unsigned not null,

`game_index` smallint unsigned not null,
`icon` varchar(44) not null,
`item_fling_power` tinyint unsigned null, # nullable
`item_fling_effect_id` tinyint unsigned null, # nullable
`is_available` bool not null,

primary key (
	`version_group_id`,
	`item_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_fling_effect_id`) references `item_fling_effects` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
