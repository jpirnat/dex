create table if not exists `egg_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
