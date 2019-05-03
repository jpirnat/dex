create table if not exists `category_names`
(
`language_id` tinyint unsigned not null,
`category_id` tinyint unsigned not null,

`name` varchar(9) not null,

primary key (
	`language_id`,
	`category_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`category_id`) references `categories` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
