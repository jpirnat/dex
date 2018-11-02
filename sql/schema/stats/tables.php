<?php
/**
 * This file defines the order in which database tables must be created and have
 * their data imported, respecting the database's many foreign key constraints.
 * These tables must be created and populated after the dex schema tables.
 */
declare(strict_types=1);

return [
	/* /stats/year-month/format-rating.txt */
	'usage',
	'usage_rated',
	'usage_pokemon',
	'usage_rated_pokemon',

	/* /stats/year-month/leads/format-rating.txt */
	'leads',
	'leads_pokemon',
	'leads_rated_pokemon',

	/* /stats/year-month/moveset/format-rating.txt */
	'moveset_pokemon',
	'moveset_rated_pokemon',
	'moveset_rated_abilities',
	'moveset_rated_items',
	'moveset_rated_spreads',
	'moveset_rated_moves',
	'moveset_rated_teammates',
	'moveset_rated_counters',
];

/*
TODO:
- Find out the difference between `usage_pokemon`.`raw` and `moveset_pokemon`.`raw_count`.
- Properly name the moveset counters `number1`, `number2`, and `number3` fields.
- Add `metagame_*` tables of metagame analysis from /stats/year-month/metagame?
*/
