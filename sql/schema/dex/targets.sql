create table if not exists `targets`
(
`id` tinyint unsigned not null,

`identifier` varchar(23) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
