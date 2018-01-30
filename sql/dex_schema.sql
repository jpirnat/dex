create schema if not exists `dex`
	charset utf8mb4
	collate utf8mb4_unicode_520_ci
;


use `dex`;


create table if not exists `generations`
(
`generation` tinyint unsigned not null,

`identifier` varchar(15) not null,

primary key (`generation`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `version_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(25) not null,
`generation` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `versions`
(
`id` tinyint unsigned not null,

`identifier` varchar(14) not null,
`version_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `languages`
(
`id` tinyint unsigned not null,

`identifier` varchar(20) not null,
`locale` varchar(7) not null,
`date_format` varchar(9) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `language_names`
(
`in_language_id` tinyint unsigned not null,
`named_language_id` tinyint unsigned not null,

`name` varchar(21) not null,

primary key (
	`in_language_id`,
	`named_language_id`
),
foreign key (`in_language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`named_language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `version_group_languages`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`language_id`
)
) engine = InnoDB;


create table if not exists `experience_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,
`introduced_in_generation` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `species`
(
`id` smallint unsigned not null,
`identifier` varchar(12) not null,

`introduced_in_version_group_id` tinyint unsigned not null,
`base_egg_cycles` tinyint unsigned not null,
`base_friendship` tinyint unsigned not null,
`experience_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`experience_group_id`) references `experience_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `species_names`
(
`language_id` tinyint unsigned not null,
`species_id` smallint unsigned not null,

`name` varchar(12) not null,

primary key (
	`language_id`,
	`species_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon`
(
`id` smallint unsigned not null,
`identifier` varchar(26) not null,
`pokemon_identifier` varchar(17) not null,

`species_id` smallint unsigned not null,
`is_default_pokemon` bool not null,
`introduced_in_version_group_id` tinyint unsigned not null,
`height_m` decimal(3, 1) not null,
`weight_kg` decimal(4, 1) not null,
`gender_ratio` decimal(4, 1) null, # nullable

primary key (`id`),
unique key (`identifier`),
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade,
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon_names`
(
`language_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`name` varchar(31) not null,
`category` varchar(21) not null,

primary key (
	`language_id`,
	`pokemon_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `forms`
(
`id` smallint unsigned not null,
`identifier` varchar(26) not null,
`form_identifier` varchar(17) not null,

`pokemon_id` smallint unsigned not null,
`is_default_form` bool not null,
`introduced_in_version_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `form_names`
(
`language_id` tinyint unsigned not null,
`form_id` smallint unsigned not null,

`name` varchar(20) not null,

primary key (
	`language_id`,
	`form_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `models`
(
`form_id` smallint unsigned not null,
`is_shiny` bool not null,
`is_back` bool not null,
`is_female` bool not null,
`attacking_index` tinyint unsigned not null,

`image` varchar(50) not null,

primary key (
	`form_id`,
	`is_shiny`,
	`is_back`,
	`is_female`,
	`attacking_index`
),
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `form_icons`
(
`generation` tinyint unsigned not null,
`form_id` smallint unsigned not null,
`is_female` bool not null,
`is_right` bool not null,

`image` varchar(50) not null,

primary key (
	`generation`,
	`form_id`,
	`is_female`,
	`is_right`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `abilities`
(
`id` smallint unsigned not null,

`identifier` varchar(16) not null,
`introduced_in_version_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `ability_names`
(
`language_id` tinyint unsigned not null,
`ability_id` smallint unsigned not null,

`name` varchar(16) not null,

primary key (
	`language_id`,
	`ability_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon_abilities`
(
`generation` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`slot` tinyint unsigned not null,

`ability_id` smallint unsigned not null,
`is_hidden_ability` tinyint unsigned not null,

primary key (
	`generation`,
	`pokemon_id`,
	`slot`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `item_fling_effects`
(
`id` tinyint unsigned not null,

`identifier` varchar(9) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `items`
(
`id` smallint unsigned not null,

`identifier` varchar(30) not null,
`introduced_in_version_group_id` tinyint unsigned not null,
`item_fling_power` tinyint unsigned null, # nullable
`item_fling_effect_id` tinyint unsigned null, # nullable

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_fling_effect_id`) references `item_fling_effects` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `item_names`
(
`language_id` tinyint unsigned not null,
`item_id` smallint unsigned not null,

`name` varchar(18) not null,

primary key (
	`language_id`,
	`item_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `item_pockets`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `version_group_items`
(
`version_group_id` tinyint unsigned not null,
`item_id` smallint unsigned not null,

`game_index` smallint unsigned not null,
`item_pocket_id` tinyint unsigned null, # nullable

primary key (
	`version_group_id`,
	`item_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_pocket_id`) references `item_pockets` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `categories`
(
`id` tinyint unsigned not null,

`identifier` varchar(8) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `types`
(
`id` tinyint unsigned not null,

`identifier` varchar(8) not null,
`category_id` tinyint unsigned null, # nullable
`hidden_power_index` tinyint unsigned null, # nullable

primary key (`id`),
unique key (`identifier`),
foreign key (`category_id`) references `categories` (`id`)
	on delete restrict
	on update cascade,
unique key (`hidden_power_index`)
) engine = InnoDB;


create table if not exists `type_names`
(
`language_id` tinyint unsigned not null,
`type_id` tinyint unsigned not null,

`name` varchar(10) not null,

primary key (
	`language_id`,
	`type_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `type_icons`
(
`generation` tinyint unsigned not null,
`language_id` tinyint unsigned not null,
`type_id` tinyint unsigned not null,

`image` varchar(19) not null,

primary key (
	`generation`,
	`language_id`,
	`type_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `type_charts`
(
`generation` tinyint unsigned not null,
`attacking_type_id` tinyint unsigned not null,
`defending_type_id` tinyint unsigned not null,

`factor` decimal(2, 1) unsigned not null,

primary key (
	`generation`,
	`attacking_type_id`,
	`defending_type_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`attacking_type_id`) references `types` (`id`)
	on delete restrict
	on update cascade,
foreign key (`defending_type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon_types`
(
`generation` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`slot` tinyint unsigned not null,

`type_id` tinyint unsigned not null,

primary key (
	`generation`,
	`pokemon_id`,
	`slot`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`type_id`) references `types` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `moves`
(
`id` smallint unsigned not null,

`identifier` varchar(100) not null,
`introduced_in_version_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `move_names`
(
`language_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`name` varchar(28) not null,

primary key (
	`language_id`,
	`move_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `z_move_names`
(
`language_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`name` varchar(28) not null,

primary key (
	`language_id`,
	`move_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `qualities`
(
`id` tinyint unsigned not null,

`identifier` varchar(44) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `inflictions`
(
`id` tinyint unsigned not null,

`identifier` varchar(11) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `targets`
(
`id` tinyint unsigned not null,

`identifier` varchar(23) not null,

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
`infliction_percent` tinyint unsigned,
`min_turns` tinyint unsigned null,
`max_turns` tinyint unsigned null,
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


create table if not exists `flags`
(
`id` tinyint unsigned not null auto_increment,

`identifier` varchar(14) not null,
`introduced_in_generation` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `move_flags`
(
`generation` tinyint unsigned not null,
`move_id` smallint unsigned not null,
`flag_id` tinyint unsigned not null,

primary key (
	`generation`,
	`move_id`,
	`flag_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`flag_id`) references `flags` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `move_methods`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,
`introduced_in_generation` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon_moves`
(
`version_group_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`move_method_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,
`level` tinyint unsigned not null,

`sort` tinyint unsigned null, # nullable

primary key (
	`version_group_id`,
	`pokemon_id`,
	`move_method_id`,
	`move_id`,
	`level`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_method_id`) references `move_methods` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `exclusive_z_moves`
(
`pokemon_id` smallint unsigned not null,
`z_crystal_id` smallint unsigned not null,

`move_id` smallint unsigned not null,
`z_move_id` smallint unsigned not null,

primary key (
	`pokemon_id`,
	`z_crystal_id`
),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_crystal_id`) references `items` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `stats`
(
`id` tinyint unsigned not null,

`identifier` varchar(15) not null,
`is_battle_only` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `stat_names`
(
`language_id` tinyint unsigned not null,
`stat_id` tinyint unsigned not null,

`name` varchar(11) not null,

primary key (
	`language_id`,
	`stat_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `base_stats`
(
`generation` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`stat_id` tinyint unsigned not null,

`value` tinyint unsigned not null,

primary key (
	`generation`,
	`pokemon_id`,
	`stat_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `ev_yields`
(
`version_group_id` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,
`stat_id` tinyint unsigned not null,

`value` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`pokemon_id`,
	`stat_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `move_stat_changes`
(
`generation` tinyint unsigned not null,
`move_id` smallint unsigned not null,
`stat_id` tinyint unsigned not null,

`stages` tinyint signed not null,
`percent` tinyint unsigned not null,

primary key (
	`generation`,
	`move_id`,
	`stat_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `characteristics`
(
`id` tinyint unsigned not null,

`identifier` varchar(23) not null,
`highest_stat_id` tinyint unsigned not null,
`iv_mod_five` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`highest_stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `characteristic_names`
(
`language_id` tinyint unsigned not null,
`characteristic_id` tinyint unsigned not null,

`name` varchar(40) not null,

primary key (
	`language_id`,
	`characteristic_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`characteristic_id`) references `characteristics` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `natures`
(
`id` tinyint unsigned not null,

`identifier` varchar(7) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `nature_names`
(
`language_id` tinyint unsigned not null,
`nature_id` tinyint unsigned not null,

`name` varchar(7) not null,
`description` varchar(89) not null,

primary key (
	`language_id`,
	`nature_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `nature_stat_modifiers`
(
`nature_id` tinyint unsigned not null,
`stat_id` tinyint unsigned not null,

`modifier` decimal(2, 1) unsigned not null,

primary key (
	`nature_id`,
	`stat_id`
),
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade,
foreign key (`stat_id`) references `stats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `colors`
(
`id` tinyint unsigned not null,

`identifier` varchar(6) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `form_colors`
(
`generation` tinyint unsigned not null,
`form_id` smallint unsigned not null,

`color_id` tinyint unsigned not null,

primary key (
	`generation`,
	`form_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade,
foreign key (`color_id`) references `colors` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `egg_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `pokemon_egg_groups`
(
`pokemon_id` smallint unsigned not null,
`egg_group_id` tinyint unsigned not null,

primary key (
	`pokemon_id`,
	`egg_group_id`
),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`egg_group_id`) references `egg_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `habitats`
(
`id` tinyint unsigned not null,

`identifier` varchar(13) not null,
`image` varchar(17) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `species_habitats`
(
`species_id` smallint unsigned not null,
`habitat_id` tinyint unsigned not null,

primary key (
	`species_id`,
	`habitat_id`
),
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade,
foreign key (`habitat_id`) references `habitats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `regions`
(
`id` tinyint unsigned not null,

`identifier` varchar(6) not null,
`introduced_in_generation` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokedexes`
(
`id` tinyint unsigned not null,

`identifier` varchar(15) not null,
`region_id` tinyint unsigned null, # nullable

primary key (`id`),
unique key (`identifier`),
foreign key (`region_id`) references `regions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokedex_numbers`
(
`pokedex_id` tinyint unsigned not null,
`number` smallint unsigned not null,

`species_id` smallint unsigned not null,

primary key (
	`pokedex_id`,
	`number`
),
foreign key (`pokedex_id`) references `pokedexes` (`id`)
	on delete restrict
	on update cascade,
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `shapes`
(
`id` tinyint unsigned not null,

`identifier` varchar(16) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `shape_icons`
(
`generation` tinyint unsigned not null,
`shape_id` tinyint unsigned not null,

`image` varchar(23) not null,

primary key (
	`generation`,
	`shape_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`shape_id`) references `shapes` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `pokemon_shapes`
(
`generation` tinyint unsigned not null,
`pokemon_id` smallint unsigned not null,

`shape_id` tinyint unsigned not null,

primary key (
	`generation`,
	`pokemon_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`shape_id`) references `shapes` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


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


create table if not exists `catch_rates`
(
`version_group_id` tinyint unsigned not null,
`species_id` smallint unsigned not null,

`catch_rate` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`species_id`
),
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade,
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `transformation_groups`
(
`id` smallint unsigned not null,

`identifier` varchar(10) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `transformation_group_pokemon`
(
`transformation_group_id` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

primary key (
	`transformation_group_id`,
	`pokemon_id`
)
) engine = InnoDB;
