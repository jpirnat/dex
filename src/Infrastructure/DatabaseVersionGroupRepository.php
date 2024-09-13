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

final readonly class DatabaseVersionGroupRepository implements VersionGroupRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a version group by its id.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 */
	public function getById(VersionGroupId $versionGroupId) : VersionGroup
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` = :version_group_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new VersionGroupNotFoundException(
				'No version group exists with id ' . $versionGroupId->value() . '.'
			);
		}

		return new VersionGroup(
			$versionGroupId,
			$result['identifier'],
			new GenerationId($result['generation_id']),
			$result['abbreviation'],
			(bool) $result['has_abilities'],
			(bool) $result['has_evs'],
			(bool) $result['has_natures'],
			$result['sort'],
		);
	}

	/**
	 * Get a version group by its identifier.
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 */
	public function getByIdentifier(string $identifier) : VersionGroup
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new VersionGroupNotFoundException(
				"No version group exists with identifier $identifier."
			);
		}

		return new VersionGroup(
			new VersionGroupId($result['id']),
			$identifier,
			new GenerationId($result['generation_id']),
			$result['abbreviation'],
			(bool) $result['has_abilities'],
			(bool) $result['has_evs'],
			(bool) $result['has_natures'],
			$result['sort'],
		);
	}

	/**
	 * Get version groups since this generation.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getSinceGeneration(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `generation_id` >= :generation_id
			ORDER BY `sort`'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have this Pokémon.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithPokemon(PokemonId $pokemonId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`vgf`.`version_group_id`
				FROM `vg_forms` AS `vgf`
				INNER JOIN `forms` AS `f`
					ON `vgf`.`form_id` = `f`.`id`
				WHERE `f`.`pokemon_id` = :pokemon_id
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have this move.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithMove(MoveId $moveId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_moves`
				WHERE `move_id` = :move_id
					AND `can_use_move` = 1
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have this move flag.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithMoveFlag(MoveFlagId $flagId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_move_flags`
				WHERE `flag_id` = :flag_id
					AND `is_functional` = 1
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':flag_id', $flagId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have this type.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithType(TypeId $typeId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_types`
				WHERE `type_id` = :type_id
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':type_id', $typeId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have this item.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithItem(ItemId $itemId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_items`
				WHERE `item_id` = :item_id
					AND `is_available` = 1
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have abilities.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithAbilities() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `has_abilities` = 1
			ORDER BY `sort`'
		);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				true,
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have this ability.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithAbility(AbilityId $abilityId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `pokemon_abilities`
				WHERE `ability_id` = :ability_id
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have this ability flag.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithAbilityFlag(AbilityFlagId $flagId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`version_group_id`
				FROM `vg_ability_flags`
				WHERE `flag_id` = :flag_id
					AND `is_functional` = 1
			)
			ORDER BY `sort`'
		);
		$stmt->bindValue(':flag_id', $flagId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have EVs.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithNatures() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_evs`,
				`sort`
			FROM `version_groups`
			WHERE `has_natures` = 1
			ORDER BY `sort`'
		);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				(bool) $result['has_evs'],
				true,
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}

	/**
	 * Get version groups that have EVs.
	 *
	 * @return VersionGroup[] Indexed by id. Ordered by sort value.
	 */
	public function getWithEvs() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`generation_id`,
				`abbreviation`,
				`has_abilities`,
				`has_natures`,
				`sort`
			FROM `version_groups`
			WHERE `has_evs` = 1
			ORDER BY `sort`'
		);
		$stmt->execute();

		$versionGroups = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$versionGroup = new VersionGroup(
				new VersionGroupId($result['id']),
				$result['identifier'],
				new GenerationId($result['generation_id']),
				$result['abbreviation'],
				(bool) $result['has_abilities'],
				true,
				(bool) $result['has_natures'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}
}
