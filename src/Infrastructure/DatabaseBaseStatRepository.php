<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabaseBaseStatRepository implements BaseStatRepositoryInterface
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
	 * Get a Pokémon's base stats by generation and Pokémon.
	 *
	 * @param GenerationId $generationId
	 * @param PokemonId $pokemonId
	 *
	 * @return StatValueContainer
	 */
	public function getByGenerationAndPokemon(
		GenerationId $generationId,
		PokemonId $pokemonId
	) : StatValueContainer {
		$stmt = $this->db->prepare(
			'SELECT
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `generation_id` = :generation_id
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = new StatValueContainer();

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStat = new StatValue(
				new StatId($result['stat_id']),
				$result['value']
			);

			$baseStats->add($baseStat);
		}

		return $baseStats;
	}

	/**
	 * Get all base stats had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @param GenerationId $generationId
	 * @param AbilityId $abilityId
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays ordered
	 *     by the generation's defined order of stats.
	 */
	public function getByPokemonAbility(
		GenerationId $generationId,
		AbilityId $abilityId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `generation_id` = :generation_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `generation_id` = :generation_id2
						AND `ability_id` = :ability_id
				)'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($generationId, $baseStats);
	}

	/**
	 * Get all base stats had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @param GenerationId $generationId
	 * @param MoveId $moveId
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays ordered
	 *     by the generation's defined order of stats.
	 */
	public function getByPokemonMove(
		GenerationId $generationId,
		MoveId $moveId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `generation_id` = :generation_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_moves` AS `pm`
					INNER JOIN `version_groups` AS `vg`
						ON `pm`.`version_group_id` = `vg`.`id`
					WHERE `pm`.`move_id` = :move_id
						AND `vg`.`generation_id` <= :generation_id2
				)'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($generationId, $baseStats);
	}

	/**
	 * Get all base stats had by Pokémon in this generation.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays ordered
	 *     by the generation's defined order of stats.
	 */
	public function getByGeneration(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `generation_id` = :generation_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($generationId, $baseStats);
	}

	/**
	 * Get all base stats had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @param GenerationId $generationId
	 * @param TypeId $typeId
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays ordered
	 *     by the generation's defined order of stats.
	 */
	public function getByPokemonType(
		GenerationId $generationId,
		TypeId $typeId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `generation_id` = :generation_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `generation_id` = :generation_id2
						AND `type_id` = :type_id
				)'
		);
		$stmt->bindValue(':generation_id1', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':generation_id2', $generationId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($generationId, $baseStats);
	}

	/**
	 * Normalize the intermediate results of this class's other methods, by 
	 * removing the inner array indexing and ordering the stats.
	 *
	 * @param GenerationId $generationId
	 * @param int[][] $baseStats Outer array indexed by Pokémon id, inner arrays
	 *     indexed by stat id.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays ordered
	 *     by the generation's defined order of stats.
	 */
	private function normalize(GenerationId $generationId, array $baseStats) : array
	{
		$statIds = StatId::getByGeneration($generationId);
		$normalized = [];

		foreach ($baseStats as $pokemonId => $statValues) {
			foreach ($statIds as $statId) {
				$normalized[$pokemonId][] = $statValues[$statId->value()] ?? 0;
			}
		}

		return $normalized;
	}
}
