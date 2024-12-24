<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Items\MachineType;
use Jp\Dex\Domain\Items\TechnicalMachine;
use Jp\Dex\Domain\Items\TmNotFoundException;
use Jp\Dex\Domain\Items\TmRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final readonly class DatabaseTmRepository implements TmRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a TM by its version group and move.
	 *
	 * @throws TmNotFoundException if no TM exists with this version group and
	 *     move.
	 */
	public function getByVersionGroupAndMove(
		VersionGroupId $versionGroupId,
		MoveId $moveId,
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
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $moveId->value, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new TmNotFoundException(
				"No TM exists with version group id $versionGroupId->value and move id $moveId->value."
			);
		}

		return new TechnicalMachine(
			$versionGroupId,
			new MachineType($result['machine_type']),
			$result['number'],
			new ItemId($result['item_id']),
			$moveId,
		);
	}

	/**
	 * Get TMs in this version group.
	 *
	 * @return TechnicalMachine[] Ordered by machine type, then by number.
	 */
	public function getByVersionGroup(VersionGroupId $versionGroupId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`machine_type`,
				`number`,
				`item_id`,
				`move_id`
			FROM `technical_machines`
			WHERE `version_group_id` = :version_group_id
			ORDER BY
				`machine_type`,
				`number`'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->execute();

		$tms = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tm = new TechnicalMachine(
				$versionGroupId,
				new MachineType($result['machine_type']),
				$result['number'],
				new ItemId($result['item_id']),
				new MoveId($result['move_id']),
			);

			$tms[] = $tm;
		}

		return $tms;
	}

	/**
	 * Get TMs by their move.
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
		$stmt->bindValue(':move_id', $moveId->value, PDO::PARAM_INT);
		$stmt->execute();

		$tms = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tm = new TechnicalMachine(
				new VersionGroupId($result['version_group_id']),
				new MachineType($result['machine_type']),
				$result['number'],
				new ItemId($result['item_id']),
				$moveId,
			);

			$tms[$result['version_group_id']] = $tm;
		}

		return $tms;
	}

	/**
	 * Get TMs available for this version group, based on all the version groups
	 * that can transfer movesets into this one.
	 *
	 * @return TechnicalMachine[][] Indexed first by version group id and then
	 *     by move id.
	 */
	public function getByIntoVg(VersionGroupId $versionGroupId) : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`version_group_id`,
				`machine_type`,
				`number`,
				`item_id`,
				`move_id`
			FROM `technical_machines`
			WHERE `version_group_id` IN (
				SELECT
					`from_vg_id`
				FROM `vg_move_transfers`
				WHERE `into_vg_id` = :version_group_id
			)'
		);
		$stmt->bindValue(':version_group_id', $versionGroupId->value, PDO::PARAM_INT);
		$stmt->execute();

		$tms = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tm = new TechnicalMachine(
				new VersionGroupId($result['version_group_id']),
				new MachineType($result['machine_type']),
				$result['number'],
				new ItemId($result['item_id']),
				new MoveId($result['move_id']),
			);

			$tms[$result['version_group_id']][$result['move_id']] = $tm;
		}

		return $tms;
	}
}
