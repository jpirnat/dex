create table if not exists `habitats`
(
`id` tinyint unsigned not null,

`identifier` varchar(13) not null,
`image` varchar(17) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
