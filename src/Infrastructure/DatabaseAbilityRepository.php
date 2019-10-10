<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\Ability;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityNotFoundException;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseAbilityRepository implements AbilityRepositoryInterface
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
	 * Get an ability by its id.
	 *
	 * @param AbilityId $abilityId
	 *
	 * @throws AbilityNotFoundException if no ability exists with this id.
	 *
	 * @return Ability
	 */
	public function getById(AbilityId $abilityId) : Ability
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`introduced_in_version_group_id`
			FROM `abilities`
			WHERE `id` = :ability_id
			LIMIT 1'
		);
		$stmt->bindValue(':ability_id', $abilityId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new AbilityNotFoundException(
				'No ability exists with id ' . $abilityId->value() . '.'
			);
		}

		$ability = new Ability(
			$abilityId,
			$result['identifier'],
			new VersionGroupId($result['introduced_in_version_group_id'])
		);

		return $ability;
	}

	/**
	 * Get an ability by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws AbilityNotFoundException if no ability exists with this
	 *     identifier.
	 *
	 * @return Ability
	 */
	public function getByIdentifier(string $identifier) : Ability
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`introduced_in_version_group_id`
			FROM `abilities`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new AbilityNotFoundException(
				'No ability exists with identifier ' . $identifier . '.'
			);
		}

		$ability = new Ability(
			new AbilityId($result['id']),
			$identifier,
			new VersionGroupId($result['introduced_in_version_group_id'])
		);

		return $ability;
	}

	/**
	 * Get abilities in this generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return Ability[] Indexed by id.
	 */
	public function getByGeneration(GenerationId $generationId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`a`.`id`,
				`a`.`identifier`,
				`a`.`introduced_in_version_group_id`
			FROM `abilities` AS `a`
			INNER JOIN `version_groups` AS `vg`
				ON `a`.`introduced_in_version_group_id` = `vg`.`id`
			WHERE `vg`.`generation_id` <= :generation_id'
		);
		$stmt->bindValue(':generation_id', $generationId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$abilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$ability = new Ability(
				new AbilityId($result['id']),
				$result['identifier'],
				new VersionGroupId($result['introduced_in_version_group_id'])
			);

			$abilities[$result['id']] = $ability;
		}

		return $abilities;
	}
}
