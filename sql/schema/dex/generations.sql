create table if not exists `generations`
(
`generation` tinyint unsigned not null,

`identifier` varchar(15) not null,

primary key (`generation`),
unique key (`identifier`)
) engine = InnoDB;
