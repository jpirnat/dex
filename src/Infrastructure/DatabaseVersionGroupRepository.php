<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;
use PDO;

final class DatabaseVersionGroupRepository implements VersionGroupRepositoryInterface
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
			$result['sort']
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
			$result['sort']
		);

		return $versionGroup;
	}
}
