<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemon;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsRatedAveragedPokemonRepositoryInterface;
use PDO;

class DatabaseLeadsRatedAveragedPokemonRepository implements LeadsRatedAveragedPokemonRepositoryInterface
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
	 * Get leads rated averaged Pokémon records by their start month, end month,
	 * format, and rating. Indexed by Pokémon id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 *
	 * @return LeadsRatedAveragedPokemon[]
	 */
	public function getByMonthsAndFormatAndRating(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating
	) : array {
		// TODO: Add rank to query as window function (once we're using a
		// database that supports window functions).
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				AVG(`usage_percent`) AS `usage_percent`
			FROM `leads_rated_pokemon`
			WHERE `month` BETWEEN :start AND :end
				AND `format_id` = :format_id
				AND `rating` = :rating
			GROUP BY `pokemon_id`
			ORDER BY
				`usage_percent` DESC,
				`pokemon_id` ASC'
		);
		$stmt->bindValue(':start', $start->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':end', $end->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();

		$leadsRatedAveragedPokemons = [];
		$rank = 1;

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsRatedAveragedPokemon = new LeadsRatedAveragedPokemon(
				$start,
				$end,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				$rank++,
				$result['usage_percent']
			);

			$leadsRatedAveragedPokemons[$result['pokemon_id']] = $leadsRatedAveragedPokemon;
		}

		return $leadsRatedAveragedPokemons;
	}
}
