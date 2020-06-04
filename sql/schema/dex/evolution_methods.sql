create table if not exists `evolution_methods`
(
`id` tinyint unsigned not null,

`identifier` varchar(255) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
