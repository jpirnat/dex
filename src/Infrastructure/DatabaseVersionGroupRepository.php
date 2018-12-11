<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Versions\VersionGroup;
use Jp\Dex\Domain\Versions\VersionGroupId;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabaseVersionGroupRepository implements VersionGroupRepositoryInterface
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
	 * Get a version group by its id.
	 *
	 * @param VersionGroupId $versionGroupId
	 *
	 * @throws VersionGroupNotFoundException if no version group exists with
	 *     this id.
	 *
	 * @return VersionGroup
	 */
	public function getById(VersionGroupId $versionGroupId) : VersionGroup
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`generation_id`,
				`icon`
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
			$result['icon']
		);

		return $versionGroup;
	}
}
