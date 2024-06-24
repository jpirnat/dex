<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\AbilityId;
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
				`icon`,
				`abbreviation`,
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

		$versionGroup = new VersionGroup(
			$versionGroupId,
			$result['identifier'],
			new GenerationId($result['generation_id']),
			$result['icon'],
			$result['abbreviation'],
			$result['sort'],
		);

		return $versionGroup;
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
				`icon`,
				`abbreviation`,
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

		$versionGroup = new VersionGroup(
			new VersionGroupId($result['id']),
			$identifier,
			new GenerationId($result['generation_id']),
			$result['icon'],
			$result['abbreviation'],
			$result['sort'],
		);

		return $versionGroup;
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
				`icon`,
				`abbreviation`,
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
				$result['icon'],
				$result['abbreviation'],
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
				`icon`,
				`abbreviation`,
				`sort`
			FROM `version_groups`
			WHERE `id` IN (
				SELECT
					`vgf`.`version_group_id`
				FROM `version_group_forms` AS `vgf`
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
				$result['icon'],
				$result['abbreviation'],
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
				`icon`,
				`abbreviation`,
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
				$result['icon'],
				$result['abbreviation'],
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
				`icon`,
				`abbreviation`,
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
				$result['icon'],
				$result['abbreviation'],
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
				`icon`,
				`abbreviation`,
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
				$result['icon'],
				$result['abbreviation'],
				$result['sort'],
			);

			$versionGroups[$result['id']] = $versionGroup;
		}

		return $versionGroups;
	}
}
