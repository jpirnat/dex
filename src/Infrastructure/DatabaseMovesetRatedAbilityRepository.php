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
				`usage_rated_pokemon_id`,
				`ability_id`,
				`percent`
			) VALUES (
				:urp_id,
				:ability_id,
				:percent
			)'
		);
		$stmt->bindValue(':urp_id', $movesetRatedAbility->getUsageRatedPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':ability_id', $movesetRatedAbility->getAbilityId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedAbility->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}
}
