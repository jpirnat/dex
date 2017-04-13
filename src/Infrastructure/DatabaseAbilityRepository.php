<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Exception;
use Jp\Dex\Domain\Abilities\Ability;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Abilities\AbilityRepositoryInterface;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseAbilityRepository implements AbilityRepositoryInterface
{
	/** @var PDO $db */
	protected $db;

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
	 * @throws Exception if no ability exists with this identifier.
	 *
	 * @return Ability
	 */
	public function getByIdentifier(string $identifier) : Ability
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`introduced_in_generation`
			FROM `abilities`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new Exception('No ability exists with identifier ' . $identifier);
		}

		$ability = new Ability(
			new AbilityId($result['id']),
			$identifier,
			new Generation($result['introduced_in_generation'])
		);

		return $ability;
	}
}
