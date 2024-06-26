create table if not exists `move_flags`
(
`id` tinyint unsigned not null,

`identifier` varchar(20) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
