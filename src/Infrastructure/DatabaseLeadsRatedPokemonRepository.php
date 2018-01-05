<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemon;
use Jp\Dex\Domain\Stats\Leads\LeadsRatedPokemonRepositoryInterface;
use PDO;

class DatabaseLeadsRatedPokemonRepository implements LeadsRatedPokemonRepositoryInterface
{
	/** @var PDO $db */
	private $db;

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
	 * Do any leads rated Pokémon records exist for this year, month, format,
	 * and rating?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function hasAny(
		int $year,
		int $month,
		FormatId $formatId,
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
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count > 0;
	}

	/**
	 * Save a leads rated Pokémon record.
	 *
	 * @param LeadsRatedPokemon $leadsRatedPokemon
	 *
	 * @return void
	 */
	public function save(LeadsRatedPokemon $leadsRatedPokemon) : void
	{
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
		$stmt->bindValue(':year', $leadsRatedPokemon->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $leadsRatedPokemon->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $leadsRatedPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $leadsRatedPokemon->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $leadsRatedPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $leadsRatedPokemon->getRank(), PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $leadsRatedPokemon->getUsagePercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get leads rated Pokémon records by year and month and format and rating.
	 * Indexed by Pokémon id value. Use this to recreate a stats leads file,
	 * such as http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getByYearAndMonthAndFormatAndRating(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`rank`,
				`usage_percent`
			FROM `leads_rated_pokemon`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();

		$leadsRatedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsRatedPokemon = new LeadsRatedPokemon(
				$year,
				$month,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				$result['rank'],
				(float) $result['usage_percent']
			);

			$leadsRatedPokemons[$result['pokemon_id']] = $leadsRatedPokemon;
		}

		return $leadsRatedPokemons;
	}

	/**
	 * Get leads rated Pokémon records by format and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return LeadsRatedPokemon[]
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
				`rank`,
				`usage_percent`
			FROM `leads_rated_pokemon`
			WHERE `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$leadsRatedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsRatedPokemon = new LeadsRatedPokemon(
				$result['year'],
				$result['month'],
				$formatId,
				$result['rating'],
				$pokemonId,
				$result['rank'],
				(float) $result['usage_percent']
			);

			$leadsRatedPokemons[] = $leadsRatedPokemon;
		}

		return $leadsRatedPokemons;
	}
}
