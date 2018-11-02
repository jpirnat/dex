create table if not exists `z_power_effects`
(
`id` tinyint unsigned not null,

`identifier` varchar(30) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
