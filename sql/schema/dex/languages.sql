create table if not exists `languages`
(
`id` tinyint unsigned not null,

`identifier` varchar(20) not null,
`locale` varchar(7) not null,
`date_format` varchar(9) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
