<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Moveset;

use PDO;

class MovesetPokemonRepository
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
	 * Insert a `moveset_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $pokemonId
	 * @param int $rawCount
	 * @param int $viabilityCeiling
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $metagameId,
		int $pokemonId,
		int $rawCount,
		int $viabilityCeiling
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_pokemon` (
				`year`,
				`month`,
				`metagame_id`,
				`pokemon_id`,
				`raw_count`,
				`viability_ceiling`
			) VALUES (
				:year,
				:month,
				:metagame_id,
				:pokemon_id,
				:raw_count,
				:viability_ceiling
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':metagame_id', $metagameId, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':raw_count', $rawCount, PDO::PARAM_INT);
		$stmt->bindValue(':viability_ceiling', $viabilityCeiling, PDO::PARAM_INT);
		return $stmt->execute();
	}
}
