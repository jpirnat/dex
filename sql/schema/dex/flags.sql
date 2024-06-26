create table if not exists `flags`
(
`id` tinyint unsigned not null,

`identifier` varchar(20) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
