create table if not exists `species`
(
`id` smallint unsigned not null,
`identifier` varchar(12) not null,

`base_egg_cycles` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
