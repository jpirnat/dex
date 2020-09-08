create table if not exists `infliction_names`
(
`language_id` tinyint unsigned not null,
`infliction_id` tinyint unsigned not null,

`name` varchar(20) not null,

primary key (`language_id`, `infliction_id`),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`infliction_id`) references `inflictions` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
