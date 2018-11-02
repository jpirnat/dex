create table if not exists `shape_icons`
(
`generation` tinyint unsigned not null,
`shape_id` tinyint unsigned not null,

`image` varchar(23) not null,

primary key (
	`generation`,
	`shape_id`
),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`shape_id`) references `shapes` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
