create table if not exists `showdown_natures_to_import`
(
`name` varchar(50) not null,

`nature_id` tinyint unsigned not null,

primary key (`name`),
foreign key (`nature_id`) references `natures` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;
