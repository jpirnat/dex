create schema if not exists `trendalyzer`
	charset utf8mb4
	collate utf8mb4_unicode_520_ci
;


use `trendalyzer`;


/*
# Dropping tables in backwards chronological order.

drop table if exists `moveset_rated_counters`;
drop table if exists `moveset_rated_teammates`;
drop table if exists `moveset_rated_moves`;
drop table if exists `moveset_rated_spreads`;
drop table if exists `moveset_rated_items`;
drop table if exists `moveset_rated_abilities`;
drop table if exists `moveset_rated_pokemon`;
drop table if exists `moveset_pokemon`;
drop table if exists `leads_rated_pokemon`;
drop table if exists `leads_pokemon`;
drop table if exists `leads`;
drop table if exists `usage_rated_pokemon`;
drop table if exists `usage_pokemon`;
drop table if exists `usage_rated`;
drop table if exists `usage`;
drop table if exists `metagames`;
drop table if exists `moves`;
drop table if exists `natures`;
drop table if exists `items`;
drop table if exists `abilities`;
drop table if exists `pokemon`;

*/


create table if not exists `pokemon`
(
`id` int unsigned not null auto_increment,

`name` varchar(20) not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (`id`),
unique key (`name`)
) engine = InnoDB;


create table if not exists `abilities`
(
`id` int unsigned not null auto_increment,

`name` varchar(20) not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (`id`),
unique key (`name`)
) engine = InnoDB;


create table if not exists `items`
(
`id` int unsigned not null auto_increment,

`name` varchar(20) not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (`id`),
unique key (`name`)
) engine = InnoDB;


create table if not exists `natures`
(
`id` int unsigned not null auto_increment,

`name` varchar(20) not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (`id`),
unique key (`name`)
) engine = InnoDB;


create table if not exists `moves`
(
`id` int unsigned not null auto_increment,

`name` varchar(20) not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (`id`),
unique key (`name`)
) engine = InnoDB;


create table if not exists `metagames`
(
`id` int unsigned not null auto_increment,

`name` varchar(20) not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (`id`),
unique key (`name`)
) engine = InnoDB;


/* /stats/year-month/metagame-rating.txt */

create table if not exists `usage`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,

`total_battles` int unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `usage_rated`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,

`average_weight_per_team` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `usage_pokemon`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`pokemon_id` int unsigned not null,

`raw` int unsigned not null,
`raw_percent` decimal unsigned not null,
`real` int unsigned not null,
`real_percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`pokemon_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `usage_rated_pokemon`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,

`rank` int unsigned not null,
`usage_percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


/* /stats/year-month/leads/metagame-rating.txt */

create table if not exists `leads`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,

`total_leads` int unsigned not null, # `usage`.`total_battles` * 2

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `leads_pokemon`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`pokemon_id` int unsigned not null,

`raw` int unsigned not null,
`raw_percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`pokemon_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `leads_rated_pokemon`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,

`rank` int unsigned not null,
`usage_percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


/* /stats/year-month/moveset/metagame-rating.txt */

create table if not exists `moveset_pokemon`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`pokemon_id` int unsigned not null,

`raw_count` int unsigned not null,
`viability_ceiling` int unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`pokemon_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_pokemon`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,

`average_weight` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_abilities`
(
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,
`ability_id` int unsigned not null,

`percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`,
	`ability_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
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
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,
`item_id` int unsigned not null,

`percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`,
	`item_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
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
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,

`nature_id` int unsigned not null,
`hp` int unsigned not null,
`atk` int unsigned not null,
`def` int unsigned not null,
`spa` int unsigned not null,
`spd` int unsigned not null,
`spe` int unsigned not null,
`percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
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
foreign key (`metagame_id`) references `metagames` (`id`)
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
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,
`move_id` int unsigned not null,

`percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`,
	`move_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
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
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,
`teammate_id` int unsigned not null,

`percent` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`,
	`teammate_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
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
`year` int unsigned not null,
`month` int unsigned not null,
`metagame_id` int unsigned not null,
`rating` int unsigned not null,
`pokemon_id` int unsigned not null,
`counter_id` int unsigned not null,

`percent` decimal unsigned not null,
`percent_knocked_out` decimal unsigned not null,
`percent_switched_out` decimal unsigned not null,

`created_at` timestamp not null
	default current_timestamp,
`updated_at` timestamp not null
	default current_timestamp
	on update current_timestamp,

primary key (
	`year`,
	`month`,
	`metagame_id`,
	`rating`,
	`pokemon_id`,
	`counter_id`
),
foreign key (`metagame_id`) references `metagames` (`id`)
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

- enable nulls in columns that don't have full historic data (e.g., viability ceiling)
- find out difference between `usage_pokemon`.`raw`and `moveset_pokemon`.`raw_count`
- find out what the other moveset counters numbers mean, and add them to the schema

*/
