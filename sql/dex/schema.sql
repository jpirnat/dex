create schema if not exists `dex`
	charset utf8mb4
	collate utf8mb4_unicode_520_ci
;


use `dex`;


create table if not exists `categories`
(
`id` tinyint unsigned not null auto_increment,

`identifier` varchar(8) not null,

primary key (`id`)
) engine = InnoDB;


create table if not exists `egg_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `generations`
(
`id` tinyint unsigned not null auto_increment,

`identifier` varchar(8) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `stats`
(
`id` tinyint unsigned not null auto_increment,

`identifier` varchar(15) not null,
`is_battle_only` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `types`
(
`id` tinyint unsigned not null auto_increment,

`identifier` varchar(8) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `z_power_effects`
(
`id` tinyint unsigned not null,

`identifier` varchar(30) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `abilities`
(
`id` smallint unsigned not null,

`identifier` varchar(16) not null,
`generation_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `characteristics`
(
`id` tinyint unsigned not null auto_increment,

`identifier` varchar(23) not null,
`highest_stat_id` tinyint unsigned not null,
`iv_mod_five` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `moves`
(
`id` smallint unsigned not null auto_increment,

`identifier` varchar(100) not null,
`generation_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `natures`
(
`id` tinyint unsigned not null auto_increment,

`identifier` varchar(7) not null,
`increased_stat_id` tinyint unsigned not null,
`decreased_stat_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `type_charts`
(
`generation_id` tinyint unsigned not null,
`attacking_type_id` tinyint unsigned not null,
`defending_type_id` tinyint unsigned not null,

`factor` decimal(2, 1) unsigned not null,

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


create table if not exists `base_stats`
(
`generation_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`stat_id` tinyint unsigned not null,

`value` tinyint unsigned not null,

primary key (
	`generation_id`,
	`pokemon_id`,
	`stat_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;



/* TODO: REFACTOR EVERYTHING BELOW THIS LINE */









create table if not exists `type_categories`
(
`type_id` tinyint unsigned not null,
`category_id` tinyint unsigned not null,

primary key (`type_id`, `category_id`),
foreign key `type_id` references `types` (`id`)
	on delete restrict
	on update cascade,
foreign key `category_id` references `categories` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;






create table if not exists `pokemon_abilities`
(
`generation_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`slot` tinyint unsigned not null,

`ability_id` smallint unsigned not null,
`is_hidden_ability` tinyint unsigned not null,

primary key (
	`generation_id`,
	`pokemon_id`,
	`slot`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon_egg_groups`
(
`pokemon_id` smallint unsigned not null,
`egg_group_id` tinyint unsigned not null,

primary key (
	`pokemon_id`,
	`egg_group_id`
)
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`egg_group_id`) references `egg_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon_types`
(
`generation_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`slot` tinyint unsigned not null,

`type_id` tinyint unsigned not null,

primary key (
	`generation_id`,
	`pokemon_id`,
	`slot`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;




create table if not exists `ev_yields`
(
`pokemon_id` smallint unsigned not null,
`stat_id` tinyint unsigned not null,

`value` tinyint unsigned not null,

primary key (
	`pokemon_id`,
	`stat_id`
),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


/*
catch rate has changed between games, not just between generations
*/

/*
Reference materials:
veekun's blog post about the pitfalls of veekun in SQL:
https://eev.ee/blog/2016/08/05/storing-pok%C3%A9mon-without-sql/

another veekun gist with schema thoughts:
https://gist.github.com/eevee/6a257a9d42400e2d03f9

veekun table descriptions
https://github.com/veekun/pokedex/blob/5b57f60d2f3063ed48b6f1dbd88894f7457a43a1/pokedex/db/tables.py

veekun's list of forms and their differences
https://gist.github.com/eevee/15a92e26088d79a77fca

ROM text dumps of ORAS and SM
https://projectpokemon.org/research/

ROM text dumps of RBY, Crystal, Ruby, FR
http://iimarck.us/dumps/
*/






create table if not exists `generation_moves`
(
`generation_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`type_id`
`pp`
`category_id`
`power`
`accuracy`
`priority`

REORDER THESE COLUMNS APPROPRIATELY:
`target_id`
`effect_id`
`effect_chance`

primary key (
	`generation_id`,
	`move_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `move_names`
(
`generation_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,
`language_id` tinyint unsigned not null,

`name` varchar(100),

primary key (
	`generation_id`,
	`move_id`,
	`language_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `move_z_moves`
(
`move_id` smallint unsigned not null,

`z_move_id` tinyint unsigned not null,
`z_base_power`

primary key (`move_id`),
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
) engine = InnoDB;


create table if not exists `move_z_power_effects`
(
`move_id` smallint unsigned not null,

`z_power_effect_id` tinyint unsigned not null,

primary key (`move_id`),
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_power_effect_id`) references `z_power_effects` (`id`)
	on delete restrict
	on update cascade,
) engine = InnoDB;
