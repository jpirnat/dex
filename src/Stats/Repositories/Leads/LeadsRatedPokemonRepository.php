<?php
declare(strict_types=1);

namespace Jp\Dex\Stats\Repositories\Leads;

use PDO;
use PDOException;

class LeadsRatedPokemonRepository
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
	 * Does `leads_rated_pokemon` contain any records for the given key?
	 * (Was the relevant data already imported?)
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function exists(
		int $year,
		int $month,
		int $formatId,
		int $rating
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `leads_rated_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Insert a `leads_rated_pokemon` record.
	 *
	 * @param int $year
	 * @param int $month
	 * @param int $formatId
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
		int $formatId,
		int $rating,
		int $pokemonId,
		int $rank,
		float $usagePercent
	) : bool {
		$stmt = $this->db->prepare(
			'INSERT INTO `leads_rated_pokemon` (
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`rank`,
				`usage_percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:rank,
				:usage_percent
			)'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_INT);
		$stmt->bindValue(':rank', $rank, PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $usagePercent, PDO::PARAM_STR);
		try {
			return $stmt->execute();
		} catch (PDOException $e) {
			// A record for this key already exists.
			return false;
		}
	}

	/**
	 * Get records by format and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return array
	 */
	public function getByFormatAndPokemon(
		FormatId $formatId,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`,
				`rating`,
				`usage_percent`
			FROM `leads_rated_pokemon`
			WHERE `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
