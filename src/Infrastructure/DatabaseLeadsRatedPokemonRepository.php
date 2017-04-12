<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Usage\LeadsRatedPokemon;
use Jp\Dex\Domain\Usage\LeadsRatedPokemonRepositoryInterface;
use PDO;

class DatabaseLeadsRatedPokemonRepository implements LeadsRatedPokemonRepositoryInterface
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
	public function has(
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
		$stmt->bindValue(':year', $leadsRatedPokemon->year(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $leadsRatedPokemon->month(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $leadsRatedPokemon->formatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $leadsRatedPokemon->rating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $leadsRatedPokemon->pokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rank', $leadsRatedPokemon->rank(), PDO::PARAM_INT);
		$stmt->bindValue(':usage_percent', $leadsRatedPokemon->usagePercent(), PDO::PARAM_STR);
		$stmt->execute();
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

		$leadsRatedPokemon = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsRatedPokemon[] = new LeadsRatedPokemon(
				$result['year'],
				$result['month'],
				$formatId,
				$result['rating'],
				$pokemonId,
				$result['rank'],
				(float) $result['usage_percent']
			);
		}

		return $leadsRatedPokemon;
	}
}
