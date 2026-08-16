/* The updated `pokemon` table, roughly. */
select
    `f`.`id`,
    `f`.`identifier`,
    `p`.`species_id`,
    `p`.`experience_group_id`,
    `p`.`gender_ratio`,
    `p`.`smogon_dex_identifier`,
    `f`.`is_battle_only`,
    `f`.`height_m`,
    `f`.`weight_kg`,
    `p`.`sort`
from `forms` as `f`
inner join `pokemon` as `p`
    on `f`.`pokemon_id` = `p`.`id`
inner join `species` as `s`
    on `p`.`species_id` = `s`.`id`
order by
    `p`.`sort`,
    `f`.`id`
;

/* The updated `vg_pokemon` table. */
select
    `vgf`.`version_group_id`,
    `vgf`.`form_id` as `pokemon_id`,
    ifnull(`fi`.`image`, concat("za/", `f`.`identifier`, ".png")) as `icon`,
    ifnull(`vgp`.`sprite`, concat("za/", `f`.`identifier`, ".png")) as `sprite`,
    ifnull(`vgp`.`type1_id`, "\\N") as `type1_id`,
    ifnull(`vgp`.`type2_id`, "\\N") as `type2_id`,
    ifnull(`vgp`.`ability1_id`, "\\N") as `ability1_id`,
    ifnull(`vgp`.`ability2_id`, "\\N") as `ability2_id`,
    ifnull(`vgp`.`ability3_id`, "\\N") as `ability3_id`,
    ifnull(`vgp`.`base_hp`, 0) as `base_hp`,
    ifnull(`vgp`.`base_atk`, 0) as `base_atk`,
    ifnull(`vgp`.`base_def`, 0) as `base_def`,
    ifnull(`vgp`.`base_spa`, 0) as `base_spa`,
    ifnull(`vgp`.`base_spd`, 0) as `base_spd`,
    ifnull(`vgp`.`base_spe`, 0) as `base_spe`,
    ifnull(`vgp`.`base_spc`, 0) as `base_spc`,
    ifnull(`vgp`.`egg_group1_id`, "\\N") as `egg_group1_id`,
    ifnull(`vgp`.`egg_group2_id`, "\\N") as `egg_group2_id`,
    ifnull(`vgp`.`base_experience`, 0) as `base_experience`,
    ifnull(`vgp`.`ev_hp`, 0) as `ev_hp`,
    ifnull(`vgp`.`ev_atk`, 0) as `ev_atk`,
    ifnull(`vgp`.`ev_def`, 0) as `ev_def`,
    ifnull(`vgp`.`ev_spa`, 0) as `ev_spa`,
    ifnull(`vgp`.`ev_spd`, 0) as `ev_spd`,
    ifnull(`vgp`.`ev_spe`, 0) as `ev_spe`,
    ifnull(`vgp`.`catch_rate`, 0) as `catch_rate`,
    ifnull(`vgp`.`base_friendship`, 0) as `base_friendship`
from `vg_forms` as `vgf`
left join `form_icons` as `fi`
    on `vgf`.`version_group_id` = `fi`.`version_group_id`
    and `vgf`.`form_id` = `fi`.`form_id`
    and `fi`.`is_female` = 0
    and `fi`.`is_right` = 0
    and `fi`.`is_shiny` = 0
inner join `forms` as `f`
    on `vgf`.`form_id` = `f`.`id`
left join `vg_pokemon` as `vgp`
    on `vgf`.`version_group_id` = `vgp`.`version_group_id`
    and `f`.`pokemon_id` = `vgp`.`pokemon_id`
order by
    `version_group_id`,
    `pokemon_id`
;

/* The updated `pokemon_moves` table. */
select
    `pm`.`version_group_id`,
    `vgf`.`form_id` as `pokemon_id`,
    `pm`.`move_id`,
    `pm`.`move_method_id`,
    `pm`.`level`,
    `pm`.`mastery_level`,
    `pm`.`sort`
from `pokemon_moves` as `pm`
inner join `forms` as `f`
    on `pm`.`pokemon_id` = `f`.`pokemon_id`
inner join `vg_forms` as `vgf`
    on `pm`.`version_group_id` = `vgf`.`version_group_id`
    and `f`.`id` = `vgf`.`form_id`
# where `vgf`.`version_group_id` in (25)
order by
    `version_group_id`,
    `pokemon_id`,
    `move_id`,
    `move_method_id`,
    `level`,
    `sort`
;

/* SANITY CHECK ON UPDATED `pokemon_moves` QUERY */
/* This should return zero results (no moves in old table not represented in new table). */
select
*
from `pokemon_moves` as `old`
where not exists (
    select
        `pm`.`version_group_id`,
        `vgf`.`form_id` as `pokemon_id`,
        `pm`.`move_id`,
        `pm`.`move_method_id`,
        `pm`.`level`,
        `pm`.`mastery_level`,
        `pm`.`sort`
    from `pokemon_moves` as `pm`
    inner join `forms` as `f`
        on `pm`.`pokemon_id` = `f`.`pokemon_id`
    inner join `vg_forms` as `vgf`
        on `pm`.`version_group_id` = `vgf`.`version_group_id`
        and `f`.`id` = `vgf`.`form_id`
    where `old`.`version_group_id` = `pm`.`version_group_id`
        and `old`.`pokemon_id` = `vgf`.`form_id`
        and `old`.`move_id` = `pm`.`move_id`
        and `old`.`move_method_id` = `pm`.`move_method_id`
        and `old`.`level` = `pm`.`level`
        and `old`.`mastery_level` = `pm`.`mastery_level`
        and `old`.`sort` = `pm`.`sort`
);
