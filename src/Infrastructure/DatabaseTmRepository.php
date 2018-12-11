<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\TechnicalMachine;
use Jp\Dex\Domain\Items\TmNotFoundException;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

class DatabaseTmRepository implements TmRepositoryInterface
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
	 * Get a TM by its version group and move.
	 *
	 * @param VersionGroupId $versionGroupId
	 * @param MoveId $moveId
	 *
	 * @throws TmNotFoundException if no TM exists with this version group and
	 *     move.
	 *
	 * @return TechnicalMachine
	 */
	public function getByVersionGroupAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId
	) : TechnicalMachine {
		$stmt = $this->db->prepare(
			'SELECT
				`is_hm`,
				`number`,
				`item_id`
			FROM `technical_machines`
			WHERE `version_group_id` = :version_group_id
				AND `move_id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TmNotFoundException(
				'No TM exists with version group id ' . $versionGroupId->value()
				. ' and move id ' . $moveId->value() . '.'
			);
		}

		$tm = new TechnicalMachine(
			$versionGroupId,
			(bool) $result['is_hm'],
			$result['number'],
			new ItemId($result['item_id']),
			$moveId
		);

		return $tm;
	}
}
