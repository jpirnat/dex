create table if not exists `moves`
(
`id` smallint unsigned not null,

`identifier` varchar(31) not null,
`move_type` varchar(5) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
