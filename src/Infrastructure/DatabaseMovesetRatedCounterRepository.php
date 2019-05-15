<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounter;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedCounterRepositoryInterface;
use PDO;

class DatabaseMovesetRatedCounterRepository implements MovesetRatedCounterRepositoryInterface
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
	 * Save a moveset rated counter record.
	 *
	 * @param MovesetRatedCounter $movesetRatedCounter
	 *
	 * @return void
	 */
	public function save(MovesetRatedCounter $movesetRatedCounter) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_counters` (
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`counter_id`,
				`number1`,
				`number2`,
				`number3`,
				`percent_knocked_out`,
				`percent_switched_out`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:counter_id,
				:number1,
				:number2,
				:number3,
				:percent_knocked_out,
				:percent_switched_out
			)'
		);
		$stmt->bindValue(':month', $movesetRatedCounter->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetRatedCounter->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedCounter->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedCounter->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':counter_id', $movesetRatedCounter->getCounterId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':number1', $movesetRatedCounter->getNumber1(), PDO::PARAM_STR);
		$stmt->bindValue(':number2', $movesetRatedCounter->getNumber2(), PDO::PARAM_STR);
		$stmt->bindValue(':number3', $movesetRatedCounter->getNumber3(), PDO::PARAM_STR);
		$stmt->bindValue(':percent_knocked_out', $movesetRatedCounter->getPercentKnockedOut(), PDO::PARAM_STR);
		$stmt->bindValue(':percent_switched_out', $movesetRatedCounter->getPercentSwitchedOut(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
