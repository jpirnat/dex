create table if not exists `showdown_abilities_to_ignore`
(
`name` varchar(50) not null,

`ability_id` smallint unsigned null, # nullable

primary key (`name`),
foreign key (`ability_id`) references `abilities` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
