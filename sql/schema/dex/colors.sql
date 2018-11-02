create table if not exists `colors`
(
`id` tinyint unsigned not null,

`identifier` varchar(6) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
