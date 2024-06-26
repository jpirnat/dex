<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\PokemonMoves\MoveMethod;
use Jp\Dex\Domain\PokemonMoves\MoveMethodId;
use Jp\Dex\Domain\PokemonMoves\MoveMethodRepositoryInterface;
use PDO;

final readonly class DatabaseMoveMethodRepository implements MoveMethodRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

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
				`sort`
			FROM `move_methods`
			ORDER BY `sort`'
		);
		$stmt->execute();

		$moveMethods = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$moveMethod = new MoveMethod(
				new MoveMethodId($result['id']),
				$result['identifier'],
				$result['sort'],
			);

			$moveMethods[$result['id']] = $moveMethod;
		}

		return $moveMethods;
	}
}
