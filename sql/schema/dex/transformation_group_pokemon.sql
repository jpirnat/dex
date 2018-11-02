create table if not exists `transformation_group_pokemon`
(
`transformation_group_id` smallint unsigned not null,
`pokemon_id` smallint unsigned not null,

primary key (
	`transformation_group_id`,
	`pokemon_id`
)
) engine = InnoDB;
