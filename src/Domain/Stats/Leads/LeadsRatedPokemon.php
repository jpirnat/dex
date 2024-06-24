<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Leads;

use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidRankException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final readonly class LeadsRatedPokemon
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidRankException if $rank is invalid.
	 * @throws InvalidPercentException if $usagePercent is invalid
	 */
	public function __construct(
		private UsageRatedPokemonId $usageRatedPokemonId,
		private int $rank,
		private float $usagePercent,
	) {
		if ($rank < 1) {
			throw new InvalidRankException("Invalid rank: $rank.");
		}

		if ($usagePercent < 0 || $usagePercent > 100) {
			throw new InvalidPercentException("Invalid usage percent: $usagePercent.");
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
	 * Get the rank.
	 */
	public function getRank() : int
	{
		return $this->rank;
	}

	/**
	 * Get the usage percent.
	 */
	public function getUsagePercent() : float
	{
		return $this->usagePercent;
	}
}
