create table if not exists `generation_moves`
(
`generation` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`type_id` tinyint unsigned not null,
`quality_id` tinyint unsigned null, # nullable
`category_id` tinyint unsigned not null,
`power` tinyint unsigned not null,
`accuracy` tinyint unsigned not null,
`pp` tinyint unsigned not null,
`priority` tinyint signed not null,
`min_hits` tinyint unsigned not null,
`max_hits` tinyint unsigned not null,
`infliction_id` tinyint unsigned null, # nullable
`infliction_percent` tinyint unsigned not null,
`min_turns` tinyint unsigned not null,
`max_turns` tinyint unsigned not null,
`crit_stage` tinyint unsigned not null,
`flinch_percent` tinyint unsigned not null,
`effect` smallint unsigned null,
`effect_percent` tinyint unsigned null, # nullable
`recoil_percent` tinyint signed not null,
`heal_percent` tinyint signed not null,
`target_id` tinyint unsigned null,
`z_move_id` smallint unsigned null, # nullable
`z_base_power` tinyint unsigned null, # nullable
`z_power_effect_id` tinyint unsigned null, # nullable

primary key (
	`generation`,
	`move_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade,
foreign key (`quality_id`) references `qualities` (`id`)
	on delete restrict
	on update cascade,
foreign key (`category_id`) references `categories` (`id`)
	on delete restrict
	on update cascade,
foreign key (`infliction_id`) references `inflictions` (`id`)
	on delete restrict
	on update cascade,
foreign key (`target_id`) references `targets` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_power_effect_id`) references `z_power_effects` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
