<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Exception;
use Jp\Dex\Domain\Moves\Move;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Moves\MoveRepositoryInterface;
use Jp\Dex\Domain\Versions\Generation;
use PDO;

class DatabaseMoveRepository implements MoveRepositoryInterface
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
	 * Get an move by its identifier.
	 *
	 * @param string $identifier
	 *
	 * @throws Exception if no move exists with this identifier.
	 *
	 * @return Move
	 */
	public function getByIdentifier(string $identifier) : Move
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`introduced_in_generation`
			FROM `moves`
			WHERE `identifier` = :identifier
			LIMIT 1'
		);
		$stmt->bindValue(':identifier', $identifier, PDO::PARAM_STR);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$result) {
			throw new Exception('No move exists with identifier ' . $identifier);
		}

		$move = new Move(
			new MoveId($result['id']),
			$identifier,
			new Generation($result['introduced_in_generation'])
		);

		return $move;
	}
}
