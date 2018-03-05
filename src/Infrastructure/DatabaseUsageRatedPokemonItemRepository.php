<?php
declare(strict_types=1);

namespace Jp\Dex\Infrastructure;

use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonItem;
use Jp\Dex\Domain\Stats\Usage\Derived\UsageRatedPokemonItemRepositoryInterface;
use PDO;

class DatabaseUsageRatedPokemonItemRepository implements UsageRatedPokemonItemRepositoryInterface
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
	 * Get usage rated Pokémon item records by their year, month, format,
	 * rating, and item. Indexed by Pokémon id value.
	 *
	 * @param int $year
	 * @param int $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param ItemId $itemId
	 *
	 * @return UsageRatedPokemonItem[]
	 */
	public function getByYearAndMonthAndFormatAndRatingAndItem(
		int $year,
		int $month,
		FormatId $formatId,
		int $rating,
		ItemId $itemId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`pokemon_id`,
				`u`.`usage_percent` AS `pokemon_percent`,
				`m`.`percent` AS `item_percent`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_items` AS `m`
				ON `u`.`year` = `m`.`year`
				AND `u`.`month` = `m`.`month`
				AND `u`.`format_id` = `m`.`format_id`
				AND `u`.`rating` = `m`.`rating`
				AND `u`.`pokemon_id` = `m`.`pokemon_id`
			WHERE `u`.`year` = :year
				AND `u`.`month` = :month
				AND `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `m`.`item_id` = :item_id'
		);
		$stmt->bindValue(':year', $year, PDO::PARAM_INT);
		$stmt->bindValue(':month', $month, PDO::PARAM_INT);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemonItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemonItems[$result['pokemon_id']] = new UsageRatedPokemonItem(
				$year,
				$month,
				$formatId,
				$rating,
				new PokemonId($result['pokemon_id']),
				(float) $result['pokemon_percent'],
				$itemId,
				(float) $result['item_percent'],
				(float) $result['usage_percent']
			);
		}

		return $usageRatedPokemonItems;
	}

	/**
	 * Get usage rated Pokémon item records by their format, rating, Pokémon,
	 * and item. Use this to create a trend line for the usage of a specific
	 * Pokémon with a specific item. Indexed and sorted by year then month.
	 *
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param ItemId $itemId
	 *
	 * @return UsageRatedPokemonItem[][]
	 */
	public function getByFormatAndRatingAndPokemonAndItem(
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		ItemId $itemId
	) : array {
		$stmt = $this->db->prepare(
			'SELECT
				`u`.`year`,
				`u`.`month`,
				`u`.`usage_percent` AS `pokemon_percent`,
				`m`.`percent` AS `item_percent`,
				`u`.`usage_percent` * `m`.`percent` / 100 AS `usage_percent`
			FROM `usage_rated_pokemon` AS `u`
			INNER JOIN `moveset_rated_items` AS `m`
				ON `u`.`year` = `m`.`year`
				AND `u`.`month` = `m`.`month`
				AND `u`.`format_id` = `m`.`format_id`
				AND `u`.`rating` = `m`.`rating`
				AND `u`.`pokemon_id` = `m`.`pokemon_id`
			WHERE `u`.`format_id` = :format_id
				AND `u`.`rating` = :rating
				AND `u`.`pokemon_id` = :pokemon_id
				AND `m`.`item_id` = :item_id
			ORDER BY
				`u`.`year`,
				`u`.`month`'
		);
		$stmt->bindValue(':format_id', $formatId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':rating', $rating, PDO::PARAM_INT);
		$stmt->bindValue(':pokemon_id', $pokemonId->value(), PDO::PARAM_INT);
		$stmt->bindValue(':item_id', $itemId->value(), PDO::PARAM_INT);
		$stmt->execute();

		$usageRatedPokemonItems = [];

		while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$usageRatedPokemonItems[$result['year']][$result['month']] = new UsageRatedPokemonItem(
				$result['year'],
				$result['month'],
				$formatId,
				$rating,
				$pokemonId,
				(float) $result['pokemon_percent'],
				$itemId,
				(float) $result['item_percent'],
				(float) $result['usage_percent']
			);
		}

		return $usageRatedPokemonItems;
	}
}
