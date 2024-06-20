<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\DexPokemonAbility;
use Jp\Dex\Domain\Abilities\DexPokemonAbilityRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseDexPokemonAbilityRepository implements DexPokemonAbilityRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get the dex Pokémon abilities of this Pokémon.
	 *
	 * @return DexPokemonAbility[] Ordered by Pokémon ability slot.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`a`.`identifier`,
				`an`.`name`,
				`pa`.`is_hidden_ability`
			FROM `pokemon_abilities` AS `pa`
			INNER JOIN `abilities` AS `a`
				ON `pa`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `pa`.`ability_id` = `an`.`ability_id`
			WHERE `pa`.`version_group_id` = :version_group_id
				AND `pa`.`pokemon_id` = :pokemon_id
				AND `an`.`language_id` = :language_id
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability'],
			);

			$dexPokemonAbilities[] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays indexed by ability id and ordered by Pokémon ability slot.
	 */
	public function getByPokemonAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pa`.`pokemon_id`,
				`pa`.`ability_id`,
				`a`.`identifier`,
				`an`.`name`,
				`pa`.`is_hidden_ability`
			FROM `pokemon_abilities` AS `pa`
			INNER JOIN `abilities` AS `a`
				ON `pa`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `pa`.`ability_id` = `an`.`ability_id`
			WHERE `pa`.`version_group_id` = :version_group_id1
				AND `an`.`language_id` = :language_id
				AND `pa`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `version_group_id` = :version_group_id2
						AND `ability_id` = :ability_id
				)
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability'],
			);

			$dexPokemonAbilities[$result['pokemon_id']][$result['ability_id']] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByPokemonMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pa`.`pokemon_id`,
				`a`.`identifier`,
				`an`.`name`,
				`pa`.`is_hidden_ability`
			FROM `pokemon_abilities` AS `pa`
			INNER JOIN `abilities` AS `a`
				ON `pa`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `pa`.`ability_id` = `an`.`ability_id`
			WHERE `pa`.`version_group_id` = :version_group_id1
				AND `an`.`language_id` = :language_id
				AND `pa`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_moves`
					WHERE `move_id` = :move_id
						AND `version_group_id` <= :version_group_id2
				)
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability'],
			);

			$dexPokemonAbilities[$result['pokemon_id']][] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon in this version group.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pa`.`pokemon_id`,
				`a`.`identifier`,
				`an`.`name`,
				`pa`.`is_hidden_ability`
			FROM `pokemon_abilities` AS `pa`
			INNER JOIN `abilities` AS `a`
				ON `pa`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `pa`.`ability_id` = `an`.`ability_id`
			WHERE `pa`.`version_group_id` = :version_group_id
				AND `an`.`language_id` = :language_id
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability'],
			);

			$dexPokemonAbilities[$result['pokemon_id']][] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByPokemonType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pa`.`pokemon_id`,
				`a`.`identifier`,
				`an`.`name`,
				`pa`.`is_hidden_ability`
			FROM `pokemon_abilities` AS `pa`
			INNER JOIN `abilities` AS `a`
				ON `pa`.`ability_id` = `a`.`id`
			INNER JOIN `ability_names` AS `an`
				ON `pa`.`ability_id` = `an`.`ability_id`
			WHERE `pa`.`version_group_id` = :version_group_id1
				AND `an`.`language_id` = :language_id
				AND `pa`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `version_group_id` = :version_group_id2
						AND `type_id` = :type_id
				)
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability'],
			);

			$dexPokemonAbilities[$result['pokemon_id']][] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}
}
