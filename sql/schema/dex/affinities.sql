create table if not exists `affinities`
(
`id` tinyint unsigned not null,

`identifier` varchar(8) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
