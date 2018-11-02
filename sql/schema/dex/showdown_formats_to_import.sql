create table if not exists `showdown_formats_to_import`
(
`month` date not null,
`name` varchar(50) not null,

`format_id` tinyint unsigned not null,

primary key (
	`month`,
	`name`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
