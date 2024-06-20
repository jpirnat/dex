<?php
/**
 * This file defines the order in which database tables must be created and have
 * their data imported, respecting the database's many foreign key constraints.
 * These tables must be created and populated before the stats schema tables.
 */
declare(strict_types=1);

// For tables whose data is split across multiple import files, the table name
// should be the key to an array of those file names (excluding file extension).

return [
	// Languages
	'languages',
	'language_names',

	// Versions
	'generations',
	'version_groups',
	'versions',
	'version_group_languages',
	'version_group_names',
	'version_names',

	// Pokémon
	'species',
	'species_names',
	'experience_groups',
	'pokemon',
	'pokemon_names',
	'forms',
	'form_names',
	'models',
	'form_icons',
	'version_group_forms',

	// Abilities
	'abilities',
	'ability_names',
	'ability_descriptions' => [
		'ability_descriptions_04',
		'ability_descriptions_05',
		'ability_descriptions_06',
		'ability_descriptions_07',
		'ability_descriptions_08',
		'ability_descriptions_09',
	],
	'vg_abilities',
	'pokemon_abilities',

	// Items
	'item_fling_effects',
	'items',
	'item_descriptions' => [
		'item_descriptions_04',
		'item_descriptions_05',
		'item_descriptions_06',
		'item_descriptions_07',
		'item_descriptions_08_ss',
		'item_descriptions_08_bdsp',
		'item_descriptions_08_la',
		'item_descriptions_09_sv',
	],
	'item_names',
	'item_icons',
	'version_group_items',

	// Types
	'categories',
	'category_names',
	'types',
	'type_names',
	'vg_types',
	'type_icons',
	'type_matchups',
	'pokemon_types',

	// Moves
	'moves',
	'move_names',
	'move_descriptions' => [
		'move_descriptions_04',
		'move_descriptions_05',
		'move_descriptions_06',
		'move_descriptions_07',
		'move_descriptions_08',
		'move_descriptions_09_sv',
	],
	'z_move_names',
	'z_move_images',

	// Version group moves
	'qualities',
	'inflictions',
	'infliction_names',
	'infliction_durations',
	'targets',
	'target_names',
	'affinities',
	'z_power_effects',
	'z_power_effect_names',
	'vg_moves' => [
		'vg_moves_01',
		'vg_moves_02',
		'vg_moves_03',
		'vg_moves_04',
		'vg_moves_05',
		'vg_moves_06',
		'vg_moves_07',
		'vg_moves_08_ss',
		'vg_moves_08_bdsp',
		'vg_moves_08_la',
		'vg_moves_09_sv',
	],
	'vg_moves_legends' => [
		'vg_moves_legends_08_la',
	],

	// Pokémon moves
	'technical_machines',
	'move_methods',
	'move_method_names',
	'pokemon_moves' => [
		'pokemon_moves_01',
		'pokemon_moves_02',
		'pokemon_moves_03',
		'pokemon_moves_04',
		'pokemon_moves_05',
		'pokemon_moves_06',
		'pokemon_moves_07',
		'pokemon_moves_08_ss',
		'pokemon_moves_08_bdsp',
		'pokemon_moves_08_la',
		'pokemon_moves_09_sv',
	],
	'exclusive_z_moves',

	// Move flags
	'flags',
	'flag_descriptions',
	'vg_flags',
	'move_flags' => [
		'move_flags_02',
		'move_flags_03',
		'move_flags_04',
		'move_flags_05',
		'move_flags_06',
		'move_flags_07',
		'move_flags_08',
		'move_flags_09',
	],

	// Stats
	'stats',
	'stat_names',
	'base_stats',
	'ev_yields',
	'move_stat_changes',

	// Characteristics
	'characteristics',
	'characteristic_names',

	// Natures
	'natures',
	'nature_names',

	// Colors
	'colors',
	'form_colors',

	// Egg groups
	'egg_groups',
	'egg_group_names',
	'pokemon_egg_groups',

	// Habitats
	'habitats',
	'species_habitats',

	// Pokédexes
	'regions',
	'region_names',
	'pokedexes',
	'pokedex_numbers',
	'pokedex_entries' => [
		'pokedex_entries_4',
		'pokedex_entries_5',
		'pokedex_entries_6',
		'pokedex_entries_7',
		'pokedex_entries_8_ss',
		'pokedex_entries_8_bdsp',
		'pokedex_entries_8_la',
		'pokedex_entries_9_sv',
	],

	// Shapes
	'shapes',
	'shape_icons',
	'pokemon_shapes',

	// Miscellaneous
	'base_experience',
	'base_friendships',
	'catch_rates',
	'transformation_groups',
	'transformation_group_pokemon',

	// Evolutions
	'evolution_methods',
	'evolutions',

	// Pokémon Showdown identifiers
	'formats',
	'format_names',
	'showdown_formats_to_import',
	'showdown_formats_to_ignore',
	'showdown_pokemon_to_import',
	'showdown_pokemon_to_ignore',
	'showdown_abilities_to_import',
	'showdown_abilities_to_ignore',
	'showdown_items_to_import',
	'showdown_items_to_ignore',
	'showdown_natures_to_import',
	'showdown_natures_to_ignore',
	'showdown_moves_to_import',
	'showdown_moves_to_ignore',
];
