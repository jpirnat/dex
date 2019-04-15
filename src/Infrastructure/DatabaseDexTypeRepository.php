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
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabaseDexTypeRepository implements DexTypeRepositoryInterface
{
	/** @var PDO $db */
	private $db;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 */
	public function __construct(PDO $db)
	{
		$this->db = $db;
	}

	/**
	 * Get a dex type by its id.
	 *
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @throws TypeNotFoundException if no type exists with this id.
	 *
	 * @return DexType
	 */
	public function getById(
		TypeId $typeId,
		LanguageId $languageId
	) : DexType {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`id`,
				`t`.`identifier`,
				`ti`.`icon`,
				`tn`.`name`
			FROM `types` AS `t`
			INNER JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
				AND `ti`.`language_id` = `tn`.`language_id`
			WHERE `t`.`id` = :type_id
				AND `ti`.`language_id` = :language_id'
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

		$dexType = new DexType(
			$result['identifier'],
			$result['icon'],
			$result['name']
		);

		return $dexType;
	}

	/**
	 * Get the dex types of this Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[] Ordered by Pokémon type slot.
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`identifier`,
				`ti`.`icon`,
				`tn`.`name`
			FROM `pokemon_types` AS `pt`
			INNER JOIN `types` AS `t`
				ON `pt`.`type_id` = `t`.`id`
			INNER JOIN `type_icons` AS `ti`
				ON `pt`.`type_id` = `ti`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `pt`.`type_id` = `tn`.`type_id`
				AND `ti`.`language_id` = `tn`.`language_id`
			WHERE `pt`.`generation_id` = :generation_id
				AND `pt`.`pokemon_id` = :pokemon_id
				AND `ti`.`language_id` = :language_id
			ORDER BY `pt`.`slot` ASC'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexType = new DexType(
				$result['identifier'],
				$result['icon'],
				$result['name']
			);

			$dexTypes[] = $dexType;
		}

		return $dexTypes;
	}

	/**
	 * Get all dex types available in this generation.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[] Indexed by type id.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`t`.`id`,
				`t`.`identifier`,
				`ti`.`icon`,
				`tn`.`name`
			FROM `types` AS `t`
			INNER JOIN `type_icons` AS `ti`
				ON `t`.`id` = `ti`.`type_id`
			INNER JOIN `type_names` AS `tn`
				ON `t`.`id` = `tn`.`type_id`
				AND `ti`.`language_id` = `tn`.`language_id`
			WHERE `t`.`introduced_in_generation_id` <= :generation_id
				AND `ti`.`language_id` = :language_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexType = new DexType(
				$result['identifier'],
				$result['icon'],
				$result['name']
			);

			$dexTypes[$result['id']] = $dexType;
		}

		return $dexTypes;
	}

	/**
	 * Get all dex types had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @param GenerationId $generationId
	 * @param AbilityId $abilityId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonAbility(
		GenerationId $generationId,
		AbilityId $abilityId,
		LanguageId $languageId
	) : array {
		$dexTypes = $this->getByGeneration($generationId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `generation_id` = :generation_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `generation_id` = :generation_id2
						AND `ability_id` = :ability_id
				)
			ORDER BY `slot` ASC'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
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
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonMove(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
	) : array {
		$dexTypes = $this->getByGeneration($generationId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `generation_id` = :generation_id1
				AND `pokemon_id` IN (
					SELECT
						`pm`.`pokemon_id`
					FROM `pokemon_moves` AS `pm`
					INNER JOIN `version_groups` AS `vg`
						ON `pm`.`version_group_id` = `vg`.`id`
					WHERE `pm`.`move_id` = :move_id
						AND `vg`.`generation_id` <= :generation_id2
				)
			ORDER BY `slot` ASC'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonTypes[$result['pokemon_id']][] = $dexTypes[$result['type_id']];
		}

		return $pokemonTypes;
	}

	/**
	 * Get all dex types had by Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonGeneration(
		GenerationId $generationId,
		LanguageId $languageId
	) : array {
		$dexTypes = $this->getByGeneration($generationId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `generation_id` = :generation_id
			ORDER BY `slot` ASC'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
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
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexType[][] Outer array indexed by Pokémon id. Inner arrays
	 *     ordered by Pokémon type slot.
	 */
	public function getByPokemonType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
	) : array {
		$dexTypes = $this->getByGeneration($generationId, $languageId);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`type_id`
			FROM `pokemon_types`
			WHERE `generation_id` = :generation_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `generation_id` = :generation_id2
						AND `type_id` = :type_id
				)
			ORDER BY `slot` ASC'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$pokemonTypes = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$pokemonTypes[$result['pokemon_id']][] = $dexTypes[$result['type_id']];
		}

		return $pokemonTypes;
	}
}
