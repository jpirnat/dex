create table if not exists `base_experience`
(
`version_group_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`base_experience` smallint unsigned not null,

primary key (
	`version_group_id`,
	`pokemon_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
