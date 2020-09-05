<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class MovesetRatedItem
{
	private UsageRatedPokemonId $usageRatedPokemonId;
	private ItemId $itemId;
	private float $percent;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonId $usageRatedPokemonId
	 * @param ItemId $itemId
	 * @param float $percent
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		UsageRatedPokemonId $usageRatedPokemonId,
		ItemId $itemId,
		float $percent
	) {
		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException('Invalid percent: ' . $percent);
		}

		$this->usageRatedPokemonId = $usageRatedPokemonId;
		$this->itemId = $itemId;
		$this->percent = $percent;
	}

	/**
	 * Get the usage rated Pokémon id.
	 *
	 * @return UsageRatedPokemonId
	 */
	public function getUsageRatedPokemonId() : UsageRatedPokemonId
	{
		return $this->usageRatedPokemonId;
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
	 * Get the percent.
	 *
	 * @return float
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
