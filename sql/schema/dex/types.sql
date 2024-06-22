create table if not exists `types`
(
`id` tinyint unsigned not null,

`identifier` varchar(8) not null,
`category_id` tinyint unsigned null, # nullable
`symbol_icon` varchar(12) not null,
`hidden_power_index` tinyint unsigned null, # nullable
`color_code` char(7) not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`category_id`) references `categories` (`id`)
	on delete restrict
	on update cascade,
unique key (`hidden_power_index`)
) engine = InnoDB;
