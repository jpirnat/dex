create table if not exists `generations`
(
`id` tinyint unsigned not null,

`identifier` varchar(2) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
