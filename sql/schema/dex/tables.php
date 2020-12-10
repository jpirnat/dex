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
	'version_group_pokemon',

	// Abilities
	'abilities',
	'ability_names',
	'ability_descriptions',
	'pokemon_abilities',

	// Items
	'item_fling_effects',
	'items',
	'item_descriptions',
	'item_names',
	'item_pockets',
	'version_group_items',

	// Types
	'categories',
	'category_names',
	'types',
	'type_names',
	'type_icons',
	'type_matchups',
	'pokemon_types',

	// Moves
	'moves',
	'move_names',
	'move_descriptions',
	'z_move_names',
	'version_group_moves',

	// Generation moves
	'qualities',
	'inflictions',
	'infliction_names',
	'targets',
	'target_names',
	'z_power_effects',
	'z_power_effect_names',
	'generation_moves',

	// Pokémon moves
	'technical_machines',
	'move_methods',
	'move_method_names',
	'pokemon_moves' => [
		'pokemon_moves_1',
		'pokemon_moves_2',
		'pokemon_moves_3',
		'pokemon_moves_4',
		'pokemon_moves_5',
		'pokemon_moves_6',
		'pokemon_moves_7',
		'pokemon_moves_8',
	],
	'exclusive_z_moves',

	// Move flags
	'flags',
	'generation_flags',
	'flag_descriptions',
	'move_flags',

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
		'pokedex_entries_8',
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
