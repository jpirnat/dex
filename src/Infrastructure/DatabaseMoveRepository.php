<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Moves\Move;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveNotFoundException;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Moves\MoveType;
use Jp\Dex\Domain\Versions\VersionGroupId;
use PDO;

final class DatabaseMoveRepository implements MoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Get a move by its id.
	 *
	 * @throws MoveNotFoundException if no move exists with this id.
	 */
	public function getById(MoveId $moveId) : Move
	{
		$stmt = $this->db->prepare(
			'SELECT
				`identifier`,
				`introduced_in_version_group_id`,
				`move_type`
			FROM `moves`
			WHERE `id` = :move_id
			LIMIT 1'
		);
		$stmt->bindValue(':move_id', $moveId->value(), PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MoveNotFoundException(
				'No move exists with id ' . $moveId->value() . '.'
			);
		}

		$move = new Move(
			$moveId,
			$result['identifier'],
			new VersionGroupId($result['introduced_in_version_group_id']),
			new MoveType($result['move_type']),
		);

		return $move;
	}

	/**
	 * Get an move by its identifier.
	 *
	 * @throws MoveNotFoundException if no move exists with this identifier.
	 */
	public function getByIdentifier(string $identifier) : Move
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`introduced_in_version_group_id`,
				`move_type`
			FROM `moves`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new MoveNotFoundException(
				'No move exists with identifier ' . $identifier . '.'
			);
		}

		$move = new Move(
			new MoveId($result['id']),
			$identifier,
			new VersionGroupId($result['introduced_in_version_group_id']),
			new MoveType($result['move_type']),
		);

		return $move;
	}
}
