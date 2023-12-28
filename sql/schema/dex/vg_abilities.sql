create table if not exists `vg_abilities`
(
`version_group_id` tinyint unsigned not null,
`ability_id` smallint unsigned not null,

`disabled_by_neutralizing_gas` bool null,
`fail_role_play` bool null,
`no_receiver` bool null,
`no_entrain` bool null,
`no_trace` bool null,
`fail_skill_swap` bool null,
`cant_suppress` bool null,
`breakable` bool null,
`no_transform` bool null,

primary key (`version_group_id`, `ability_id`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
