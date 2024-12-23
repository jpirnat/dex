<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeraType;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedTeraTypeRepositoryInterface;
use PDO;
use PDOException;

final readonly class DatabaseMovesetRatedTeraTypeRepository implements MovesetRatedTeraTypeRepositoryInterface
{
	public function __construct(
		private PDO $db,
	) {}

	/**
	 * Save a moveset rated tera type record.
	 */
	public function save(MovesetRatedTeraType $movesetRatedTeraType) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_tera_types` (
				`usage_rated_pokemon_id`,
				`type_id`,
				`percent`
			) VALUES (
				:urp_id,
				:type_id,
				:percent
			)'
		);
		$stmt->bindValue(':urp_id', $movesetRatedTeraType->usageRatedPokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':type_id', $movesetRatedTeraType->typeId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedTeraType->percent);
		try {
			$stmt->execute();
		} catch (PDOException) {
			// This record already exists.
		}
	}
}
