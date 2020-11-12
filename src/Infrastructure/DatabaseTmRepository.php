<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\MachineType;
use Jp\Dex\Domain\Items\TechnicalMachine;
use Jp\Dex\Domain\Items\TmNotFoundException;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\GenerationId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseTmRepository implements TmRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

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
				`machine_type`,
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
			new MachineType($result['machine_type']),
			$result['number'],
			new ItemId($result['item_id']),
			$moveId
		);

		return $tm;
	}

	/**
	 * Get TMs by their move.
	 *
	 * @param MoveId $moveId
	 *
	 * @return TechnicalMachine[] Indexed by version group id.
	 */
	public function getByMove(MoveId $moveId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`version_group_id`,
				`machine_type`,
				`number`,
				`item_id`
			FROM `technical_machines`
			WHERE `move_id` = :move_id'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$tms = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tm = new TechnicalMachine(
				new VersionGroupId($result['version_group_id']),
				new MachineType($result['machine_type']),
				$result['number'],
				new ItemId($result['item_id']),
				$moveId
			);

			$tms[$result['version_group_id']] = $tm;
		}

		return $tms;
	}

	/**
	 * Get TMs between these two generations, inclusive. This method is used to
	 * get all potentially relevant TMs for the dex Pokémon page.
	 *
	 * @param GenerationId $begin
	 * @param GenerationId $end
	 *
	 * @return TechnicalMachine[][] Indexed first by version group id and then
	 *     by move id.
	 */
	public function getBetween(GenerationId $begin, GenerationId $end) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`tm`.`version_group_id`,
				`tm`.`machine_type`,
				`tm`.`number`,
				`tm`.`item_id`,
				`tm`.`move_id`
			FROM `technical_machines` AS `tm`
			INNER JOIN `version_groups` AS `vg`
				ON `tm`.`version_group_id` = `vg`.`id`
			WHERE `vg`.`generation_id` BETWEEN :begin AND :end'
		);
		$stmt->bindValue(':begin', $begin->value(), PDO::PARAM_INT);
		$stmt->bindValue(':end', $end->value(), PDO::PARAM_INT);
		$stmt->execute();

		$tms = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tm = new TechnicalMachine(
				new VersionGroupId($result['version_group_id']),
				new MachineType($result['machine_type']),
				$result['number'],
				new ItemId($result['item_id']),
				new MoveId($result['move_id'])
			);

			$tms[$result['version_group_id']][$result['move_id']] = $tm;
		}

		return $tms;
	}
}
