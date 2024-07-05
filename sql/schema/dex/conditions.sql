create table if not exists `conditions`
(
`id` tinyint unsigned not null,

`identifier` varchar(10) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
