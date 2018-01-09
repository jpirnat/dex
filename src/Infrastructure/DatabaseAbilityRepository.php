<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Abilities\Ability;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityNotFoundException;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

class DatabaseAbilityRepository implements AbilityRepositoryInterface
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
				'No ability exists with identifier ' . $identifier
			);
		}

		$ability = new Ability(
			new AbilityId($result['id']),
			$identifier,
			new VersionGroupId($result['introduced_in_version_group_id'])
		);

		return $ability;
	}
}
