<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Usage;

use PDO;

class UsagePokemonRepository
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
	 * Insert a `usage_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $metagameId
	 * @param int $pokemonId
	 * @param int $raw
	 * @param float $rawPercent
	 * @param int $real
	 * @param float $realPercent
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $metagameId,
		int $pokemonId,
		int $raw,
		float $rawPercent,
		int $real,
		float $realPercent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_pokemon` (
				`year`,
				`month`,
				`metagame_id`,
				`pokemon_id`,
				`raw`,
				`raw_percent`,
				`real`,
				`real_percent`
			) VALUES (
				:year,
				:month,
				:metagame_id,
				:pokemon_id,
				:raw,
				:raw_percent,
				:real,
				:real_percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':metagame_id', $metagameId, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':raw', $raw, PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $rawPercent, PDO::PARAM_STR);
		$stmt->bindValue(':real', $real, PDO::PARAM_INT);
		$stmt->bindValue(':real_percent', $realPercent, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
