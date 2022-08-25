create table if not exists `z_move_images`
(
`language_id` tinyint unsigned not null,
`move_id` smallint unsigned not null,

`image` varchar(35) not null,

primary key (
	`language_id`,
	`move_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
