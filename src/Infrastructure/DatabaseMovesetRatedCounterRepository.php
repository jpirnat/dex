<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounter;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;
use PDO;

final class DatabaseMovesetRatedCounterRepository implements MovesetRatedCounterRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated counter record.
	 */
	public function save(MovesetRatedCounter $movesetRatedCounter) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_counters` (
				`usage_rated_pokemon_id`,
				`counter_id`,
				`number1`,
				`number2`,
				`number3`,
				`percent_knocked_out`,
				`percent_switched_out`
			) VALUES (
				:urp_id,
				:counter_id,
				:number1,
				:number2,
				:number3,
				:percent_knocked_out,
				:percent_switched_out
			)'
		);
		$stmt->bindValue(':urp_id', $movesetRatedCounter->getUsageRatedPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':counter_id', $movesetRatedCounter->getCounterId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':number1', $movesetRatedCounter->getNumber1());
		$stmt->bindValue(':number2', $movesetRatedCounter->getNumber2());
		$stmt->bindValue(':number3', $movesetRatedCounter->getNumber3());
		$stmt->bindValue(':percent_knocked_out', $movesetRatedCounter->getPercentKnockedOut());
		$stmt->bindValue(':percent_switched_out', $movesetRatedCounter->getPercentSwitchedOut());
		$stmt->execute();
	}
}
