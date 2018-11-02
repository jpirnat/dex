create table if not exists `showdown_natures_to_ignore`
(
`name` varchar(50) not null,

`nature_id` tinyint unsigned null, # nullable

primary key (`name`),
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
