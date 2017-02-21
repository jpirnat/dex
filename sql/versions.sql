/*
The foundation for nearly everything else.
*/

create table if not exists `generations`
(
`generation` tinyint unsigned not null,

`identifier` varchar(8) not null,

primary key (`generation`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `version_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(16) not null,
`generation` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`generation`) references `generations` (`generation`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `versions`
(
`id` tinyint unsigned not null,

`identifier` varchar(16) not null,
`version_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


insert into `generations` (
	`id`,
	`identifier`
) values
(1, "Gen 1"),
(2, "Gen 2"),
(3, "Gen 3"),
(4, "Gen 4"),
(5, "Gen 5"),
(6, "Gen 6"),
(7, "Gen 7")
;


insert into `version_groups` (
	`id`,
	`identifier`,
	`generation`
) values
(1, "red-green", 1),
(2, "blue", 1),
(3, "yellow", 1),
(4, "red-blue", 1),
(5, "gold-silver", 2),
(6, "crystal", 2),
(7, "ruby-sapphire", 3),
(8, "firered-leafgreen", 3),
(9, "emerald", 3),
(10, "diamond-pearl", 4),
(11, "platinum", 4),
(12, "heartgold-soulsilver", 4),
(13, "black-white", 5),
(14, "black-2-white-2", 5),
(15, "x-y", 6),
(16, "omega-ruby-alpha-sapphire", 6),
(17, "sun-moon", 7)
(101, "colosseum", 3),
(102, "xd", 3)
;


insert into `versions` (
	`id`,
	`identifier`,
	`version_group_id`
)
(1, "red-jp", 1),
(2, "green-jp", 1),
(3, "blue-jp", 2),
(4, "yellow", 3),
(5, "red", 4),
(6, "blue", 4),
(7, "gold", 5),
(8, "silver", 5),
(9, "crystal", 6),
(10, "ruby", 7),
(11, "sapphire", 7),
(12, "firered", 8),
(13, "leafgreen", 8),
(14, "emerald", 9),
(15, "diamond", 10),
(16, "pearl", 10),
(17, "platinum", 11),
(18, "heartgold", 12),
(19, "soulsilver", 12),
(20, "black", 13),
(21, "white", 13),
(22, "black-2", 14),
(23, "white-2", 14),
(24, "x", 15),
(25, "y", 15),
(26, "omega-ruby", 16),
(27, "alpha-sapphire", 16),
(28, "sun", 17),
(29, "moon", 17)
(101, "colosseum", 101),
(102, "xd", 102)
;
