/*
The project currently doesn't use automated migrations, so this script is only
really being saved for future reference, because it's the biggest foundational
schema change the project has ever had.
*/

# showdown_formats_to_import
alter table `showdown_formats_to_import`
add column `ym` date not null first
;

update `showdown_formats_to_import` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `showdown_formats_to_import`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `name`)
;

# showdown_formats_to_ignore
alter table `showdown_formats_to_ignore`
add column `ym` date not null first
;

update `showdown_formats_to_ignore` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `showdown_formats_to_ignore`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `name`)
;

# leads
alter table `leads`
add column `ym` date not null first
;

update `leads` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `leads`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`)
;

# leads_pokemon
alter table `leads_pokemon`
add column `ym` date not null first
;

update `leads_pokemon` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `leads_pokemon`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `pokemon_id`)
;

# leads_rated_pokemon
alter table `leads_rated_pokemon`
add column `ym` date not null first
;

update `leads_rated_pokemon` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `leads_rated_pokemon`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`)
;

# moveset_pokemon
alter table `moveset_pokemon`
add column `ym` date not null first
;

update `moveset_pokemon` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_pokemon`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `pokemon_id`)
;

# moveset_rated_abilities
alter table `moveset_rated_abilities`
add column `ym` date not null first
;

update `moveset_rated_abilities` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_rated_abilities`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`, `ability_id`)
;

# moveset_rated_counters
alter table `moveset_rated_counters`
add column `ym` date not null first
;

update `moveset_rated_counters` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_rated_counters`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`, `counter_id`)
;

# moveset_rated_items
alter table `moveset_rated_items`
add column `ym` date not null first
;

update `moveset_rated_items` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_rated_items`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`, `item_id`)
;

# moveset_rated_moves
alter table `moveset_rated_moves`
add column `ym` date not null first
;

update `moveset_rated_moves` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_rated_moves`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`, `move_id`)
;

# moveset_rated_pokemon
alter table `moveset_rated_pokemon`
add column `ym` date not null first
;

update `moveset_rated_pokemon` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_rated_pokemon`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`)
;

# moveset_rated_spreads
alter table `moveset_rated_spreads`
add column `ym` date not null first
;

update `moveset_rated_spreads` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_rated_spreads`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`, `nature_id`, `hp`, `atk`, `def`, `spa`, `spd`, `spe`)
;

# moveset_rated_teammates
alter table `moveset_rated_teammates`
add column `ym` date not null first
;

update `moveset_rated_teammates` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `moveset_rated_teammates`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`, `teammate_id`)
;

# usage
alter table `usage`
add column `ym` date not null first
;

update `usage` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `usage`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`)
;

# usage_pokemon
alter table `usage_pokemon`
add column `ym` date not null first
;

update `usage_pokemon` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `usage_pokemon`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `pokemon_id`)
;

# usage_rated
alter table `usage_rated`
add column `ym` date not null first
;

update `usage_rated` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `usage_rated`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`)
;

# usage_rated_pokemon
alter table `usage_rated_pokemon`
add column `ym` date not null first
;

update `usage_rated_pokemon` set
	`ym` = str_to_date(concat(`year`,"-",`month`,"-01"), "%Y-%m-%d")
;

alter table `usage_rated_pokemon`
drop primary key,
drop column `year`,
drop column `month`,
change `ym` `month` date not null,
add primary key (`month`, `format_id`, `rating`, `pokemon_id`)
;
