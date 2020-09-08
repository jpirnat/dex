create table if not exists `targets`
(
`id` tinyint unsigned not null,

`identifier` varchar(30) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
