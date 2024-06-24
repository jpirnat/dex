<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageAveragedPokemon;
use Jp\Dex\Domain\Stats\Usage\Averaged\UsageAveragedPokemonRepositoryInterface;
use PDO;

final readonly class DatabaseUsageAveragedPokemonRepository implements UsageAveragedPokemonRepositoryInterface
{
	public function __construct(
		private PDO $db,
		private MonthsCounter $monthsCounter,
	) {}

	/**
	 * Get usage averaged Pokémon records by their start month, end month, and
	 * format.
	 *
	 * @return UsageAveragedPokemon[] Indexed by Pokémon id.
	 */
	public function getByMonthsAndFormat(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
	) : array {
		$months = $this->monthsCounter->countAllMonths($start, $end);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				SUM(`raw`) AS `raw`,
				SUM(`raw_percent`) / :months1 AS `raw_percent`,
				SUM(`real`) AS `real`,
				SUM(`real_percent`) / :months2 AS `real_percent`
			FROM `usage_pokemon`
			WHERE `month` BETWEEN :start AND :end
				AND `format_id` = :format_id
			GROUP BY `pokemon_id`'
		);
		$stmt->bindValue(':months1', $months, PDO::PARAM_INT);
		$stmt->bindValue(':months2', $months, PDO::PARAM_INT);
		$stmt->bindValue(':start', $start->format('Y-m-01'));
		$stmt->bindValue(':end', $end->format('Y-m-01'));
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageAveragedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageAveragedPokemon = new UsageAveragedPokemon(
				$start,
				$end,
				$formatId,
				new PokemonId($result['pokemon_id']),
				(int) $result['raw'],
				(float) $result['raw_percent'],
				(int) $result['real'],
				(float) $result['real_percent'],
			);

			$usageAveragedPokemons[$result['pokemon_id']] = $usageAveragedPokemon;
		}

		return $usageAveragedPokemons;
	}
}
