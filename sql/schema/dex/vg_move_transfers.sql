create table if not exists `vg_move_transfers`
(
`into_vg_id` tinyint unsigned not null,
`from_vg_id` tinyint unsigned not null,

primary key (`into_vg_id`, `from_vg_id`),
foreign key (`into_vg_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade,
foreign key (`from_vg_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
