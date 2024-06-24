<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Items\ItemId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final readonly class MovesetRatedItem
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		private UsageRatedPokemonId $usageRatedPokemonId,
		private ItemId $itemId,
		private float $percent,
	) {
		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException("Invalid percent: $percent.");
		}
	}

	/**
	 * Get the usage rated Pokémon id.
	 */
	public function getUsageRatedPokemonId() : UsageRatedPokemonId
	{
		return $this->usageRatedPokemonId;
	}

	/**
	 * Get the item id.
	 */
	public function getItemId() : ItemId
	{
		return $this->itemId;
	}

	/**
	 * Get the percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
