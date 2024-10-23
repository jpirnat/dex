create table if not exists `experience_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,
`name` varchar(11) not null,
`points` mediumint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
