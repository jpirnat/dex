<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
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
	 * Do any leads rated Pokémon records exist for this month, format, and
	 * rating?
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function hasAny(DateTime $month, FormatId $formatId, int $rating) : bool
	{
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `leads_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
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
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`rank`,
				`usage_percent`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:rank,
				:usage_percent
			)'
		);
		$stmt->bindValue(':month', $leadsRatedPokemon->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $leadsRatedPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $leadsRatedPokemon->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $leadsRatedPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $leadsRatedPokemon->getRank(), PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $leadsRatedPokemon->getUsagePercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get leads rated Pokémon records by month, format, and rating. Indexed by
	 * Pokémon id value. Use this to recreate a stats leads file, such as
	 * http://www.smogon.com/stats/leads/2014-11/ou-1695.txt.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getByMonthAndFormatAndRating(
		DateTime $month,
		FormatId $formatId,
		int $rating
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				`rank`,
				`usage_percent`
			FROM `leads_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();

		$leadsRatedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsRatedPokemon = new LeadsRatedPokemon(
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
	 * Get leads rated Pokémon records by their format, rating, and Pokémon.
	 * Use this to create a trend line for a Pokémon's lead usage in a format.
	 * Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return LeadsRatedPokemon[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`month`,
				`rank`,
				`usage_percent`
			FROM `leads_rated_pokemon`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			ORDER BY `month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$leadsRatedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsRatedPokemon = new LeadsRatedPokemon(
				new DateTime($result['month']),
				$formatId,
				$rating,
				$pokemonId,
				$result['rank'],
				(float) $result['usage_percent']
			);

			$leadsRatedPokemons[$result['month']] = $leadsRatedPokemon;
		}

		return $leadsRatedPokemons;
	}
}
