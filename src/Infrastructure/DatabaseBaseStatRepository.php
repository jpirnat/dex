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
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseBaseStatRepository implements BaseStatRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a Pokémon's base stats by version group and Pokémon.
	 */
	public function getByPokemon(
		VersionGroupId $versionGroupId,
		PokemonId $pokemonId,
	) : StatValueContainer {
		$stmt = $this->db->prepare(
			'SELECT
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `version_group_id` = :version_group_id
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = new StatValueContainer();

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStat = new StatValue(
				new StatId($result['stat_id']),
				$result['value'],
			);

			$baseStats->add($baseStat);
		}

		return $baseStats;
	}

	/**
	 * Get all base stats had by Pokémon with this ability.
	 * This method is used to get data for the dex ability page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonAbility(
		VersionGroupId $versionGroupId,
		AbilityId $abilityId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `version_group_id` = :version_group_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_abilities`
					WHERE `version_group_id` = :version_group_id2
						AND `ability_id` = :ability_id
				)'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($versionGroupId, $baseStats);
	}

	/**
	 * Get all base stats had by Pokémon with this move.
	 * This method is used to get data for the dex move page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `version_group_id` = :version_group_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_moves`
					WHERE `move_id` = :move_id
						AND `version_group_id` <= :version_group_id2
				)'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($versionGroupId, $baseStats);
	}

	/**
	 * Get all base stats had by Pokémon in this version group.
	 * This method is used to get data for the dex Pokémons page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByVersionGroup(VersionGroupId $versionGroupId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `version_group_id` = :version_group_id'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($versionGroupId, $baseStats);
	}

	/**
	 * Get all base stats had by Pokémon with this type.
	 * This method is used to get data for the dex type page.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	public function getByPokemonType(
		VersionGroupId $versionGroupId,
		TypeId $typeId,
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`stat_id`,
				`value`
			FROM `base_stats`
			WHERE `version_group_id` = :version_group_id1
				AND `pokemon_id` IN (
					SELECT
						`pokemon_id`
					FROM `pokemon_types`
					WHERE `version_group_id` = :version_group_id2
						AND `type_id` = :type_id
				)'
		);
		$stmt->bindValue(':version_group_id1', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':version_group_id2', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$baseStats = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$baseStats[$result['pokemon_id']][$result['stat_id']] = $result['value'];
		}

		return $this->normalize($versionGroupId, $baseStats);
	}

	/**
	 * Normalize the intermediate results of this class's other methods, by 
	 * removing the inner array indexing and ordering the stats.
	 *
	 * @param int[][] $baseStats Outer array indexed by Pokémon id, inner arrays
	 *     indexed by stat id.
	 *
	 * @return int[][] Outer array indexed by Pokémon id. Inner arrays indexed
	 *     by each stat's json identifier.
	 */
	private function normalize(VersionGroupId $versionGroupId, array $baseStats) : array
	{
		$statIds = StatId::getByVersionGroup($versionGroupId);
		$idsToIdentifiers = StatId::getIdsToIdentifiers();
		$normalized = [];

		foreach ($baseStats as $pokemonId => $statValues) {
			foreach ($statIds as $statId) {
				$identifier = $idsToIdentifiers[$statId->value()];
				$normalized[$pokemonId][$identifier] = $statValues[$statId->value()] ?? 0;
			}
		}

		return $normalized;
	}
}
