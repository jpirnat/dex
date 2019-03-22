<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\PokemonMoves\MoveMethod;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use Jp\Dex\Domain\Versions\GenerationId;
use PDO;

class DatabaseMoveMethodRepository implements MoveMethodRepositoryInterface
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
	 * Get all move methods.
	 *
	 * @return MoveMethod[] Indexed by id, sorted by sort value.
	 */
	public function getAll() : array
	{
		$stmt = $this->db->prepare(
			'SELECT
				`id`,
				`identifier`,
				`introduced_in_generation_id`,
				`sort`
			FROM `move_methods`
			ORDER BY `sort` ASC'
		);
		$stmt->execute();

		$moveMethods = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$moveMethod = new MoveMethod(
				new MoveMethodId($result['id']),
				$result['identifier'],
				new GenerationId($result['introduced_in_generation_id']),
				$result['sort']
			);

			$moveMethods[$result['id']] = $moveMethod;
		}

		return $moveMethods;
	}
}
