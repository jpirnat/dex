<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

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
				`year`,
				`month`,
				`format_id`,
				`rating`,
				`pokemon_id`,
				`item_id`,
				`percent`
			) VALUES (
				:year,
				:month,
				:format_id,
				:rating,
				:pokemon_id,
				:item_id,
				:percent
			)'
		);
		$stmt->bindValue(':year', $movesetRatedItem->getYear(), PDO::PARAM_INT);
		$stmt->bindValue(':month', $movesetRatedItem->getMonth(), PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $movesetRatedItem->getFormatId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $movesetRatedItem->getRating(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $movesetRatedItem->getPokemonId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $movesetRatedItem->getItemId()->value(), PDO::PARAM_INT);
		$stmt->bindValue(':percent', $movesetRatedItem->getPercent(), PDO::PARAM_STR);
		$stmt->execute();
	}

	/**
	 * Get moveset rated item records by year, month, format, rating, and
	 * Pokémon.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndPokemon(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`item_id`,
				`percent`
			FROM `moveset_rated_items`
			WHERE `year` = :year
				AND `month` = :month
				AND `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedItems[] = new MovesetRatedItem(
				$year,
				$month,
				$formatId,
				$rating,
				$pokemonId,
				new ItemId($result['item_id']),
				(float) $result['percent']
			);
		}

		return $movesetRatedItems;
	}

	/**
	 * Get moveset rated item records by format and rating and Pokémon.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByFormatAndRatingAndPokemon(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`,
				`item_id`,
				`percent`
			FROM `moveset_rated_items`
			WHERE `format_id` = :format_id
				AND `rating` = :rating
				AND `pokemon_id` = :pokemon_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedItems[] = new MovesetRatedItem(
				$result['year'],
				$result['month'],
				$formatId,
				$rating,
				$pokemonId,
				new ItemId($result['item_id']),
				(float) $result['percent']
			);
		}

		return $movesetRatedItems;
	}

	/**
	 * Get moveset rated item records by format and Pokémon and item.
	 *
	 * @param FormatId $formatId
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 *
	 * @return MovesetRatedItem[]
	 */
	public function getByFormatAndPokemonAndItem(
		FormatId $formatId,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`year`,
				`month`,
				`rating`,
				`percent`
			FROM `moveset_rated_items`
			WHERE `format_id` = :format_id
				AND `pokemon_id` = :pokemon_id
				AND `item_id` = :item_id'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$movesetRatedItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$movesetRatedItems[] = new MovesetRatedItem(
				$result['year'],
				$result['month'],
				$formatId,
				$result['rating'],
				$pokemonId,
				$itemId,
				(float) $result['percent']
			);
		}

		return $movesetRatedItems;
	}
}
