<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use PDO;

class DatabaseNatureRepository implements NatureRepositoryInterface
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
	 * Get a nature by its id.
	 *
	 * @param NatureId $natureId
	 *
	 * @throws NatureNotFoundException if no nature exists with this id.
	 *
	 * @return Nature
	 */
	public function getById(NatureId $natureId) : Nature
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`
			FROM `natures`
			WHERE `id` = :nature_id
			LIMIT 1'
		);
		$stmt->bindValue(':nature_id', $natureId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new NatureNotFoundException(
				'No nature exists with id ' . $$natureId->value()
			);
		}

		$nature = new Nature(
			$natureId,
			$result['identifier']
		);

		return $nature;
	}
}
