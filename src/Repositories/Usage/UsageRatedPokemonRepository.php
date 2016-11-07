<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Usage;

use PDO;

class UsageRatedPokemonRepository
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
	 * Insert a `usage_rated_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $rating
	 * @param int $pokemonId
	 * @param int $rank
	 * @param float $usagePercent
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $metagameId,
		int $rating,
		int $pokemonId,
		int $rank,
		float $usagePercent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_rated_pokemon` (
				`year`,
				`month`,
				`metagame_id`,
				`rating`,
				`pokemon_id`,
				`rank`,
				`usage_percent`
			) VALUES (
				:year,
				:month,
				:metagame_id,
				:rating,
				:pokemon_id,
				:rank,
				:usage_percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':metagame_id', $metagameId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':rank', $rank, PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $usagePercent, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
