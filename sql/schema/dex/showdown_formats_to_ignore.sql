create table if not exists `showdown_formats_to_ignore`
(
`month` date not null,
`name` varchar(50) not null,

`format_id` tinyint unsigned null, # nullable
`reason` varchar(30) not null,

primary key (
	`month`,
	`name`
),
foreign key (`format_id`) references `formats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
