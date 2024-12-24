<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageRatedAveragedPokemon;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageRatedAveragedPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseUsageRatedAveragedPokemonRepository implements UsageRatedAveragedPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
		private MonthsCounter $monthsCounter,
	) {}

	/**
	 * Get usage rated averaged Pokémon records by their start month, end month,
	 * format, and rating.
	 *
	 * @return UsageRatedAveragedPokemon[] Indexed by Pokémon id.
	 */
	public function getByMonthsAndFormatAndRating(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
	) : array {
		$months = $this->monthsCounter->countAllMonths($start, $end);

		// TODO: Add rank to query as window function (once we're using a
		// database that supports window functions).
		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				SUM(`usage_percent`) / :months AS `usage_percent`
			FROM `usage_rated_pokemon`
			WHERE `month` BETWEEN :start AND :end
				AND `format_id` = :format_id
				AND `rating` = :rating
			GROUP BY `pokemon_id`
			ORDER BY
				`usage_percent` DESC,
				`pokemon_id`'
		);
		$stmt->bindValue(':months', $months, PDO::PARAM_INT);
		$stmt->bindValue(':start', $start->format('Y-m-01'));
		$stmt->bindValue(':end', $end->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value, PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedAveragedPokemons = [];
		$rank = 1;

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedAveragedPokemon = new UsageRatedAveragedPokemon(
				$start,
				$end,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				$rank++,
				(float) $result['usage_percent'],
			);

			$usageRatedAveragedPokemons[$result['pokemon_id']] = $usageRatedAveragedPokemon;
		}

		return $usageRatedAveragedPokemons;
	}
}
