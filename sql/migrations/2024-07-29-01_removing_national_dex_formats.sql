create table if not exists `urps_to_delete`
(
`id` int unsigned not null,

primary key (`id`)
) engine = InnoDB;

insert into `urps_to_delete` (
	`id`
)
select
	`id`
from `usage_rated_pokemon`
where `format_id` in (65, 66);

delete from `leads` where `format_id` in (65, 66);
delete from `leads_pokemon` where `format_id` in (65, 66);
delete from `leads_rated_pokemon` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);

delete from `moveset_pokemon` where `format_id` in (65, 66);
delete from `moveset_rated_abilities` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);
delete from `moveset_rated_counters` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);
delete from `moveset_rated_items` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);
delete from `moveset_rated_moves` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);
delete from `moveset_rated_pokemon` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);
delete from `moveset_rated_spreads` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);
delete from `moveset_rated_teammates` where `usage_rated_pokemon_id` in (
	select
		`id`
	from `urps_to_delete`
);

delete from `usage` where `format_id` in (65, 66);
delete from `usage_pokemon` where `format_id` in (65, 66);
delete from `usage_rated` where `format_id` in (65, 66);
delete from `usage_rated_pokemon` where `format_id` in (65, 66);

drop table if exists `urps_to_delete`;

delete from `showdown_formats_to_ignore` where `format_id` in (65, 66);
delete from `showdown_formats_to_import` where `format_id` in (65, 66);

delete from `format_names` where `format_id` in (65, 66);
delete from `formats` where `id` in (65, 66);
