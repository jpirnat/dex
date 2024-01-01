create table if not exists `infliction_durations`
(
`id` tinyint unsigned not null,

`identifier` varchar(20) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
