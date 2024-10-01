select
	`vp`.`version_group_id`,
	`vp`.`pokemon_id`,
	ifnull(`t1`.`type_id`, "\\N") as `type1_id`,
	ifnull(`t2`.`type_id`, "\\N") as `type2_id`,
	ifnull(`a1`.`ability_id`, "\\N") as `ability1_id`,
	ifnull(`a2`.`ability_id`, "\\N") as `ability2_id`,
	ifnull(`a3`.`ability_id`, "\\N") as `ability3_id`,
	ifnull(`base_hp` , 0) as `base_hp`,
	ifnull(`base_atk`, 0) as `base_atk`,
	ifnull(`base_def`, 0) as `base_def`,
	ifnull(`base_spa`, 0) as `base_spa`,
	ifnull(`base_spd`, 0) as `base_spd`,
	ifnull(`base_spe`, 0) as `base_spe`,
	ifnull(`base_spc`, 0) as `base_spc`,
	ifnull(`base_experience`, 0) as `base_experience`,
	ifnull(`ev_hp` , 0) as `ev_hp`,
	ifnull(`ev_atk`, 0) as `ev_atk`,
	ifnull(`ev_def`, 0) as `ev_def`,
	ifnull(`ev_spa`, 0) as `ev_spa`,
	ifnull(`ev_spd`, 0) as `ev_spd`,
	ifnull(`ev_spe`, 0) as `ev_spe`,
	`p`.`species_id`
from (
	select distinct
		`vf`.`version_group_id`,
		`f`.`pokemon_id`
	from `vg_forms` as `vf`
	inner join `forms` as `f`
		on `vf`.`form_id` = `f`.`id`
	order by `version_group_id`, `pokemon_id`
) as `vp`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`type_id`
	from `pokemon_types`
	where `slot` = 1
) as `t1`
	on `vp`.`version_group_id` = `t1`.`version_group_id`
	and `vp`.`pokemon_id` = `t1`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`type_id`
	from `pokemon_types`
	where `slot` = 2
) as `t2`
	on `vp`.`version_group_id` = `t2`.`version_group_id`
	and `vp`.`pokemon_id` = `t2`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`ability_id`
	from `pokemon_abilities`
	where `slot` = 1
) as `a1`
	on `vp`.`version_group_id` = `a1`.`version_group_id`
	and `vp`.`pokemon_id` = `a1`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`ability_id`
	from `pokemon_abilities`
	where `slot` = 2
) as `a2`
	on `vp`.`version_group_id` = `a2`.`version_group_id`
	and `vp`.`pokemon_id` = `a2`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`ability_id`
	from `pokemon_abilities`
	where `slot` = 3
) as `a3`
	on `vp`.`version_group_id` = `a3`.`version_group_id`
	and `vp`.`pokemon_id` = `a3`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `base_hp`
	from `base_stats`
	where `stat_id` = 1
) as `base_hp`
	on `vp`.`version_group_id` = `base_hp`.`version_group_id`
	and `vp`.`pokemon_id` = `base_hp`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `base_atk`
	from `base_stats`
	where `stat_id` = 2
) as `base_atk`
	on `vp`.`version_group_id` = `base_atk`.`version_group_id`
	and `vp`.`pokemon_id` = `base_atk`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `base_def`
	from `base_stats`
	where `stat_id` = 3
) as `base_def`
	on `vp`.`version_group_id` = `base_def`.`version_group_id`
	and `vp`.`pokemon_id` = `base_def`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `base_spa`
	from `base_stats`
	where `stat_id` = 8
) as `base_spa`
	on `vp`.`version_group_id` = `base_spa`.`version_group_id`
	and `vp`.`pokemon_id` = `base_spa`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `base_spd`
	from `base_stats`
	where `stat_id` = 9
) as `base_spd`
	on `vp`.`version_group_id` = `base_spd`.`version_group_id`
	and `vp`.`pokemon_id` = `base_spd`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `base_spe`
	from `base_stats`
	where `stat_id` = 4
) as `base_spe`
	on `vp`.`version_group_id` = `base_spe`.`version_group_id`
	and `vp`.`pokemon_id` = `base_spe`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `base_spc`
	from `base_stats`
	where `stat_id` = 5
) as `base_spc`
	on `vp`.`version_group_id` = `base_spc`.`version_group_id`
	and `vp`.`pokemon_id` = `base_spc`.`pokemon_id`

left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`base_experience`
	from `base_experience`
) as `base_exp`
	on `vp`.`version_group_id` = `base_exp`.`version_group_id`
	and `vp`.`pokemon_id` = `base_exp`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `ev_hp`
	from `ev_yields`
	where `stat_id` = 1
) as `ev_hp`
	on `vp`.`version_group_id` = `ev_hp`.`version_group_id`
	and `vp`.`pokemon_id` = `ev_hp`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `ev_atk`
	from `ev_yields`
	where `stat_id` = 2
) as `ev_atk`
	on `vp`.`version_group_id` = `ev_atk`.`version_group_id`
	and `vp`.`pokemon_id` = `ev_atk`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `ev_def`
	from `ev_yields`
	where `stat_id` = 3
) as `ev_def`
	on `vp`.`version_group_id` = `ev_def`.`version_group_id`
	and `vp`.`pokemon_id` = `ev_def`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `ev_spa`
	from `ev_yields`
	where `stat_id` = 8
) as `ev_spa`
	on `vp`.`version_group_id` = `ev_spa`.`version_group_id`
	and `vp`.`pokemon_id` = `ev_spa`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `ev_spd`
	from `ev_yields`
	where `stat_id` = 9
) as `ev_spd`
	on `vp`.`version_group_id` = `ev_spd`.`version_group_id`
	and `vp`.`pokemon_id` = `ev_spd`.`pokemon_id`
left join (
	select
		`version_group_id`,
		`pokemon_id`,
		`value` as `ev_spe`
	from `ev_yields`
	where `stat_id` = 4
) as `ev_spe`
	on `vp`.`version_group_id` = `ev_spe`.`version_group_id`
	and `vp`.`pokemon_id` = `ev_spe`.`pokemon_id`
left join `pokemon` as `p`
	on `vp`.`pokemon_id` = `p`.`id`
;
