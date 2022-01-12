<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpread;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use PDO;

final class DatabaseMovesetRatedSpreadRepository implements MovesetRatedSpreadRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated spread record.
	 */
	public function save(MovesetRatedSpread $movesetRatedSpread) : void
	{
		$evSpread = $movesetRatedSpread->getEvSpread();

		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_spreads` (
				`usage_rated_pokemon_id`,
				`nature_id`,
				`hp`,
				`atk`,
				`def`,
				`spa`,
				`spd`,
				`spe`,
				`percent`
			) VALUES (
				:urp_id,
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
		$stmt->bindValue(':urp_id', $movesetRatedSpread->getUsageRatedPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':nature_id', $movesetRatedSpread->getNatureId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':hp', $evSpread->get(new StatId(StatId::HP))->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':atk', $evSpread->get(new StatId(StatId::ATTACK))->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':def', $evSpread->get(new StatId(StatId::DEFENSE))->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':spa', $evSpread->get(new StatId(StatId::SPECIAL_ATTACK))->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':spd', $evSpread->get(new StatId(StatId::SPECIAL_DEFENSE))->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':spe', $evSpread->get(new StatId(StatId::SPEED))->getValue(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedSpread->getPercent());
		$stmt->execute();
	}
}
