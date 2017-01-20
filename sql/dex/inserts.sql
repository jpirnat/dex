use `dex`;


insert into `categories` (
	`id`,
	`identifier`
) values
(1, "physical"),
(2, "special"),
(3, "physical")
;


insert into `egg_groups` (
	`id`,
	`identifier`
) values
(1, "monster"),
(2, "water-1"),
(3, "bug"),
(4, "flying"),
(5, "field"),
(6, "fairy"),
(7, "grass"),
(8, "human-like"),
(9, "water-3"),
(10, "mineral"),
(11, "amorphous"),
(12, "water-2"),
(13, "ditto"),
(14, "dragon"),
(15, "undiscovered")
;


insert into `generations` (
	`id`,
	`identifier`
) values
(1, "Gen 1"),
(2, "Gen 2"),
(3, "Gen 3"),
(4, "Gen 4"),
(5, "Gen 5"),
(6, "Gen 6"),
(7, "Gen 7")
;


insert into `stats` (
	`id`,
	`identifier`,
	`is_battle_only`
) values
(1, "hp", 0),
(2, "attack", 0),
(3, "defense", 0),
(4, "speed", 0),
(5, "special", 0),
(6, "accuracy", 1),
(7, "evasiveness", 1),
(8, "special-attack", 0),
(9, "special-defense", 0)
;


insert into `types` (
	`id`,
	`identifier`
) values
(1, "normal"),
(2, "fighting"),
(3, "flying"),
(4, "poison"),
(5, "ground"),
(6, "rock"),
(7, "bug"),
(8, "ghost"),
(9, "steel"),
(10, "fire"),
(11, "water"),
(12, "grass"),
(13, "electric"),
(14, "psychic"),
(15, "ice"),
(16, "dragon"),
(17, "dark"),
(18, "fairy"),
(101, "bird")
(102, "unknown")
(103, "shadow")
;


insert into `z_power_effects` (
	`id`,
	`identifier`
) values
(1, "boosts-critical-hit-ratio"),
(2, "attack-1"),
(3, "defense-1"),
(4, "special-attack-1"),
(5, "special-defense-1"),
(6, "speed-1"),
(7, "accuracy-1"),
(8, "evasiveness-1"),
(9, "attack-2"),
(10, "defense-2"),
(11, "special-attack-2"),
(12, "special-defense-2"),
(13, "speed-2"),
(14, "accuracy-2"),
(15, "evasiveness-2"),
(16, "attack-3"),
(17, "defense-3"),
(18, "special-attack-3"),
(19, "special-defense-3"),
(20, "speed-3"),
(21, "accuracy-3"),
(22, "evasiveness-3"),
(23, "stats-1"),
(24, "stats-2"),
(25, "stats-3"),
(26, "reset-stats"),
(27, "restores-hp"),
(28, "restores-replacement-s-hp"),
(29, "center-of-attention"),
(30, "changes-depending-on-the-type"),
(101, "no-additional-effect")
;


source "abilities.sql";


insert into `characteristics` (
	`id`,
	`identifier`,
	`highest_stat_id`,
	`iv_mod_five`
) values
(1, "loves-to-eat", 1, 0),
(2, "takes-plenty-of-siestas", 1, 1),
(3, "nods-off-a-lot", 1, 2),
(4, "scatters-things-often", 1, 3),
(5, "likes-to-relax", 1, 4),
(6, "proud-of-its-power", 2, 0),
(7, "likes-to-thrash-about", 2, 1),
(8, "a-little-quick-tempered", 2, 2),
(9, "likes-to-fight", 2, 3),
(10, "quick-tempered", 2, 4),
(11, "sturdy-body", 3, 0),
(12, "capable-of-taking-hits", 3, 1),
(13, "highly-persistent", 3, 2),
(14, "good-endurance", 3, 3),
(15, "good-perseverance", 3, 4),
(16, "likes-to-run", 4, 0),
(17, "alert-to-sounds", 4, 1),
(18, "impetuous-and-silly", 4, 2),
(19, "somewhat-of-a-clown", 4, 3),
(20, "quick-to-flee", 4, 4),
(21, "highly-curious", 8, 0),
(22, "mischievous", 8, 1),
(23, "thoroughly-cunning", 8, 2),
(24, "often-lost-in-thought", 8, 3),
(25, "very-finicky", 8, 4),
(26, "strong-willed", 9, 0),
(27, "somewhat-vain", 9, 1),
(28, "strongly-defiant", 9, 2),
(29, "hates-to-lose", 9, 3),
(30, "somewhat-stubborn", 9, 4)
;


source "moves.sql";


insert into `natures` (
	`id`,
	`identifier`,
	`increased_stat_id`,
	`decreased_stat_id`
) values
(1, "hardy", 2, 2),
(2, "lonely", 2, 3),
(3, "brave", 2, 4),
(4, "adamant", 2, 8),
(5, "naughty", 2, 9),
(6, "bold", 3, 2),
(7, "docile", 3, 3),
(8, "relaxed", 3, 4),
(9, "impish", 3, 8),
(10, "lax", 3, 9),
(11, "timid", 4, 2),
(12, "hasty", 4, 3),
(13, "serious", 4, 4),
(14, "jolly", 4, 8),
(15, "naive", 4, 9),
(16, "modest", 8, 2),
(17, "mild", 8, 3),
(18, "quiet", 8, 4),
(19, "bashful", 8, 8),
(20, "rash", 8, 9),
(21, "calm", 9, 2),
(22, "gentle", 9, 3),
(23, "sassy", 9, 4),
(24, "careful", 9, 8),
(25, "quirky", 9, 9)
;


source "type_charts.sql";


source "base_stats.sql";


source "pokemon_abilities.sql";


source "pokemon_egg_groups.sql";


source "pokemon_types.sql";


