create table if not exists `stats`
(
`id` tinyint unsigned not null,

`identifier` varchar(15) not null,
`is_battle_only` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
