create table if not exists `item_fling_effects`
(
`id` tinyint unsigned not null,

`identifier` varchar(9) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
