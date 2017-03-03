/*
The foundation for nearly everything else.
*/

create table if not exists `generations`
(
`generation` tinyint unsigned not null,

`identifier` varchar(15) not null,

primary key (`generation`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `version_groups`
(
`id` tinyint unsigned not null,

`identifier` varchar(25) not null,
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

`identifier` varchar(14) not null,
`version_group_id` tinyint unsigned not null,

primary key (`id`),
unique key (`identifier`),
foreign key (`version_group_id`) references `version_groups` (`id`)
	on delete restrict
	on update cascade
) engine = InnoDB;


create table if not exists `languages`
(
`id` tinyint unsigned not null,

`identifier` varchar(20) not null,

primary key (`id`),
unique key (`identifier`)
) engine = InnoDB;


create table if not exists `version_group_languages`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`language_id`
)
) engine = InnoDB;


insert into `generations` (
	`generation`,
	`identifier`
) values
(1, "generation-i"),
(2, "generation-ii"),
(3, "generation-iii"),
(4, "generation-iv"),
(5, "generation-v"),
(6, "generation-vi"),
(7, "generation-vii")
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
(17, "sun-moon", 7),
(101, "colosseum", 3),
(102, "xd", 3)
;


insert into `versions` (
	`id`,
	`identifier`,
	`version_group_id`
) values
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
(29, "moon", 17),
(101, "colosseum", 101),
(102, "xd", 102)
;


insert into `languages` (
	`id`,
	`identifier`
) values
(1, "japanese"),
(2, "english"),
(3, "french"),
(4, "german"),
(5, "italian"),
(6, "spanish"),
(7, "korean"),
(8, "japanese-kanji"),
(9, "chinese-simplified"),
(10, "chinese-traditional")
;


insert into `version_group_languages` (
	`version_group_id`,
	`language_id`
) values
# RGB (Japanese)
(1, 1),
(2, 1),

# Yellow (International)
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),

# RB (International)
(4, 2),
(4, 3),
(4, 4),
(4, 5),
(4, 6),

# GS was also released in Korean
(5, 1),
(5, 2),
(5, 3),
(5, 4),
(5, 5),
(5, 6),
(5, 7),

# C
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),

# RS
(7, 1),
(7, 2),
(7, 3),
(7, 4),
(7, 5),
(7, 6),

# FRLG
(8, 1),
(8, 2),
(8, 3),
(8, 4),
(8, 5),
(8, 6),

# E
(9, 1),
(9, 2),
(9, 3),
(9, 4),
(9, 5),
(9, 6),

# DP re-added Korean
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 5),
(10, 6),
(10, 7),

# P
(11, 1),
(11, 2),
(11, 3),
(11, 4),
(11, 5),
(11, 6),
(11, 7),

# HGSS
(12, 1),
(12, 2),
(12, 3),
(12, 4),
(12, 5),
(12, 6),
(12, 7),

# BW added Japanese (Kanji)
(13, 1),
(13, 2),
(13, 3),
(13, 4),
(13, 5),
(13, 6),
(13, 7),
(13, 8),

# B2W2
(14, 1),
(14, 2),
(14, 3),
(14, 4),
(14, 5),
(14, 6),
(14, 7),
(14, 8),

# XY
(15, 1),
(15, 2),
(15, 3),
(15, 4),
(15, 5),
(15, 6),
(15, 7),
(15, 8),

# ORAS
(16, 1),
(16, 2),
(16, 3),
(16, 4),
(16, 5),
(16, 6),
(16, 7),
(16, 8),

# SM added Chinese
(17, 1),
(17, 2),
(17, 3),
(17, 4),
(17, 5),
(17, 6),
(17, 7),
(17, 8),
(17, 9),
(17, 10)
;
