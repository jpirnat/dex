create table if not exists `move_methods`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,
`sort` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
