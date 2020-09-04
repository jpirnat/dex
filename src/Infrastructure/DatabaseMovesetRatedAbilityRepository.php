<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbility;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use PDO;

final class DatabaseMovesetRatedAbilityRepository implements MovesetRatedAbilityRepositoryInterface
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
	 * Save a moveset rated ability record.
	 *
	 * @param MovesetRatedAbility $movesetRatedAbility
	 *
	 * @return void
	 */
	public function save(MovesetRatedAbility $movesetRatedAbility) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_abilities` (
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`ability_id`,
				`percent`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:ability_id,
				:percent
			)'
		);
		$stmt->bindValue(':month', $movesetRatedAbility->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetRatedAbility->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedAbility->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedAbility->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $movesetRatedAbility->getAbilityId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedAbility->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
