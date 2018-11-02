create table if not exists `version_group_languages`
(
`version_group_id` tinyint unsigned not null,
`language_id` tinyint unsigned not null,

primary key (
	`version_group_id`,
	`language_id`
)
) engine = InnoDB;
