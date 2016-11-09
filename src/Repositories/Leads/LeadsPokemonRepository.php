<?php
declare(strict_types=1);

namespace Jp\Trendalyzer\Repositories\Leads;

use PDO;

class LeadsPokemonRepository
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
	 * Insert a `leads_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $pokemonId
	 * @param int $raw
	 * @param float $rawPercent
	 *
	 * @return bool
	 */
	public function insert(
		int $year,
		int $month,
		int $formatId,
		int $pokemonId,
		int $raw,
		float $rawPercent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `leads_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`pokemon_id`,
				`raw`,
				`raw_percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:pokemon_id,
				:raw,
				:raw_percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':raw', $raw, PDO::PARAM_INT);
		$stmt->bindValue(':raw_percent', $rawPercent, PDO::PARAM_STR);
		return $stmt->execute();
	}
}
