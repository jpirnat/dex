<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories\Moveset;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpread;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use PDO;

class DatabaseMovesetRatedSpreadRepository implements MovesetRatedSpreadRepositoryInterface
{
	/** @var PDO $db */
	protected $db;

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
	 * Save a moveset rated spread record.
	 *
	 * @param MovesetRatedSpread $movesetRatedSpread
	 *
	 * @return void
	 */
	public function save(MovesetRatedSpread $movesetRatedSpread) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_spreads` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`nature_id`,
				`hp`,
				`atk`,
				`def`,
				`spa`,
				`spd`,
				`spe`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:nature_id,
				:hp,
				:atk,
				:def,
				:spa,
				:spd,
				:spe,
				:percent
			)'
		);
		$stmt->bindValue(':year', $movesetRatedSpread->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedSpread->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedSpread->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedSpread->rating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedSpread->pokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':nature_id', $movesetRatedSpread->natureId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':hp', $movesetRatedSpread->hp(), PDO::PARAM_INT);
		$stmt->bindValue(':atk', $movesetRatedSpread->atk(), PDO::PARAM_INT);
		$stmt->bindValue(':def', $movesetRatedSpread->def(), PDO::PARAM_INT);
		$stmt->bindValue(':spa', $movesetRatedSpread->spa(), PDO::PARAM_INT);
		$stmt->bindValue(':spd', $movesetRatedSpread->spd(), PDO::PARAM_INT);
		$stmt->bindValue(':spe', $movesetRatedSpread->spe(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedSpread->percent(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
