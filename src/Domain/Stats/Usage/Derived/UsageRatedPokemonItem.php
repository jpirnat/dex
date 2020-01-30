<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Usage\Derived;

use DateTime;
use Jp\Dex\Domain\Formats\FormatId;
use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidMonthException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRatingException;
use Jp\Dex\Domain\Stats\UsageDataInterface;
use Jp\Dex\Domain\Stats\ValidateMonthTrait;

/**
 * This class holds data derived from the join of a usage rated Pokémon record
 * and a moveset rated item record. The $pokemonPercent property should come
 * from `usage_rated_pokemon`.`usage_percent`. The $itemPercent property should
 * come from `moveset_rated_items`.`percent`. The $usagePercent property should
 * be those two values multiplied together (and divided by 100 to keep it a
 * percent).
 */
final class UsageRatedPokemonItem implements UsageDataInterface
{
	use ValidateMonthTrait;

	private DateTime $month;
	private FormatId $formatId;
	private int $rating;
	private PokemonId $pokemonId;
	private float $pokemonPercent;
	private ItemId $itemId;
	private float $itemPercent;
	private float $usagePercent;

	/**
	 * Constructor.
	 *
	 * @param DateTime $month
	 * @param FormatId $formatId
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param float $pokemonPercent
	 * @param ItemId $itemId
	 * @param float $itemPercent
	 * @param float $usagePercent
	 *
	 * @throws InvalidMonthException if $month is invalid.
	 * @throws InvalidRatingException if $rating is invalid.
	 * @throws InvalidPercentException if $pokemonPercent, $itemPercent, or
	 *     $usagePercent is invalid.
	 */
	public function __construct(
		DateTime $month,
		FormatId $formatId,
		int $rating,
		PokemonId $pokemonId,
		float $pokemonPercent,
		ItemId $itemId,
		float $itemPercent,
		float $usagePercent
	) {
		$this->validateMonth($month);

		if ($rating < 0) {
			throw new InvalidRatingException('Invalid rating: ' . $rating);
		}

		if ($pokemonPercent < 0 || $pokemonPercent > 100) {
			throw new InvalidPercentException(
				'Invalid Pokémon percent: ' . $pokemonPercent
			);
		}

		if ($itemPercent < 0 || $itemPercent > 100) {
			throw new InvalidPercentException(
				'Invalid item percent: ' . $itemPercent
			);
		}

		if ($usagePercent < 0 || $usagePercent > 100) {
			throw new InvalidPercentException(
				'Invalid usage percent: ' . $usagePercent
			);
		}

		$this->month = $month;
		$this->formatId = $formatId;
		$this->rating = $rating;
		$this->pokemonId = $pokemonId;
		$this->pokemonPercent = $pokemonPercent;
		$this->itemId = $itemId;
		$this->itemPercent = $itemPercent;
		$this->usagePercent = $usagePercent;
	}

	/**
	 * Get the month.
	 *
	 * @return DateTime
	 */
	public function getMonth() : DateTime
	{
		return $this->month;
	}

	/**
	 * Get the format id.
	 *
	 * @return FormatId
	 */
	public function getFormatId() : FormatId
	{
		return $this->formatId;
	}

	/**
	 * Get the rating.
	 *
	 * @return int
	 */
	public function getRating() : int
	{
		return $this->rating;
	}

	/**
	 * Get the Pokémon id.
	 *
	 * @return PokemonId
	 */
	public function getPokemonId() : PokemonId
	{
		return $this->pokemonId;
	}

	/**
	 * Get the usage percent of the Pokémon.
	 *
	 * @return float
	 */
	public function getPokemonPercent() : float
	{
		return $this->pokemonPercent;
	}

	/**
	 * Get the item id.
	 *
	 * @return ItemId
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the usage percent of the item on the Pokémon.
	 *
	 * @return float
	 */
	public function getItemPercent() : float
	{
		return $this->itemPercent;
	}

	/**
	 * Get the usage percent of the Pokémon and item combination.
	 *
	 * @return float
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}
}
