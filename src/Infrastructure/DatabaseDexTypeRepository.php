<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\DexType;
use Jp\Dex\Domain\Types\DexTypeRepositoryInterface;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Types\TypeNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseDexTypeRepository implements DexTypeRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a dex type by its id.
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 */
	public function getById(
		TypeId $typeId,
		LanguageId $languageId,
	) : DexType {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`identifier`,
				`tn`.`name`,
				`ti`.`icon`
			FROM `types` AS `t`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
				AND `tn`.`language_id` = `ti`.`language_id`
			WHERE `t`.`id` = :type_id
				AND `tn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TypeNotFoundException(
				'No type exists with id ' . $typeId->value() . '.'
			);
		}

		return new DexType(
			$typeId,
			$result['identifier'],
			$result['name'],
			$result['icon'] ?? '',
		);
	}

	/**
	 * Get the dex types of this Pokémon.
	 *
	 * @return DexType[] Ordered by Pokémon type slot.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`id`,
				`t`.`identifier`,
				`tn`.`name`,
				`ti`.`icon`
			FROM `pokemon_types` AS `pt`
			INNER JOIN `types` AS `t`
				ON `pt`.`type_id` = `t`.`id`
			INNER JOIN `type_names` AS `tn`
				ON `pt`.`type_id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `pt`.`type_id` = `ti`.`type_id`
				AND `tn`.`language_id` = `ti`.`language_id`
			WHERE `pt`.`version_group_id` = :version_group_id
				AND `pt`.`pokemon_id` = :pokemon_id
				AND `tn`.`language_id` = :language_id
			ORDER BY `pt`.`slot`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexType = new DexType(
				new TypeId($result['id']),
				$result['identifier'],
				$result['name'],
				$result['icon'] ?? '',
			);

			$dexTypes[] = $dexType;
		}

		return $dexTypes;
	}

	/**
	 * Get the main dex types available in this version group.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getMainByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`id`,
				`t`.`identifier`,
				`tn`.`name`,
				`ti`.`icon`
			FROM `types` AS `t`
			INNER JOIN `vg_types` AS `vgt`
				ON `t`.`id` = `vgt`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
				AND `tn`.`language_id` = `ti`.`language_id`
			WHERE `t`.`id` < 18
				AND `vgt`.`version_group_id` = :version_group_id
				AND `tn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexType = new DexType(
				new TypeId($result['id']),
				$result['identifier'],
				$result['name'],
				$result['icon'] ?? '',
			);

			$dexTypes[$result['id']] = $dexType;
		}

		return $dexTypes;
	}

	/**
	 * Get all dex types available in this version group.
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getByVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`id`,
				`t`.`identifier`,
				`tn`.`name`,
				`ti`.`icon`
			FROM `types` AS `t`
			INNER JOIN `vg_types` AS `vgt`
				ON `t`.`id` = `vgt`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
			LEFT JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
				AND `tn`.`language_id` = `ti`.`language_id`
			WHERE `vgt`.`version_group_id` = :version_group_id
				AND `tn`.`language_id` = :language_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexType = new DexType(
				new TypeId($result['id']),
				$result['identifier'],
				$result['name'],
				$result['icon'] ?? '',
			);

			$dexTypes[$result['id']] = $dexType;
		}

		return $dexTypes;
	}

	/**
	 * Get all dex types had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
		LanguageId $languageId,
	) : array {
		$dexTypes = $this->getByVersionGroup($versionGroupId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `version_group_id` = :version_group_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `version_group_id` = :version_group_id2
						AND `ability_id` = :ability_id
				)
			ORDER BY `slot`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonTypes[$result['pokemon_id']][] = $dexTypes[$result['type_id']];
		}

		return $pokemonTypes;
	}

	/**
	 * Get all dex types had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
		LanguageId $languageId,
	) : array {
		$dexTypes = $this->getByVersionGroup($versionGroupId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `version_group_id` = :version_group_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_moves`
					WHERE `move_id` = :move_id
						AND `version_group_id` <= :version_group_id2
				)
			ORDER BY `slot`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonTypes[$result['pokemon_id']][] = $dexTypes[$result['type_id']];
		}

		return $pokemonTypes;
	}

	/**
	 * Get all dex types had by Pokémon in this version group.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonVersionGroup(
		VersionGroupId $versionGroupId,
		LanguageId $languageId,
	) : array {
		$dexTypes = $this->getByVersionGroup($versionGroupId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `version_group_id` = :version_group_id
			ORDER BY `slot`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonTypes[$result['pokemon_id']][] = $dexTypes[$result['type_id']];
		}

		return $pokemonTypes;
	}

	/**
	 * Get all dex types had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
		LanguageId $languageId,
	) : array {
		$dexTypes = $this->getByVersionGroup($versionGroupId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `version_group_id` = :version_group_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `version_group_id` = :version_group_id2
						AND `type_id` = :type_id
				)
			ORDER BY `slot`'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonTypes[$result['pokemon_id']][] = $dexTypes[$result['type_id']];
		}

		return $pokemonTypes;
	}
}
