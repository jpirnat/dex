<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityFlagId;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Moves\MoveFlagId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Types\TypeId;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;
use PDO;
use PDOStatement;

final readonly class DatabaseVersionGroupRepository implements VersionGroupRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	private function getBaseQuery() : string
	{
		return
			"SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_breeding`,
				`steps_per_egg_cycle`,
				`stat_formula_type`,
				`has_iv_based_stats`,
				`max_iv`,
				`has_iv_based_hidden_power`,
				`has_ev_based_stats`,
				`has_ev_yields`,
				`max_evs_per_stat`,
				`has_abilities`,
				`has_natures`,
				`has_characteristics`,
				`sort`
			FROM `version_groups`
			";
	}

	/**
	 * @return VersionGroup[] Indexed by id.
	 */
	private function executeAndFetch(PDOStatement $stmt) : array
	{
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroups[$result['id']] = $this->fromRecord($result);
		}

		return $versionGroups;
	}

	private function fromRecord(array $result) : VersionGroup
	{
		return new VersionGroup(
			new VersionGroupId($result['id']),
			$result['identifier'],
			new GenerationId($result['generation_id']),
			$result['abbreviation'],
			(bool) $result['has_breeding'],
			$result['steps_per_egg_cycle'],
			$result['stat_formula_type'],
			$result['max_iv'],
			(bool) $result['has_iv_based_hidden_power'],
			(bool) $result['has_ev_based_stats'],
			(bool) $result['has_ev_yields'],
			$result['max_evs_per_stat'],
			(bool) $result['has_abilities'],
			(bool) $result['has_natures'],
			(bool) $result['has_characteristics'],
			$result['sort'],
		);
	}

	/**
	 * Get a version group by its id.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 */
	public function getById(VersionGroupId $versionGroupId) : VersionGroup
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` = :version_group_id
			LIMIT 1"
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			$versionGroupId = $versionGroupId->value();
			throw new VersionGroupNotFoundException(
				"No version group exists with id $versionGroupId."
			);
		}

		return $this->fromRecord($result);
	}

	/**
	 * Get a version group by its identifier.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 */
	public function getByIdentifier(string $identifier) : VersionGroup
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `identifier` = :identifier
			LIMIT 1"
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new VersionGroupNotFoundException(
				"No version group exists with identifier $identifier."
			);
		}

		return $this->fromRecord($result);
	}

	/**
	 * Get version groups since this generation.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getSinceGeneration(GenerationId $generationId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `generation_id` >= :generation_id
			ORDER BY `sort`"
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have this Pokémon.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithPokemon(PokemonId $pokemonId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_pokemon`
				WHERE `pokemon_id` = :pokemon_id
			)
			ORDER BY `sort`"
		);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have this move.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithMove(MoveId $moveId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_moves`
				WHERE `move_id` = :move_id
					AND `can_use_move` = 1
			)
			ORDER BY `sort`"
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have this move flag.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithMoveFlag(MoveFlagId $flagId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_move_flags`
				WHERE `flag_id` = :flag_id
					AND `is_functional` = 1
			)
			ORDER BY `sort`"
		);
		$stmt->bindValue(':flag_id', $flagId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have this type.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithType(TypeId $typeId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_types`
				WHERE `type_id` = :type_id
			)
			ORDER BY `sort`"
		);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have this item.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithItem(ItemId $itemId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_items`
				WHERE `item_id` = :item_id
					AND `is_available` = 1
			)
			ORDER BY `sort`"
		);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have abilities.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithAbilities() : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `has_abilities` = 1
			ORDER BY `sort`"
		);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have this ability.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithAbility(AbilityId $abilityId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `pokemon_abilities`
				WHERE `ability_id` = :ability_id
			)
			ORDER BY `sort`"
		);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have this ability flag.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithAbilityFlag(AbilityFlagId $flagId) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_ability_flags`
				WHERE `flag_id` = :flag_id
					AND `is_functional` = 1
			)
			ORDER BY `sort`"
		);
		$stmt->bindValue(':flag_id', $flagId->value(), PDO::PARAM_INT);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have EVs.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithNatures() : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `has_natures` = 1
			ORDER BY `sort`"
		);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have TMs.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithTms() : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `technical_machines`
			)
			ORDER BY `sort`"
		);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that have breeding.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithBreeding() : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `has_breeding` = 1
			ORDER BY `sort`"
		);
		return $this->executeAndFetch($stmt);
	}

	/**
	 * Get version groups that use these stat formulas.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithStatFormulaType(string $statFormulaType) : array
	{
		$baseQuery = $this->getBaseQuery();
		$stmt = $this->db->prepare(
			"$baseQuery
			WHERE `stat_formula_type` = :stat_formula_type
			ORDER BY `sort`"
		);
		$stmt->bindValue(':stat_formula_type', $statFormulaType);
		return $this->executeAndFetch($stmt);
	}
}
