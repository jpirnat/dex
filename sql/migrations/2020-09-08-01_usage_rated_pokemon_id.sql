/*
Before this migration, each of the `moveset_rated_*` tables contained all of
`month`, `format_id`, `rating`, and `pokemon_id` in their massive composite keys.
Now that the project has 5+ years of data, that means some HUGE table indexes.
Luckily, I can replace all those fields with a new `usage_rated_pokemon_id`.
It will make all queries on these tables more complicated, but it will cut back
significantly on storage space.
*/

# Rename the existing tables to set them apart from the new ones.
alter table `usage_rated_pokemon` rename `old_usage_rated_pokemon`;
alter table `leads_rated_pokemon` rename `old_leads_rated_pokemon`;
alter table `moveset_rated_pokemon` rename `old_moveset_rated_pokemon`;
alter table `moveset_rated_abilities` rename `old_moveset_rated_abilities`;
alter table `moveset_rated_items` rename `old_moveset_rated_items`;
alter table `moveset_rated_moves` rename `old_moveset_rated_moves`;
alter table `moveset_rated_spreads` rename `old_moveset_rated_spreads`;
alter table `moveset_rated_teammates` rename `old_moveset_rated_teammates`;
alter table `moveset_rated_counters` rename `old_moveset_rated_counters`;


# The definitions for the new versions of the tables, copied directly from their
# schema files.
create table if not exists `usage_rated_pokemon`
(
`id` int unsigned not null auto_increment,

`month` date not null,
`format_id` tinyint unsigned not null,
`rating` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

`rank` smallint unsigned not null,
`usage_percent` decimal(8, 5) unsigned not null,

primary key (`id`),
unique key (`month`,`format_id`,`rating`,`pokemon_id`),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `leads_rated_pokemon`
(
`usage_rated_pokemon_id` int unsigned not null,

`rank` smallint unsigned not null,
`usage_percent` decimal(8, 5) unsigned not null,

primary key (`usage_rated_pokemon_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_pokemon`
(
`usage_rated_pokemon_id` int unsigned not null,

`average_weight` decimal(18, 15) unsigned not null,

primary key (`usage_rated_pokemon_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_abilities`
(
`usage_rated_pokemon_id` int unsigned not null,
`ability_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (`usage_rated_pokemon_id`, `ability_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_items`
(
`usage_rated_pokemon_id` int unsigned not null,
`item_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (`usage_rated_pokemon_id`, `item_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_moves`
(
`usage_rated_pokemon_id` int unsigned not null,
`move_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (`usage_rated_pokemon_id`, `move_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_spreads`
(
`id` int unsigned not null auto_increment,

`usage_rated_pokemon_id` int unsigned not null,

`nature_id` tinyint unsigned not null,
`hp` tinyint unsigned not null,
`atk` tinyint unsigned not null,
`def` tinyint unsigned not null,
`spa` tinyint unsigned not null,
`spd` tinyint unsigned not null,
`spe` tinyint unsigned not null,
`percent` decimal(6, 3) unsigned not null,

primary key (`id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_teammates`
(
`usage_rated_pokemon_id` int unsigned not null,
`teammate_id` smallint unsigned not null,

`percent` decimal(6, 3) unsigned not null,

primary key (`usage_rated_pokemon_id`, `teammate_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`teammate_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moveset_rated_counters`
(
`usage_rated_pokemon_id` int unsigned not null,
`counter_id` smallint unsigned not null,

`number1` decimal(6, 3) unsigned not null,
`number2` decimal(5, 2) unsigned not null,
`number3` decimal(5, 2) unsigned not null,
`percent_knocked_out` decimal(4, 1) unsigned not null,
`percent_switched_out` decimal(4, 1) unsigned not null,

primary key (`usage_rated_pokemon_id`, `counter_id`),
foreign key (`usage_rated_pokemon_id`) references `usage_rated_pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`counter_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


# Re-insert usage rated Pokémon.
insert into `usage_rated_pokemon` (
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`rank`,
	`usage_percent`
)
select
	`month`,
	`format_id`,
	`rating`,
	`pokemon_id`,
	`rank`,
	`usage_percent`
from `old_usage_rated_pokemon`
order by `month`, `format_id`, `rating`, `pokemon_id`;


# Re-insert all other tables via joins on usage rated Pokémon.
insert into `leads_rated_pokemon` (
	`usage_rated_pokemon_id`,
	`rank`,
	`usage_percent`
)
select
	`urp`.`id`,
	`olrp`.`rank`,
	`olrp`.`usage_percent`
from `usage_rated_pokemon` as `urp`
inner join `old_leads_rated_pokemon` as `olrp`
	on  `urp`.`month`      = `olrp`.`month`
	and `urp`.`format_id`  = `olrp`.`format_id`
	and `urp`.`rating`     = `olrp`.`rating`
	and `urp`.`pokemon_id` = `olrp`.`pokemon_id`
order by `urp`.`id`
;


insert into `moveset_rated_pokemon` (
	`usage_rated_pokemon_id`,
	`average_weight`
)
select
	`urp`.`id`,
	`omrp`.`average_weight`
from `usage_rated_pokemon` as `urp`
inner join `old_moveset_rated_pokemon` as `omrp`
	on  `urp`.`month`      = `omrp`.`month`
	and `urp`.`format_id`  = `omrp`.`format_id`
	and `urp`.`rating`     = `omrp`.`rating`
	and `urp`.`pokemon_id` = `omrp`.`pokemon_id`
order by `urp`.`id`
;


insert into `moveset_rated_abilities` (
	`usage_rated_pokemon_id`,
	`ability_id`,
	`percent`
)
select
	`urp`.`id`,
	`omra`.`ability_id`,
	`omra`.`percent`
from `usage_rated_pokemon` as `urp`
inner join `old_moveset_rated_abilities` as `omra`
	on  `urp`.`month`      = `omra`.`month`
	and `urp`.`format_id`  = `omra`.`format_id`
	and `urp`.`rating`     = `omra`.`rating`
	and `urp`.`pokemon_id` = `omra`.`pokemon_id`
order by `urp`.`id`, `omra`.`ability_id`
;


insert into `moveset_rated_items` (
	`usage_rated_pokemon_id`,
	`item_id`,
	`percent`
)
select
	`urp`.`id`,
	`omri`.`item_id`,
	`omri`.`percent`
from `usage_rated_pokemon` as `urp`
inner join `old_moveset_rated_items` as `omri`
	on  `urp`.`month`      = `omri`.`month`
	and `urp`.`format_id`  = `omri`.`format_id`
	and `urp`.`rating`     = `omri`.`rating`
	and `urp`.`pokemon_id` = `omri`.`pokemon_id`
order by `urp`.`id`, `omri`.`item_id`
;


insert into `moveset_rated_moves` (
	`usage_rated_pokemon_id`,
	`move_id`,
	`percent`
)
select
	`urp`.`id`,
	`omrm`.`move_id`,
	`omrm`.`percent`
from `usage_rated_pokemon` as `urp`
inner join `old_moveset_rated_moves` as `omrm`
	on  `urp`.`month`      = `omrm`.`month`
	and `urp`.`format_id`  = `omrm`.`format_id`
	and `urp`.`rating`     = `omrm`.`rating`
	and `urp`.`pokemon_id` = `omrm`.`pokemon_id`
order by `urp`.`id`, `omrm`.`move_id`
;


insert into `moveset_rated_spreads` (
	`usage_rated_pokemon_id`,
	`nature_id`,
	`hp`,
	`atk`,
	`def`,
	`spa`,
	`spd`,
	`spe`,
	`percent`
)
select
	`urp`.`id`,
	`omrs`.`nature_id`,
	`omrs`.`hp`,
	`omrs`.`atk`,
	`omrs`.`def`,
	`omrs`.`spa`,
	`omrs`.`spd`,
	`omrs`.`spe`,
	`omrs`.`percent`
from `usage_rated_pokemon` as `urp`
inner join `old_moveset_rated_spreads` as `omrs`
	on  `urp`.`month`      = `omrs`.`month`
	and `urp`.`format_id`  = `omrs`.`format_id`
	and `urp`.`rating`     = `omrs`.`rating`
	and `urp`.`pokemon_id` = `omrs`.`pokemon_id`
order by `urp`.`id`
;


insert into `moveset_rated_teammates` (
	`usage_rated_pokemon_id`,
	`teammate_id`,
	`percent`
)
select
	`urp`.`id`,
	`omrt`.`teammate_id`,
	`omrt`.`percent`
from `usage_rated_pokemon` as `urp`
inner join `old_moveset_rated_teammates` as `omrt`
	on  `urp`.`month`      = `omrt`.`month`
	and `urp`.`format_id`  = `omrt`.`format_id`
	and `urp`.`rating`     = `omrt`.`rating`
	and `urp`.`pokemon_id` = `omrt`.`pokemon_id`
order by `urp`.`id`, `omrt`.`teammate_id`
;


insert into `moveset_rated_counters` (
	`usage_rated_pokemon_id`,
	`counter_id`,
	`number1`,
	`number2`,
	`number3`,
	`percent_knocked_out`,
	`percent_switched_out`
)
select
	`urp`.`id`,
	`omrc`.`counter_id`,
	`omrc`.`number1`,
	`omrc`.`number2`,
	`omrc`.`number3`,
	`omrc`.`percent_knocked_out`,
	`omrc`.`percent_switched_out`
from `usage_rated_pokemon` as `urp`
inner join `old_moveset_rated_counters` as `omrc`
	on  `urp`.`month`      = `omrc`.`month`
	and `urp`.`format_id`  = `omrc`.`format_id`
	and `urp`.`rating`     = `omrc`.`rating`
	and `urp`.`pokemon_id` = `omrc`.`pokemon_id`
order by `urp`.`id`, `omrc`.`counter_id`
;


# Delete the old tables.
drop table `old_usage_rated_pokemon`;
drop table `old_leads_rated_pokemon`;
drop table `old_moveset_rated_pokemon`;
drop table `old_moveset_rated_abilities`;
drop table `old_moveset_rated_items`;
drop table `old_moveset_rated_moves`;
drop table `old_moveset_rated_spreads`;
drop table `old_moveset_rated_teammates`;
drop table `old_moveset_rated_counters`;
