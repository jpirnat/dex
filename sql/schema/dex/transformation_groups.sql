create table if not exists `transformation_groups`
(
`id` smallint unsigned not null,

`identifier` varchar(10) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;
