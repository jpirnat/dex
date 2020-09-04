<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMove;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedMoveRepositoryInterface;
use PDO;

final class DatabaseMovesetRatedMoveRepository implements MovesetRatedMoveRepositoryInterface
{
	private PDO $db;

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
	 * Save a moveset rated move record.
	 *
	 * @param MovesetRatedMove $movesetRatedMove
	 *
	 * @return void
	 */
	public function save(MovesetRatedMove $movesetRatedMove) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_moves` (
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`move_id`,
				`percent`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:move_id,
				:percent
			)'
		);
		$stmt->bindValue(':month', $movesetRatedMove->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetRatedMove->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedMove->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedMove->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':move_id', $movesetRatedMove->getMoveId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedMove->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
