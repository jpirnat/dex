create table if not exists `showdown_items_to_import`
(
`name` varchar(50) not null,

`item_id` smallint unsigned not null,

primary key (`name`),
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
