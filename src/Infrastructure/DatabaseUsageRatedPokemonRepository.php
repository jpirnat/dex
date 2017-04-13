<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\UsageRatedPokemon;
use Jp\Dex\Domain\Stats\UsageRatedPokemonRepositoryInterface;
use PDO;

class DatabaseUsageRatedPokemonRepository implements UsageRatedPokemonRepositoryInterface
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
	 * Do any usage rated Pokémon records exist for this year, month, format,
	 * and rating?
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return bool
	 */
	public function has(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating
	) : bool {
		$stmt = $this->db->prepare(
			'SELECT
				COUNT(*)
			FROM `usage_rated_pokemon`
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
		$stmt->bindValue(':year', $usageRatedPokemon->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $usageRatedPokemon->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $usageRatedPokemon->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $usageRatedPokemon->rating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $usageRatedPokemon->pokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $usageRatedPokemon->rank(), PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $usageRatedPokemon->usagePercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get usage rated Pokémon records by format and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 *
	 * @return UsageRatedPokemon[]
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
			FROM `usage_rated_pokemon`
			WHERE `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemon = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemon[] = new UsageRatedPokemon(
				$result['year'],
				$result['month'],
				$formatId,
				$result['rating'],
				$pokemonId,
				$result['rank'],
				(float) $result['usage_percent']
			);
		}

		return $usageRatedPokemon;
	}
}
