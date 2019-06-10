<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemon;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonRepositoryInterface;
use PDO;

class DatabaseUsageRatedPokemonRepository implements UsageRatedPokemonRepositoryInterface
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
	 * Do any usage rated Pokémon records exist for this month, format, and
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
			FROM `usage_rated_pokemon`
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
	 * Save a usage rated Pokémon record.
	 *
	 * @param UsageRatedPokemon $usageRatedPokemon
	 *
	 * @return void
	 */
	public function save(UsageRatedPokemon $usageRatedPokemon) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `usage_rated_pokemon` (
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
		$stmt->bindValue(':month', $usageRatedPokemon->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $usageRatedPokemon->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $usageRatedPokemon->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $usageRatedPokemon->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $usageRatedPokemon->getRank(), PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $usageRatedPokemon->getUsagePercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get usage rated Pokémon records by month, format, and rating. Indexed by
	 * Pokémon id value. Use this to recreate a stats usage file, such as
	 * http://www.smogon.com/stats/2014-11/ou-1695.txt.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return UsageRatedPokemon[]
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
			FROM `usage_rated_pokemon`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemon = new UsageRatedPokemon(
				$month,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				$result['rank'],
				(float) $result['usage_percent']
			);

			$usageRatedPokemons[$result['pokemon_id']] = $usageRatedPokemon;
		}

		return $usageRatedPokemons;
	}

	/**
	 * Get usage rated Pokémon records by their format, rating, and Pokémon.
	 * Use this to create a trend line for a Pokémon's usage in a format.
	 * Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return UsageRatedPokemon[]
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
			FROM `usage_rated_pokemon`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			ORDER BY `month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemon = new UsageRatedPokemon(
				new DateTime($result['month']),
				$formatId,
				$rating,
				$pokemonId,
				$result['rank'],
				(float) $result['usage_percent']
			);

			$usageRatedPokemons[$result['month']] = $usageRatedPokemon;
		}

		return $usageRatedPokemons;
	}
}
