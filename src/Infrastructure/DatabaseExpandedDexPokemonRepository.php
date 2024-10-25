<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\ExpandedDexPokemonAbility;
use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\ExperienceGroups\DexExperienceGroup;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\ExpandedDexPokemon;
use Jp\Dex\Domain\Pokemon\ExpandedDexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\GenderRatio;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\VgPokemonNotFoundException;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseExpandedDexPokemonRepository implements ExpandedDexPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	private function getBaseQuery() : string
	{
		return
"SELECT
	`p`.`identifier`,
	`pn`.`name`,
	`vp`.`sprite`,

	`t1`.`identifier` AS `type1_identifier`,
	`t1n`.`name` AS `type1_name`,
	`t1i`.`icon` AS `type1_icon`,

	`t2`.`identifier` AS `type2_identifier`,
	`t2n`.`name` AS `type2_name`,
	`t2i`.`icon` AS `type2_icon`,

	`a1`.`identifier` AS `ability1_identifier`,
	COALESCE(`a1d`.`name`, `a1n`.`name`) AS `ability1_name`,
	`a1d`.`description` AS `ability1_description`,

	`a2`.`identifier` AS `ability2_identifier`,
	COALESCE(`a2d`.`name`, `a2n`.`name`) AS `ability2_name`,
	`a2d`.`description` AS `ability2_description`,

	`a3`.`identifier` AS `ability3_identifier`,
	COALESCE(`a3d`.`name`, `a3n`.`name`) AS `ability3_name`,
	`a3d`.`description` AS `ability3_description`,

	`vp`.`base_hp`,
	`vp`.`base_atk`,
	`vp`.`base_def`,
	`vp`.`base_spa`,
	`vp`.`base_spd`,
	`vp`.`base_spe`,
	`vp`.`base_spc`,

	`vp`.`base_experience`,
	`vp`.`ev_hp`,
	`vp`.`ev_atk`,
	`vp`.`ev_def`,
	`vp`.`ev_spa`,
	`vp`.`ev_spd`,
	`vp`.`ev_spe`,
	`vp`.`catch_rate`,
	`vp`.`base_friendship`,

	`x`.`identifier` AS `exp_group_identifier`,
	`x`.`name` AS `exp_group_name`,
	`x`.`points` AS `exp_group_points`,

	`e1`.`identifier` AS `egg_group1_identifier`,
	`e1n`.`name` AS `egg_group1_name`,

	`e2`.`identifier` AS `egg_group2_identifier`,
	`e2n`.`name` AS `egg_group2_name`,

	`p`.`gender_ratio`,
	`s`.`egg_cycles`,
	`vg`.`steps_per_egg_cycle`
FROM `vg_pokemon` AS `vp`
INNER JOIN `pokemon` AS `p`
	ON `vp`.`pokemon_id` = `p`.`id`
INNER JOIN `pokemon_names` AS `pn`
	ON `vp`.`pokemon_id` = `pn`.`pokemon_id`

LEFT JOIN `types` AS `t1`
	ON `vp`.`type1_id` = `t1`.`id`
LEFT JOIN `type_names` AS `t1n`
	ON `pn`.`language_id` = `t1n`.`language_id`
	AND `vp`.`type1_id` = `t1n`.`type_id`
LEFT JOIN `type_icons` AS `t1i`
	ON `pn`.`language_id` = `t1i`.`language_id`
	AND `vp`.`type1_id` = `t1i`.`type_id`

LEFT JOIN `types` AS `t2`
	ON `vp`.`type2_id` = `t2`.`id`
LEFT JOIN `type_names` AS `t2n`
	ON `pn`.`language_id` = `t2n`.`language_id`
	AND `vp`.`type2_id` = `t2n`.`type_id`
LEFT JOIN `type_icons` AS `t2i`
	ON `pn`.`language_id` = `t2i`.`language_id`
	AND `vp`.`type2_id` = `t2i`.`type_id`

LEFT JOIN `abilities` AS `a1`
	ON `vp`.`ability1_id` = `a1`.`id`
LEFT JOIN `ability_names` AS `a1n`
	ON `pn`.`language_id` = `a1n`.`language_id`
	AND `vp`.`ability1_id` = `a1n`.`ability_id`
LEFT JOIN `ability_descriptions` AS `a1d`
	ON `vp`.`version_group_id` = `a1d`.`version_group_id`
	AND `pn`.`language_id` = `a1d`.`language_id`
	AND `vp`.`ability1_id` = `a1d`.`ability_id`

LEFT JOIN `abilities` AS `a2`
	ON `vp`.`ability2_id` = `a2`.`id`
LEFT JOIN `ability_names` AS `a2n`
	ON `pn`.`language_id` = `a2n`.`language_id`
	AND `vp`.`ability2_id` = `a2n`.`ability_id`
LEFT JOIN `ability_descriptions` AS `a2d`
	ON `vp`.`version_group_id` = `a2d`.`version_group_id`
	AND `pn`.`language_id` = `a2d`.`language_id`
	AND `vp`.`ability2_id` = `a2d`.`ability_id`

LEFT JOIN `abilities` AS `a3`
	ON `vp`.`ability3_id` = `a3`.`id`
LEFT JOIN `ability_names` AS `a3n`
	ON `pn`.`language_id` = `a3n`.`language_id`
	AND `vp`.`ability3_id` = `a3n`.`ability_id`
LEFT JOIN `ability_descriptions` AS `a3d`
	ON `vp`.`version_group_id` = `a3d`.`version_group_id`
	AND `pn`.`language_id` = `a3d`.`language_id`
	AND `vp`.`ability3_id` = `a3d`.`ability_id`

INNER JOIN `experience_groups` AS `x`
	ON `p`.`experience_group_id` = `x`.`id`

LEFT JOIN `egg_groups` AS `e1`
	ON `vp`.`egg_group1_id` = `e1`.`id`
LEFT JOIN `egg_group_names` AS `e1n`
	ON `pn`.`language_id` = `e1n`.`language_id`
	AND `vp`.`egg_group1_id` = `e1n`.`egg_group_id`

LEFT JOIN `egg_groups` AS `e2`
	ON `vp`.`egg_group2_id` = `e2`.`id`
LEFT JOIN `egg_group_names` AS `e2n`
	ON `pn`.`language_id` = `e2n`.`language_id`
	AND `vp`.`egg_group2_id` = `e2n`.`egg_group_id`

INNER JOIN `species` AS `s`
	ON `p`.`species_id` = `s`.`id`
INNER JOIN `version_groups` AS `vg`
	ON `vp`.`version_group_id` = `vg`.`id`
";
	}

	private function fromRecord(array $result) : ExpandedDexPokemon
	{
		$types = [];
		if ($result['type1_identifier']) {
			$types[] = new DexType(
				$result['type1_identifier'],
				$result['type1_name'],
				$result['type1_icon'],
			);
		}
		if ($result['type2_identifier']) {
			$types[] = new DexType(
				$result['type2_identifier'],
				$result['type2_name'],
				$result['type2_icon'],
			);
		}

		$abilities = [];
		if ($result['ability1_identifier']) {
			$abilities[] = new ExpandedDexPokemonAbility(
				$result['ability1_identifier'],
				$result['ability1_name'],
				$result['ability1_description'] ?? '',
				false,
			);
		}
		if ($result['ability2_identifier']) {
			$abilities[] = new ExpandedDexPokemonAbility(
				$result['ability2_identifier'],
				$result['ability2_name'],
				$result['ability2_description'] ?? '',
				false,
			);
		}
		if ($result['ability3_identifier']) {
			$abilities[] = new ExpandedDexPokemonAbility(
				$result['ability3_identifier'],
				$result['ability3_name'],
				$result['ability3_description'] ?? '',
				true,
			);
		}

		$baseStats = [];
		$bst = 0;

		$baseStats['hp'] = $result['base_hp'];
		$bst += $result['base_hp'];

		$baseStats['attack'] = $result['base_atk'];
		$bst += $result['base_atk'];

		$baseStats['defense'] = $result['base_def'];
		$bst += $result['base_def'];

		if ($result['base_spa']) {
			$baseStats['special-attack'] = $result['base_spa'];
			$bst += $result['base_spa'];
		}
		if ($result['base_spd']) {
			$baseStats['special-defense'] = $result['base_spd'];
			$bst += $result['base_spd'];
		}
		if ($result['base_spc']) {
			$baseStats['special'] = $result['base_spc'];
			$bst += $result['base_spc'];
		}
		$baseStats['speed'] = $result['base_spe'];
		$bst += $result['base_spe'];

		$evYield = [];
		$evTotal = 0;

		if ($result['ev_hp']) {
			$evYield['hp'] = $result['ev_hp'];
			$evTotal += $result['ev_hp'];
		}
		if ($result['ev_atk']) {
			$evYield['attack'] = $result['ev_atk'];
			$evTotal += $result['ev_atk'];
		}
		if ($result['ev_def']) {
			$evYield['defense'] = $result['ev_def'];
			$evTotal += $result['ev_def'];
		}
		if ($result['ev_spa']) {
			$evYield['special-attack'] = $result['ev_spa'];
			$evTotal += $result['ev_spa'];
		}
		if ($result['ev_spd']) {
			$evYield['special-defense'] = $result['ev_spd'];
			$evTotal += $result['ev_spd'];
		}
		if ($result['ev_spe']) {
			$evYield['speed'] = $result['ev_spe'];
			$evTotal += $result['ev_spe'];
		}

		$eggGroups = [];
		if ($result['egg_group1_identifier']) {
			$eggGroups[] = new DexEggGroup(
				$result['egg_group1_identifier'],
				$result['egg_group1_name'],
			);
		}
		if ($result['egg_group2_identifier']) {
			$eggGroups[] = new DexEggGroup(
				$result['egg_group2_identifier'],
				$result['egg_group2_name'],
			);
		}

		return new ExpandedDexPokemon(
			$result['identifier'],
			$result['name'],
			$result['sprite'] ?? '',
			$types,
			$abilities,
			$baseStats,
			$bst,
			$result['base_experience'],
			$evYield,
			$evTotal,
			$result['catch_rate'],
			$result['base_friendship'],
			new DexExperienceGroup(
				$result['exp_group_identifier'],
				$result['exp_group_name'],
				$result['exp_group_points'],
			),
			$eggGroups,
			new GenderRatio($result['gender_ratio']),
			$result['egg_cycles'],
			$result['egg_cycles'] * $result['steps_per_egg_cycle'],
		);
	}

	/**
	 * Get an expanded dex Pokémon by its id.
	 *
	 * @throws VgPokemonNotFoundException if no Pokémon exists with this id.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : ExpandedDexPokemon {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `vp`.`version_group_id` = :version_group_id
	AND `vp`.`pokemon_id` = :pokemon_id
	AND `pn`.`language_id` = :language_id
LIMIT 1"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			$versionGroupId = $versionGroupId->value();
			$pokemonId = $pokemonId->value();
			$languageId = $languageId->value();
			throw new VgPokemonNotFoundException(
				"No version group Pokémon exists with version group id $versionGroupId, Pokémon id $pokemonId, and language id $languageId."
			);
		}

		return $this->fromRecord($result);
	}
}
