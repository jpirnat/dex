create table if not exists `inflictions`
(
`id` tinyint unsigned not null,

`identifier` varchar(11) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
