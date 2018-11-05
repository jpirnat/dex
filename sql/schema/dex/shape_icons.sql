create table if not exists `shape_icons`
(
`generation_id` tinyint unsigned not null,
`shape_id` tinyint unsigned not null,

`image` varchar(23) not null,

primary key (
	`generation_id`,
	`shape_id`
),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`shape_id`) references `shapes` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
