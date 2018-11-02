create table if not exists `items`
(
`id` smallint unsigned not null,

`identifier` varchar(30) not null,
`introduced_in_version_group_id` tinyint unsigned not null,
`item_fling_power` tinyint unsigned null, # nullable
`item_fling_effect_id` tinyint unsigned null, # nullable

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_fling_effect_id`) references `item_fling_effects` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
