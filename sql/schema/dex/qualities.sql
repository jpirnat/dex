create table if not exists `qualities`
(
`id` tinyint unsigned not null,

`identifier` varchar(44) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
