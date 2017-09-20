create table if not exists `qualities`
(
`id` tinyint unsigned not null,

`identifier` varchar(44) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


insert into `qualities` (
	`id`,
	`identifier`
) values
(1, "only-damage"),
(2, "no-damage-and-inflict-status"),
(3, "no-damage-and-minus-target-or-plus-user-stat"),
(4, "no-damage-and-heal-user"),
(5, "damage-and-inflict-status"),
(6, "no-damage-and-status-and-plus-target-stat"),
(7, "damage-and-minus-target-stat"),
(8, "damage-and-plus-user-stat"),
(9, "damage-and-absorbs-damage"),
(10, "one-hit-ko"),
(11, "affects-whole-field"),
(12, "affect-one-side-of-the-field"),
(13, "forces-target-to-switch"),
(14, "unique-effect")
;


create table if not exists `inflictions`
(
`id` tinyint unsigned not null,

`identifier` varchar(11) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


insert into `inflictions` (
	`id`,
	`identifier`
) values
(1, "paralysis"),
(2, "sleep"),
(3, "freeze"),
(4, "burn"),
(5, "poison"),
(6, "confusion"),
(7, "attract"),
(8, "bind"),
(9, "nightmare"),
(12, "torment"),
(13, "disable"),
(14, "yawn"),
(15, "heal-block"),
(17, "foresight"),
(18, "leech-seed"),
(19, "embargo"),
(20, "perish-song"),
(21, "ingrain"),
(24, "silence"),
(101, "special")
;


create table if not exists `targets`
(
`id` tinyint unsigned not null,

`identifier` varchar(23) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


insert into `targets` (
	`id`,
	`identifier`
) values
(1, "single-adjacent-pokemon"),
(2, "user-1"),
(3, "user-2"),
(4, "random-adjacent-foe"),
(5, "all-adjacent-foes"),
(6, "all-adjacent-pokemon-1"),
(7, "opponents-field"),
(8, "users-field"),
(9, "entire-field"),
(10, "single-adjacent-ally"),
(11, "user-or-adjacent-ally"),
(12, "single-adjacent-foe"),
(13, "all-allies"),
(14, "all-adjacent-pokemon-2")
;


create table if not exists `generation_moves`
(
`generation` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`type_id` tinyint unsigned not null,
`quality_id` tinyint unsigned null, # nullable UNTIL BACKFILLED
`category_id` tinyint unsigned not null,
`power` tinyint unsigned not null,
`accuracy` tinyint unsigned not null,
`pp` tinyint unsigned not null,
`priority` tinyint unsigned null, # nullable UNTIL BACKFILLED
`min_hits` tinyint unsigned null, # nullable UNTIL BACKFILLED
`max_hits` tinyint unsigned null, # nullable UNTIL BACKFILLED
`infliction_id` tinyint unsigned null, # nullable
`infliction_percent` tinyint unsigned, # nullable
`min_turns` tinyint unsigned null, # nullable UNTIL BACKFILLED
`max_turns` tinyint unsigned null, # nullable UNTIL BACKFILLED
`crit_stage` tinyint unsigned null, # nullable UNTIL BACKFILLED
`flinch_percent` tinyint unsigned null, # nullable UNTIL BACKFILLED
`effect` smallint unsigned null,
`effect_percent` tinyint unsigned null, # nullable
`recoil_percent` tinyint signed null, # nullable UNTIL BACKFILLED
`heal_percent` tinyint signed null, # nullable UNTIL BACKFILLED
`target_id` tinyint unsigned null, # nullable UNTIL BACKFILLED

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
	on update cascade
) engine = InnoDB;

