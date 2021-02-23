create table if not exists `item_icons`
(
`generation_id` tinyint unsigned not null,
`item_id` smallint unsigned not null,

`icon` varchar(100) not null,

primary key (`generation_id`, `item_id`),
foreign key (`generation_id`) references `generations` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
