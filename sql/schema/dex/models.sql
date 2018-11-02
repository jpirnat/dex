create table if not exists `models`
(
`form_id` smallint unsigned not null,
`is_shiny` bool not null,
`is_back` bool not null,
`is_female` bool not null,
`attacking_index` tinyint unsigned not null,

`image` varchar(50) not null,

primary key (
	`form_id`,
	`is_shiny`,
	`is_back`,
	`is_female`,
	`attacking_index`
),
foreign key (`form_id`) references `forms` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
