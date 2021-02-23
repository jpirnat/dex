# Porydex

Porydex is a combined Pokédex and Pokémon Showdown competitive usage stats
viewer, designed to make it easy to browse and analyze metagame trends over
time.

## Getting Started

Porydex requires the following to run:
* PHP 8.0+
    * extension `intl`
* MySQL 5.6+

### Installation

1. Create the database schema (and user) for the project. Give them whatever
names you want. For example,
```MySQL
create schema `dex`
	charset utf8mb4
	collate utf8mb4_unicode_520_ci # Use the best modern collation you have access to.
;
create user `dex_user`@'127.0.0.1' identified by 'password';
# Don't forget the grants!
```

2. Create a file `/config/.env`. Fill it with the database credentials you just
created. Follow the template in `/config/.env.example`.

3. Run the script `php /bin/setup` to setup the database and load all non-stats
data.

4. Run the script `php /bin/import --year=2018 --month=1` to import all
importable data in https://www.smogon.com/stats/2018-01/ (or any other month).
This can take a while.

### Importing Usage Data

"Importable data" is determined by the contents of these database tables:
* `showdown_abilities_to_ignore`
* `showdown_abilities_to_import`
* `showdown_formats_to_ignore`
* `showdown_formats_to_import`
* `showdown_items_to_ignore`
* `showdown_items_to_import`
* `showdown_moves_to_ignore`
* `showdown_moves_to_import`
* `showdown_natures_to_ignore`
* `showdown_natures_to_import`
* `showdown_pokemon_to_ignore`
* `showdown_pokemon_to_import`

The `showdown_x_to_import` tables contain the names of entities to import, along
with their database ids (example: "Bulbasaur": 1). The `showdown_x_to_ignore`
tables contain the names of entities to NOT import (examples: "Missingno", CAP
Pokémon/abilities). When the `/bin/import` script is run, if it encounters an
entity that is neither imported nor ignored, the script crashes. This is
intentional.

Before `import`ing a month's data, you should first run the script
`php /bin/parse --year=2018 --month=1` on that month. It will give you a list of
all unknown entities in that month's usage data files. Add those entities to
the appropriate `showdown_x_to_x` tables, and then you can `import` safely.

A new month's formats must always be added to the `showdown_formats_to_x`
tables. This is necessary because formats have changed names before ("ou" to
"gen6ou"), and we don't want to import such changes as separate formats.

The main implementation of Porydex only imports the official Smogon and VGC
formats. If you want to run a clone of Porydex that focuses on (for example) LC
or CAP, you can! Just edit the database accordingly.

## Other

This README is a work in progress. I know I still need contributing guidelines
and a license statement. For now, use your best judgement.
