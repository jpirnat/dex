create table if not exists `abilities`
(
`id` smallint unsigned not null,

`identifier` varchar(30) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
