create table if not exists `natures`
(
`id` tinyint unsigned not null,

`identifier` varchar(7) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
