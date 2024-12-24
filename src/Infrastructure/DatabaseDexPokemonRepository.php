<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\EggGroups\DexEggGroup;
use Jp\Dex\Domain\EggGroups\EggGroupId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\DexPokemon;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\GenderRatio;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Pokemon\VgPokemonNotFoundException;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;
use PDOStatement;

final readonly class DatabaseDexPokemonRepository implements DexPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	public function getBaseQuery() : string
	{
		return
"SELECT
	`p`.`id`,
	`vp`.`icon`,
	`p`.`identifier`,
	`pn`.`name`,

	`t1`.`identifier` AS `type1_identifier`,
	`t1n`.`name` AS `type1_name`,
	`t1i`.`icon` AS `type1_icon`,

	`t2`.`identifier` AS `type2_identifier`,
	`t2n`.`name` AS `type2_name`,
	`t2i`.`icon` AS `type2_icon`,

	`a1`.`identifier` AS `ability1_identifier`,
	`a1n`.`name` AS `ability1_name`,

	`a2`.`identifier` AS `ability2_identifier`,
	`a2n`.`name` AS `ability2_name`,

	`a3`.`identifier` AS `ability3_identifier`,
	`a3n`.`name` AS `ability3_name`,

	`vp`.`base_hp`,
	`vp`.`base_atk`,
	`vp`.`base_def`,
	`vp`.`base_spa`,
	`vp`.`base_spd`,
	`vp`.`base_spe`,
	`vp`.`base_spc`,

	`e1`.`identifier` AS `egg_group1_identifier`,
	`e1n`.`name` AS `egg_group1_name`,

	`e2`.`identifier` AS `egg_group2_identifier`,
	`e2n`.`name` AS `egg_group2_name`,

	`p`.`gender_ratio`,
	`s`.`egg_cycles`,
	`vg`.`steps_per_egg_cycle`,

	`vp`.`ev_hp`,
	`vp`.`ev_atk`,
	`vp`.`ev_def`,
	`vp`.`ev_spa`,
	`vp`.`ev_spd`,
	`vp`.`ev_spe`,

	`p`.`sort`
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

LEFT JOIN `abilities` AS `a2`
	ON `vp`.`ability2_id` = `a2`.`id`
LEFT JOIN `ability_names` AS `a2n`
	ON `pn`.`language_id` = `a2n`.`language_id`
	AND `vp`.`ability2_id` = `a2n`.`ability_id`

LEFT JOIN `abilities` AS `a3`
	ON `vp`.`ability3_id` = `a3`.`id`
LEFT JOIN `ability_names` AS `a3n`
	ON `pn`.`language_id` = `a3n`.`language_id`
	AND `vp`.`ability3_id` = `a3n`.`ability_id`

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

	/**
	 * @return DexPokemon[] Indexed by id.
	 */
	public function executeAndFetch(PDOStatement $stmt) : array
	{
		$stmt->execute();

		$pokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemons[$result['id']] = $this->fromRecord($result);
		}

		return $pokemons;
	}

	private function fromRecord(array $result) : DexPokemon
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
			$abilities[] = new DexPokemonAbility(
				$result['ability1_identifier'],
				$result['ability1_name'],
				false,
			);
		}
		if ($result['ability2_identifier']) {
			$abilities[] = new DexPokemonAbility(
				$result['ability2_identifier'],
				$result['ability2_name'],
				false,
			);
		}
		if ($result['ability3_identifier']) {
			$abilities[] = new DexPokemonAbility(
				$result['ability3_identifier'],
				$result['ability3_name'],
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

		$evYield = [];
		if ($result['ev_hp']) {
			$evYield['hp'] = $result['ev_hp'];
		}
		if ($result['ev_atk']) {
			$evYield['attack'] = $result['ev_atk'];
		}
		if ($result['ev_def']) {
			$evYield['defense'] = $result['ev_def'];
		}
		if ($result['ev_spa']) {
			$evYield['special-attack'] = $result['ev_spa'];
		}
		if ($result['ev_spd']) {
			$evYield['special-defense'] = $result['ev_spd'];
		}
		if ($result['ev_spe']) {
			$evYield['speed'] = $result['ev_spe'];
		}

		return new DexPokemon(
			$result['icon'] ?? '',
			$result['identifier'],
			$result['name'],
			$types,
			$abilities,
			$baseStats,
			$bst,
			$eggGroups,
			new GenderRatio($result['gender_ratio']),
			$result['egg_cycles'],
			$result['egg_cycles'] * $result['steps_per_egg_cycle'],
			$evYield,
			$result['sort'],
		);
	}

	/**
	 * Get a dex Pokémon by its id.
	 *
	 * @throws VgPokemonNotFoundException if no Pokémon exists with this id.
	 */
	public function getById(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : DexPokemon {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `vp`.`version_group_id` = :version_group_id
	AND `vp`.`pokemon_id` = :pokemon_id
	AND `pn`.`language_id` = :language_id
LIMIT 1"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new VgPokemonNotFoundException(
				"No version group Pokémon exists with version group id $versionGroupId->value, Pokémon id $pokemonId->value, and language id $languageId->value."
			);
		}

		return $this->fromRecord($result);
	}

	/**
	 * Get all dex Pokémon with this ability.
	 * This method is used to get data for the dex/ability page.
	 *
	 * @return DexPokemon[] Indexed by id. Ordered by Pokémon sort value.
	 */
	public function getWithAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `vp`.`version_group_id` = :version_group_id
	AND :ability_id IN (
		`vp`.`ability1_id`,
		`vp`.`ability2_id`,
		`vp`.`ability3_id`
	)
	AND `pn`.`language_id` = :language_id
ORDER BY `p`.`sort`"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get all dex Pokémon in this egg group.
	 * This method is used to get data for the dex/egg-group page.
	 *
	 * @return DexPokemon[] Indexed by id. Ordered by Pokémon sort value.
	 */
	public function getInEggGroup(
		VersionGroupId $versionGroupId,
		EggGroupId $eggGroupId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `vp`.`version_group_id` = :version_group_id
	AND :egg_group_id IN (
		`vp`.`egg_group1_id`,
		`vp`.`egg_group2_id`
	)
	AND `pn`.`language_id` = :language_id
ORDER BY `p`.`sort`"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':egg_group_id', $eggGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get all dex Pokémon with this move.
	 * This method is used to get data for the dex/move page.
	 *
	 * @return DexPokemon[] Indexed by id. Ordered by Pokémon sort value.
	 */
	public function getWithMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `vp`.`version_group_id` = :version_group_id1
	AND `vp`.`pokemon_id` IN (
		SELECT
			`pokemon_id`
		FROM `pokemon_moves`
		WHERE `version_group_id` IN (
			SELECT
				`from_vg_id`
			FROM `vg_move_transfers`
			WHERE `into_vg_id` = :version_group_id2
		)
		AND `move_id` = :move_id
	)
	AND `pn`.`language_id` = :language_id
ORDER BY `p`.`sort`"
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get all dex Pokémon with this type.
	 * This method is used to get data for the dex/type page.
	 *
	 * @return DexPokemon[] Indexed by id. Ordered by Pokémon sort value.
	 */
	public function getByType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `vp`.`version_group_id` = :version_group_id
	AND :type_id IN (
		`vp`.`type1_id`,
		`vp`.`type2_id`
	)
	AND `pn`.`language_id` = :language_id
ORDER BY `p`.`sort`"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get all dex Pokémon in this version group.
	 * This method is used to get data for the dex/pokemons page.
	 *
	 * @return DexPokemon[] Indexed by id. Ordered by Pokémon sort value.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
"$baseQuery
WHERE `vp`.`version_group_id` = :version_group_id
	AND `pn`.`language_id` = :language_id
ORDER BY `p`.`sort`"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value, PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}
}
