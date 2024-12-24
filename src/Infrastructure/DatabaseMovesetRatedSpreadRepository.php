<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpread;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use PDO;

final readonly class DatabaseMovesetRatedSpreadRepository implements MovesetRatedSpreadRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated spread record.
	 */
	public function save(MovesetRatedSpread $movesetRatedSpread) : void
	{
		$evSpread = $movesetRatedSpread->evSpread;

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
		$stmt->bindValue(':urp_id', $movesetRatedSpread->usageRatedPokemonId->value, PDO::PARAM_INT);
		$stmt->bindValue(':nature_id', $movesetRatedSpread->natureId->value, PDO::PARAM_INT);
		$stmt->bindValue(':hp', $evSpread->get(new StatId(StatId::HP))->value, PDO::PARAM_INT);
		$stmt->bindValue(':atk', $evSpread->get(new StatId(StatId::ATTACK))->value, PDO::PARAM_INT);
		$stmt->bindValue(':def', $evSpread->get(new StatId(StatId::DEFENSE))->value, PDO::PARAM_INT);
		$stmt->bindValue(':spa', $evSpread->get(new StatId(StatId::SPECIAL_ATTACK))->value, PDO::PARAM_INT);
		$stmt->bindValue(':spd', $evSpread->get(new StatId(StatId::SPECIAL_DEFENSE))->value, PDO::PARAM_INT);
		$stmt->bindValue(':spe', $evSpread->get(new StatId(StatId::SPEED))->value, PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedSpread->percent);
		$stmt->execute();
	}
}
