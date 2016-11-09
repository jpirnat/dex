use `trendalyzer`;


insert into `formats` (
	`id`,
	`name`,
	`generation`,
	`level`,
	`field_size`,
	`team_size`,
	`in_battle_team_size`
) values
(1, "Smogon Gen 6 Anything Goes", 6, 100, 1, 6, 6),
(2, "Smogon Gen 6 Ubers", 6, 100, 1, 6, 6),
(3, "Smogon Gen 6 OU", 6, 100, 1, 6, 6),
(4, "Smogon Gen 6 UU", 6, 100, 1, 6, 6),
(5, "Smogon Gen 6 RU", 6, 100, 1, 6, 6),
(6, "Smogon Gen 6 NU", 6, 100, 1, 6, 6),
(7, "Smogon Gen 6 PU", 6, 100, 1, 6, 6),

(8, "Smogon Gen 6 Doubles Ubers", 6, 100, 2, 6, 6),
(9, "Smogon Gen 6 Doubles OU", 6, 100, 2, 6, 6),
(10, "Smogon Gen 6 Doubles UU", 6, 100, 2, 6, 6),

(11, "VGC 2014", 6, 50, 2, 6, 4),
(12, "VGC 2015", 6, 50, 2, 6, 4),
(13, "VGC 2016", 6, 50, 2, 6, 4),
(14, "VGC 2017", 7, 50, 2, 6, 4)
;


insert into `smogon_format_names` (
	`name`,
	`format_id`
) values
("anythinggoes", 1),
("ubers", 2),
("ou", 3),
("uu", 4),
("ru", 5),
("nu", 6),
("pu", 7),

("smogondoublesubers", 8),
("doublesubers", 8),
("smogondoubles", 9),
("doublesou", 9),
("smogondoublesuu", 10),
("doublesuu", 10),

("vgc2014", 11),
("vgc2015", 12),
("vgc2016", 13),
("vgc2017" 14)
;
