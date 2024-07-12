create table if not exists `item_names`
(
`language_id` tinyint unsigned not null,
`item_id` smallint unsigned not null,

`name` varchar(25) not null,

primary key (
	`language_id`,
	`item_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
