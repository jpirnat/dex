create table if not exists `ability_flags`
(
`id` tinyint unsigned not null,

`identifier` varchar(28) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
