<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammate;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeammateRepositoryInterface;
use PDO;
use PDOException;

final class DatabaseMovesetRatedTeammateRepository implements MovesetRatedTeammateRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated teammate record.
	 *
	 * @param MovesetRatedTeammate $movesetRatedTeammate
	 *
	 * @return void
	 */
	public function save(MovesetRatedTeammate $movesetRatedTeammate) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_teammates` (
				`usage_rated_pokemon_id`,
				`teammate_id`,
				`percent`
			) VALUES (
				:urp_id,
				:teammate_id,
				:percent
			)'
		);
		$stmt->bindValue(':urp_id', $movesetRatedTeammate->getUsageRatedPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':teammate_id', $movesetRatedTeammate->getTeammateId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedTeammate->getPercent());
		try {
			$stmt->execute();
		} catch (PDOException) {
			// This record already exists.
			// Bug fix for http://www.smogon.com/stats/2014-11/moveset/anythinggoes-0.txt
			// in which Inkay has teammate Abra twice.
		}
	}
}
