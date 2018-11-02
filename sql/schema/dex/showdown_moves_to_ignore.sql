create table if not exists `showdown_moves_to_ignore`
(
`name` varchar(50) not null,

`move_id` smallint unsigned null, # nullable

primary key (`name`),
foreign key (`move_id`) references `moves` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
