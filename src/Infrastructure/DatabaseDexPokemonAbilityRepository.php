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
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabaseDexPokemonAbilityRepository implements DexPokemonAbilityRepositoryInterface
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
	 * Get the dex Pokémon abilities of this Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemonAbility[] Ordered by Pokémon ability slot.
	 */
	public function getByPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId,
		LanguageId $languageId
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
			WHERE `pa`.`generation_id` = :generation_id
				AND `pa`.`pokemon_id` = :pokemon_id
				AND `an`.`language_id` = :language_id
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability']
			);

			$dexPokemonAbilities[] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @param GenerationId $generationId
	 * @param AbilityId $abilityId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays indexed by ability id and ordered by Pokémon ability slot.
	 */
	public function getByPokemonAbility(
		GenerationId $generationId,
		AbilityId $abilityId,
		LanguageId $languageId
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
			WHERE `pa`.`generation_id` = :generation_id1
				AND `an`.`language_id` = :language_id
				AND `pa`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `generation_id` = :generation_id2
						AND `ability_id` = :ability_id
				)
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability']
			);

			$dexPokemonAbilities[$result['pokemon_id']][$result['ability_id']] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByPokemonMove(
		GenerationId $generationId,
		MoveId $moveId,
		LanguageId $languageId
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
			WHERE `pa`.`generation_id` = :generation_id1
				AND `an`.`language_id` = :language_id
				AND `pa`.`pokemon_id` IN (
					SELECT
						`pm`.`pokemon_id`
					FROM `pokemon_moves` AS `pm`
					INNER JOIN `version_groups` AS `vg`
						ON `pm`.`version_group_id` = `vg`.`id`
					WHERE `pm`.`move_id` = :move_id
						AND `vg`.`generation_id` <= :generation_id2
				)
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability']
			);

			$dexPokemonAbilities[$result['pokemon_id']][] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @param GenerationId $generationId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByGeneration(
		GenerationId $generationId,
		LanguageId $languageId
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
			WHERE `pa`.`generation_id` = :generation_id
				AND `an`.`language_id` = :language_id
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability']
			);

			$dexPokemonAbilities[$result['pokemon_id']][] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}

	/**
	 * Get all dex Pokémon abilities had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 * @param LanguageId $languageId
	 *
	 * @return DexPokemonAbility[][] Outer array indexed by Pokémon id. Inner
	 *     arrays ordered by Pokémon ability slot.
	 */
	public function getByPokemonType(
		GenerationId $generationId,
		TypeId $typeId,
		LanguageId $languageId
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
			WHERE `pa`.`generation_id` = :generation_id1
				AND `an`.`language_id` = :language_id
				AND `pa`.`pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `generation_id` = :generation_id2
						AND `type_id` = :type_id
				)
			ORDER BY `pa`.`slot`'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':language_id', $languageId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$dexPokemonAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$dexPokemonAbility = new DexPokemonAbility(
				$result['identifier'],
				$result['name'],
				(bool) $result['is_hidden_ability']
			);

			$dexPokemonAbilities[$result['pokemon_id']][] = $dexPokemonAbility;
		}

		return $dexPokemonAbilities;
	}
}
