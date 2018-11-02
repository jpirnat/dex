create table if not exists `characteristic_names`
(
`language_id` tinyint unsigned not null,
`characteristic_id` tinyint unsigned not null,

`name` varchar(40) not null,

primary key (
	`language_id`,
	`characteristic_id`
),
foreign key (`language_id`) references `languages` (`id`)
	on delete restrict
	on update cascade,
foreign key (`characteristic_id`) references `characteristics` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
