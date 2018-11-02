create table if not exists `showdown_items_to_ignore`
(
`name` varchar(50) not null,

`item_id` smallint unsigned null, # nullable

primary key (`name`),
foreign key (`item_id`) references `items` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
