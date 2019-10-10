<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use PDO;

final class DatabaseNatureRepository implements NatureRepositoryInterface
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
				`identifier`,
				`increased_stat_id`,
				`decreased_stat_id`,
				`vc_exp_remainder`
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

		$increasedStatId = $result['increased_stat_id'] !== null
			? new StatId($result['increased_stat_id'])
			: null;
		$decreasedStatId = $result['decreased_stat_id'] !== null
			? new StatId($result['decreased_stat_id'])
			: null;

		$nature = new Nature(
			$natureId,
			$result['identifier'],
			$increasedStatId,
			$decreasedStatId,
			$result['vc_exp_remainder']
		);

		return $nature;
	}

	/**
	 * Get all natures.
	 *
	 * @return Nature[] Indexed by id.
	 */
	public function getAll() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`increased_stat_id`,
				`decreased_stat_id`,
				`vc_exp_remainder`
			FROM `natures`'
		);
		$stmt->execute();

		$natures = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$increasedStatId = $result['increased_stat_id'] !== null
				? new StatId($result['increased_stat_id'])
				: null;
			$decreasedStatId = $result['decreased_stat_id'] !== null
				? new StatId($result['decreased_stat_id'])
				: null;

			$nature = new Nature(
				new NatureId($result['id']),
				$result['identifier'],
				$increasedStatId,
				$decreasedStatId,
				$result['vc_exp_remainder']
			);

			$natures[$result['id']] = $nature;
		}

		return $natures;
	}
}
