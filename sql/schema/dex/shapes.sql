create table if not exists `shapes`
(
`id` tinyint unsigned not null,

`identifier` varchar(16) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
