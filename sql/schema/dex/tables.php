<?php
/**
 * This file defines the order in which database tables must be created and have
 * their data imported, respecting the database's many foreign key constraints.
 * These tables must be created and populated before the stats schema tables.
 */
declare(strict_types=1);

return [
	// Versions
	'generations',
	'version_groups',
	'versions',

	// Languages
	'languages',
	'language_names',
	'version_group_languages',

	// Pokémon
	'experience_groups',
	'species',
	'species_names',
	'pokemon',
	'pokemon_names',
	'forms',
	'form_names',
	'models',
	'form_icons',

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
	'types',
	'type_names',
	'type_icons',
	'type_effectivenesses',
	'pokemon_types',

	// Moves
	'moves',
	'move_names',
	'move_descriptions',
	'z_move_names',
	'qualities',
	'inflictions',
	'targets',
	'z_power_effects',
	'z_power_effect_names',
	'generation_moves',
	'flags',
	'move_flags',
	'technical_machines',
	'move_methods',
	'move_method_names',
	'pokemon_moves',
	'exclusive_z_moves',

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
	'pokedex_entries',

	// Shapes
	'shapes',
	'shape_icons',
	'pokemon_shapes',

	// Miscellaneous
	'base_experience',
	'catch_rates',
	'transformation_groups',
	'transformation_group_pokemon',

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
