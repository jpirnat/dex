<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MonthsCounter;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItem;
use Jp\Dex\Domain\Stats\Moveset\Averaged\MovesetRatedAveragedItemRepositoryInterface;
use PDO;

class DatabaseMovesetRatedAveragedItemRepository implements MovesetRatedAveragedItemRepositoryInterface
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
		$months = $this->monthsCounter->countMonths($start, $end);

		$stmt = $this->db->prepare(
			'SELECT
				`item_id`,
				SUM(`percent`) / :months AS `percent`
			FROM `moveset_rated_items`
			WHERE `month` BETWEEN :start AND :end
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
			GROUP BY `item_id`'
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
