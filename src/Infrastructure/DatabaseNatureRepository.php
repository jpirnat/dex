<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Exception;
use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
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
	 * @throws Exception if no nature exists with this id.
	 *
	 * @return Nature
	 */
	public function getById(NatureId $natureId) : Nature
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`increased_stat_id`,
				`decreased_stat_id`
			FROM `natures`
			WHERE `id` = :nature_id
			LIMIT 1'
		);
		$stmt->bindValue(':nature_id', $natureId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new Exception('No nature exists with id ' . $$natureId->value());
		}

		if ($result['increased_stat_id'] !== null) {
			$increasedStatId = new StatId($result['increased_stat_id']);
		} else {
			// It's a neutral nature.
			$increasedStatId = null;
		}

		if ($result['decreased_stat_id'] !== null) {
			$decreasedStatId = new StatId($result['decreased_stat_id']);
		} else {
			// It's a neutral nature.
			$decreasedStatId = null;
		}

		$nature = new Nature(
			$natureId,
			$result['identifier'],
			$increasedStatId,
			$decreasedStatId
		);

		return $nature;
	}
}
