<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbility;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedAbilityRepositoryInterface;
use PDO;

final readonly class DatabaseMovesetRatedAbilityRepository implements MovesetRatedAbilityRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated ability record.
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
		$stmt->bindValue(':percent', $movesetRatedAbility->getPercent());
		$stmt->execute();
	}
}
