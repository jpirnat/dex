<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsAveragedPokemon;
use Jp\Dex\Domain\Stats\Leads\Averaged\LeadsAveragedPokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use PDO;

final class DatabaseLeadsAveragedPokemonRepository implements LeadsAveragedPokemonRepositoryInterface
{
	/** @var PDO $db */
	private $db;

	/** @var MonthsCounter $monthsCounter */
	private $monthsCounter;

	/**
	 * Constructor.
	 *
	 * @param PDO $db
	 * @param MonthsCounter $monthsCounter
	 */
	public function __construct(PDO $db, MonthsCounter $monthsCounter)
	{
		$this->db = $db;
		$this->monthsCounter = $monthsCounter;
	}

	/**
	 * Get leads averaged Pokémon records by their start month, end month, and
	 * format. Indexed by Pokémon id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 *
	 * @return LeadsAveragedPokemon[]
	 */
	public function getByMonthsAndFormat(
		DateTime $start,
		DateTime $end,
		FormatId $formatId
	) : array {
		$months = $this->monthsCounter->countAllMonths($start, $end);

		$stmt = $this->db->prepare(
			'SELECT
				`pokemon_id`,
				SUM(`raw`) AS `raw`,
				SUM(`raw_percent`) / :months AS `raw_percent`
			FROM `leads_pokemon`
			WHERE `month` BETWEEN :start AND :end
				AND `format_id` = :format_id
			GROUP BY `pokemon_id`'
		);
		$stmt->bindValue(':months', $months, PDO::PARAM_INT);
		$stmt->bindValue(':start', $start->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':end', $end->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$leadsAveragedPokemons = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$leadsAveragedPokemon = new LeadsAveragedPokemon(
				$start,
				$end,
				$formatId,
				new PokemonId($result['pokemon_id']),
				(int) $result['raw'],
				(float) $result['raw_percent']
			);

			$leadsAveragedPokemons[$result['pokemon_id']] = $leadsAveragedPokemon;
		}

		return $leadsAveragedPokemons;
	}
}
