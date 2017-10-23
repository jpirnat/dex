create schema if not exists `dex`
	charset utf8mb4
	collate utf8mb4_unicode_520_ci
;


use `dex`;


source "versions.sql";


create table if not exists `categories`
(
`id` tinyint unsigned not null,

`identifier` varchar(8) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


insert into `categories` (
	`id`,
	`identifier`
) values
(1, "physical"),
(2, "special"),
(3, "status")
;


create table if not exists `stats`
(
`id` tinyint unsigned not null,

`identifier` varchar(15) not null,
`is_battle_only` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


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


insert into `experience_groups` (
	`id`,
	`identifier`,
	`introduced_in_generation`
) values
(1, "fast", 1),
(2, "medium-fast", 1),
(3, "medium-slow", 1),
(4, "slow", 1),
(5, "erratic", 3),
(6, "fluctuating", 3)
;


source "species.sql";
source "pokemon.sql";
source "forms.sql";


/* TODO: REFACTOR EVERYTHING BELOW THIS LINE */






/*
Reference materials:
veekun's blog post about the pitfalls of veekun in SQL:
https://eev.ee/blog/2016/08/05/storing-pok%C3%A9mon-without-sql/

another veekun gist with schema thoughts:
https://gist.github.com/eevee/6a257a9d42400e2d03f9

veekun table descriptions
https://github.com/veekun/pokedex/blob/5b57f60d2f3063ed48b6f1dbd88894f7457a43a1/pokedex/db/tables.py

veekun's list of forms and their differences
https://gist.github.com/eevee/15a92e26088d79a77fca

ROM text dumps of ORAS and SM
https://projectpokemon.org/research/

ROM text dumps of RBY, Crystal, Ruby, FR
http://iimarck.us/dumps/
*/





create table if not exists `z_exclusive_moves`
(
`pokemon_id` smallint unsigned not null,
`move_id` smallint unsigned not null,
`z_crystal_id` smallint unsigned not null,
`z_move_id` smallint unsigned not null,

primary key (`pokemon_id`),
foreign key (`pokemon_id`) references `pokemon` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_crystal_id`) references `items` (`id`)
	on delete restrict
	on update cascade,
foreign key (`z_move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade,
) engine = InnoDB;


insert into `z_exclusive_moves` (
	`pokemon_id`,
	`move_id`,
	`z_crystal_id`,
	`z_move_id`
) values
(25, 344, 857, 658),
(724, 662, 861, 695),
(727, 663, 862, 696),
(730, 664, 863, 697),
(785, 717, 864, 698),
(786, 717, 864, 698),
(787, 717, 864, 698),
(788, 717, 864, 698),
(802, 712, 865, 699),
(10111, 85, 866, 700),
(143, 416, 867, 701),
(133, 387, 868, 702),
(151, 94, 869, 703),
(10110, 85, 899, 719)
;
