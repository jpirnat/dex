<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedAbility;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedAbilityRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use PDO;

class DatabaseMovesetRatedAveragedAbilityRepository implements MovesetRatedAveragedAbilityRepositoryInterface
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
	 * Get moveset rated averaged ability records by their start month, end month,
	 * format, rating, and Pokémon. Indexed by ability id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAveragedAbility[]
	 */
	public function getByMonthsAndFormatAndRatingAndPokemon(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$months = $this->monthsCounter->countMonths($start, $end);

		$stmt = $this->db->prepare(
			'SELECT
				`ability_id`,
				SUM(`percent`) / :months AS `percent`
			FROM `moveset_rated_abilities`
			WHERE `month` BETWEEN :start AND :end
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			GROUP BY `ability_id`'
		);
		$stmt->bindValue(':months', $months, PDO::PARAM_INT);
		$stmt->bindValue(':start', $start->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':end', $end->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedAveragedAbilities = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedAveragedAbility = new MovesetRatedAveragedAbility(
				$start,
				$end,
				$formatId,
				$rating,
				$pokemonId,
				new AbilityId($result['ability_id']),
				(float) $result['percent']
			);

			$movesetRatedAveragedAbilities[$result['ability_id']] = $movesetRatedAveragedAbility;
		}

		return $movesetRatedAveragedAbilities;
	}
}
