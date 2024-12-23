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
		private(set) UsageRatedPokemonId $usageRatedPokemonId,
		private(set) int $rank,
		private(set) float $usagePercent,
	) {
		if ($rank < 1) {
			throw new InvalidRankException("Invalid rank: $rank.");
		}

		if ($usagePercent < 0 || $usagePercent > 100) {
			throw new InvalidPercentException("Invalid usage percent: $usagePercent.");
		}
	}
}
