create table if not exists `species_habitats`
(
`species_id` smallint unsigned not null,
`habitat_id` tinyint unsigned not null,

primary key (
	`species_id`,
	`habitat_id`
),
foreign key (`species_id`) references `species` (`id`)
	on delete restrict
	on update cascade,
foreign key (`habitat_id`) references `habitats` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
