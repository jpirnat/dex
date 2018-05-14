<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItem;
use Jp\Dex\Domain\Stats\Moveset\MovesetRatedItemRepositoryInterface;
use PDO;

class DatabaseMovesetRatedItemRepository implements MovesetRatedItemRepositoryInterface
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
	 * Save a moveset rated item record.
	 *
	 * @param MovesetRatedItem $movesetRatedItem
	 *
	 * @return void
	 */
	public function save(MovesetRatedItem $movesetRatedItem) : void
	{
		$stmt = $this->db->prepare(
			'INSERT INTO `moveset_rated_items` (
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`item_id`,
				`percent`
			) VALUES (
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:item_id,
				:percent
			)'
		);
		$stmt->bindValue(':month', $movesetRatedItem->getMonth()->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $movesetRatedItem->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedItem->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedItem->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $movesetRatedItem->getItemId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedItem->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get moveset rated item records by month, format, rating, and Pokémon.
	 * Indexed by item id value.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByMonthAndFormatAndRatingAndPokemon(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`item_id`,
				`percent`
			FROM `moveset_rated_items`
			WHERE `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':month', $month->format('Y-m-01'), PDO::PARAM_STR);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedItem = new MovesetRatedItem(
				$month,
				$formatId,
				$rating,
				$pokemonId,
				new ItemId($result['item_id']),
				(float) $result['percent']
			);

			$movesetRatedItems[$result['item_id']] = $movesetRatedItem;
		}

		return $movesetRatedItems;
	}

	/**
	 * Get moveset rated item records by their format, rating, Pokémon, and item.
	 * Use this to create a trend line for a Pokémon's item usage in a format.
	 * Indexed and sorted by month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByFormatAndRatingAndPokemonAndItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`month`,
				`percent`
			FROM `moveset_rated_items`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id
				AND `item_id` = :item_id
			ORDER BY `month` ASC'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedItem = new MovesetRatedItem(
				new DateTime($result['month']),
				$formatId,
				$rating,
				$pokemonId,
				$itemId,
				(float) $result['percent']
			);

			$movesetRatedItems[$result['month']] = $movesetRatedItem;
		}

		return $movesetRatedItems;
	}
}
