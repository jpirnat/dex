create table if not exists `vg_moves_legends`
(
`version_group_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`splinter_modifier` tinyint unsigned not null,
`status_fixated` bool not null,
`status_obscured` bool not null,
`status_obscured_duration` tinyint unsigned not null,
`status_primed` bool not null,
`status_primed_duration` tinyint unsigned not null,
`status_primed_percent` tinyint unsigned not null,
`status_stance_swap` bool not null,
`status_stance_swap_duration` tinyint unsigned not null,
`status_future_crit` bool not null,
`status_future_crit_duration` tinyint unsigned not null,
`status_future_crit_stage` tinyint unsigned not null,
`damage_percent_statused` tinyint unsigned not null,
`can_style` bool not null,
`act_speed_mod_user` tinyint signed not null,
`act_speed_mod_user_agile` tinyint signed not null,
`act_speed_mod_user_strong` tinyint signed not null,
`act_speed_mod_target` tinyint signed not null,
`act_speed_mod_target_agile` tinyint signed not null,
`agile_power` tinyint unsigned not null,
`agile_heal_percent` tinyint signed not null,
`agile_turn_max` tinyint unsigned not null,
`agile_effect_duration` tinyint unsigned not null,
`agile_stat_change_duration` tinyint unsigned not null,
`strong_power` tinyint unsigned not null,
`strong_accuracy` tinyint unsigned not null,
`strong_crit_stage` tinyint unsigned not null,
`strong_inflict_percent` tinyint unsigned not null,
`strong_stat1_percent` tinyint unsigned not null,
`strong_turn_max` tinyint unsigned not null,
`strong_effect_duration` tinyint unsigned not null,
`strong_stat_change_duration` tinyint unsigned not null,
`strong_recoil_percent` tinyint signed not null,
`strong_heal_percent` tinyint signed not null,
`strong_splinter_modifier` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`move_id`
),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
