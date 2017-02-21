use `dex`;


create table if not exists `formats`
(
`id` tinyint unsigned not null auto_increment,

`name` varchar(30) not null,
`generation` tinyint unsigned not null,
`level` tinyint unsigned not null,
`field_size` tinyint unsigned not null,
`team_size` tinyint unsigned not null,
`in_battle_team_size` tinyint unsigned not null,

primary key (`id`),
unique key (`name`)
) engine = InnoDB;


create table if not exists `showdown_formats_to_import`
(
`name` varchar(50) not null,

`format_id` tinyint unsigned not null,

primary key (`name`),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_formats_to_ignore`
(
`name` varchar(50) not null,

`format_id` tinyint unsigned null,

primary key (`name`),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_pokemon_to_import`
(
`name` varchar(50) not null,

`pokemon_id` smallint unsigned not null,

primary key (`name`),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_pokemon_to_ignore`
(
`name` varchar(50) not null,

`pokemon_id` smallint unsigned null,

primary key (`name`),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_abilities_to_import`
(
`name` varchar(50) not null,

`ability_id` smallint unsigned not null,

primary key (`name`),
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_abilities_to_ignore`
(
`name` varchar(50) not null,

`ability_id` smallint unsigned null,

primary key (`name`),
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_items_to_import`
(
`name` varchar(50) not null,

`item_id` smallint unsigned not null,

primary key (`name`),
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_items_to_ignore`
(
`name` varchar(50) not null,

`item_id` smallint unsigned null,

primary key (`name`),
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_natures_to_import`
(
`name` varchar(50) not null,

`nature_id` tinyint unsigned not null,

primary key (`name`),
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_natures_to_ignore`
(
`name` varchar(50) not null,

`nature_id` tinyint unsigned null,

primary key (`name`),
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_moves_to_import`
(
`name` varchar(50) not null,

`move_id` smallint unsigned not null,

primary key (`name`),
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `showdown_moves_to_ignore`
(
`name` varchar(50) not null,

`move_id` smallint unsigned null,

primary key (`name`),
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;



/* /stats/year-month/format-rating.txt */

create table if not exists `usage`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,

`total_battles` mediumint unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `usage_rated`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,

`average_weight_per_team` decimal(6, 3) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `usage_pokemon`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`raw` mediumint unsigned not null,
`raw_percent` decimal(6, 3) unsigned not null,
`real` mediumint unsigned not null,
`real_percent` decimal(6, 3) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `usage_rated_pokemon`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`rank` smallint unsigned not null,
`usage_percent` decimal(8, 5) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;



/* /stats/year-month/leads/format-rating.txt */

create table if not exists `leads`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,

`total_leads` mediumint unsigned not null, # `usage`.`total_battles` * 2

primary key (
	`year`,
	`month`,
	`format_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `leads_pokemon`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`raw` mediumint unsigned not null,
`raw_percent` decimal(6, 3) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `leads_rated_pokemon`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`rank` smallint unsigned not null,
`usage_percent` decimal(8, 5) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;



/* /stats/year-month/moveset/format-rating.txt */

create table if not exists `moveset_pokemon`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`raw_count` mediumint unsigned not null,
`viability_ceiling` tinyint unsigned null, # nullable

primary key (
	`year`,
	`month`,
	`format_id`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_pokemon`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`average_weight` decimal(18, 15) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_abilities`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`ability_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`ability_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_items`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`item_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`item_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_spreads`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`nature_id` tinyint unsigned not null,
`hp` tinyint unsigned not null,
`atk` tinyint unsigned not null,
`def` tinyint unsigned not null,
`spa` tinyint unsigned not null,
`spd` tinyint unsigned not null,
`spe` tinyint unsigned not null,
`percent` decimal(6, 3) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`nature_id`,
	`hp`,
	`atk`,
	`def`,
	`spa`,
	`spd`,
	`spe`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_moves`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`move_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`move_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_teammates`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`teammate_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,
# `percent` % of teams that have `pokemon_id` also have `teammate_id`.

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`teammate_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`teammate_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_counters`
(
`year` smallint not null,
`month` tinyint not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,
`counter_id` smallint unsigned not null,

`number1` decimal(6, 3) unsigned not null,
`number2` decimal(5, 2) unsigned not null,
`number3` decimal(5, 2) unsigned not null,
`percent_knocked_out` decimal(4, 1) unsigned not null,
`percent_switched_out` decimal(4, 1) unsigned not null,

primary key (
	`year`,
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`counter_id`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`counter_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


/*
TODO:

- find out difference between `usage_pokemon`.`raw` and `moveset_pokemon`.`raw_count`
- find out what the other moveset counters numbers mean, and properly name them
- add `metagame_%` tables of metagame analysis?

*/
