create table if not exists `z_power_effect_names`
(
`language_id` tinyint unsigned not null,
`z_power_effect_id` tinyint unsigned not null,

`name` varchar(48) not null,

primary key (
	`language_id`,
	`z_power_effect_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_power_effect_id`) references `z_power_effects` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
