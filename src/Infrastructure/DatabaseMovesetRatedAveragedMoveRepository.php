<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedMove;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedMoveRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use PDO;

final class DatabaseMovesetRatedAveragedMoveRepository implements MovesetRatedAveragedMoveRepositoryInterface
{
	public function __construct(
		private PDO $db,
		private MonthsCounter $monthsCounter,
	) {}

	/**
	 * Get moveset rated averaged move records by their start month, end month,
	 * format, rating, and Pokémon. Indexed by move id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAveragedMove[]
	 */
	public function getByMonthsAndFormatAndRatingAndPokemon(
		DateTime $start,
		DateTime $end,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$months = $this->monthsCounter->countMovesetMonths(
			$start,
			$end,
			$formatId,
			$rating,
			$pokemonId
		);

		$stmt = $this->db->prepare(
			'SELECT
				`mrm`.`move_id`,
				SUM(`mrm`.`percent`) / :months AS `percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_moves` AS `mrm`
				ON `urp`.`id` = `mrm`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` BETWEEN :start AND :end
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
			GROUP BY `mrm`.`move_id`'
		);
		$stmt->bindValue(':months', $months, PDO::PARAM_INT);
		$stmt->bindValue(':start', $start->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':end', $end->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedAveragedMoves = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedAveragedMove = new MovesetRatedAveragedMove(
				$start,
				$end,
				$formatId,
				$rating,
				$pokemonId,
				new MoveId($result['move_id']),
				(float) $result['percent']
			);

			$movesetRatedAveragedMoves[$result['move_id']] = $movesetRatedAveragedMove;
		}

		return $movesetRatedAveragedMoves;
	}
}
