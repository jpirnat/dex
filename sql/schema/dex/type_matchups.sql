create table if not exists `type_matchups`
(
`generation_id` tinyint unsigned not null,
`attacking_type_id` tinyint unsigned not null,
`defending_type_id` tinyint unsigned not null,

`multiplier` decimal(2, 1) unsigned not null,

primary key (
	`generation_id`,
	`attacking_type_id`,
	`defending_type_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`attacking_type_id`) references `types` (`id`)
	on delete restrict
	on update cascade,
foreign key (`defending_type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
