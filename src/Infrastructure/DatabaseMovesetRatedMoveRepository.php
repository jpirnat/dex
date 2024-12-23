<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use PDO;

final readonly class DatabaseMovesetRatedMoveRepository implements MovesetRatedMoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated move record.
	 */
	public function save(MovesetRatedMove $movesetRatedMove) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_moves` (
				`usage_rated_pokemon_id`,
				`move_id`,
				`percent`
			) VALUES (
				:urp_id,
				:move_id,
				:percent
			)'
		);
		$stmt->bindValue(':urp_id', $movesetRatedMove->usageRatedPokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $movesetRatedMove->moveId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedMove->percent);
		$stmt->execute();
	}
}
