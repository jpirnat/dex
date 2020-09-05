<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItem;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItemRepositoryInterface;
use Jp\Dex\Domain\Stats\Usage\Averaged\MonthsCounter;
use PDO;

final class DatabaseMovesetRatedAveragedItemRepository implements MovesetRatedAveragedItemRepositoryInterface
{
	private PDO $db;
	private MonthsCounter $monthsCounter;

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
	 * Get moveset rated averaged item records by their start month, end month,
	 * format, rating, and Pokémon. Indexed by item id value.
	 *
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedAveragedItem[]
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
				`mri`.`item_id`,
				SUM(`mri`.`percent`) / :months AS `percent`
			FROM `usage_rated_pokemon` AS `urp`
			INNER JOIN `moveset_rated_items` AS `mri`
				ON `urp`.`id` = `mri`.`usage_rated_pokemon_id`
			WHERE `urp`.`month` BETWEEN :start AND :end
				AND `urp`.`format_id` = :format_id
				AND `urp`.`rating` = :rating
				AND `urp`.`pokemon_id` = :pokemon_id
			GROUP BY `mri`.`item_id`'
		);
		$stmt->bindValue(':months', $months, PDO::PARAM_INT);
		$stmt->bindValue(':start', $start->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':end', $end->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedAveragedItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedAveragedItem = new MovesetRatedAveragedItem(
				$start,
				$end,
				$formatId,
				$rating,
				$pokemonId,
				new ItemId($result['item_id']),
				(float) $result['percent']
			);

			$movesetRatedAveragedItems[$result['item_id']] = $movesetRatedAveragedItem;
		}

		return $movesetRatedAveragedItems;
	}
}
