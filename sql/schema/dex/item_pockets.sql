create table if not exists `item_pockets`
(
`id` tinyint unsigned not null,

`identifier` varchar(12) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
