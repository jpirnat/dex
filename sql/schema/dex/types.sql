create table if not exists `types`
(
`id` tinyint unsigned not null,

`identifier` varchar(8) not null,
`introduced_in_generation` tinyint unsigned not null,
`category_id` tinyint unsigned null, # nullable
`hidden_power_index` tinyint unsigned null, # nullable
`color_code` char(7) not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`introduced_in_generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade,
foreign key (`category_id`) references `categories` (`id`)
	on delete restrict
	on update cascade,
unique key (`hidden_power_index`)
) engine = InnoDB;
