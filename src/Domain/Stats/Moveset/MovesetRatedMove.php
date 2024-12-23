<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Moves\MoveId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final readonly class MovesetRatedMove
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		private(set) UsageRatedPokemonId $usageRatedPokemonId,
		private(set) MoveId $moveId,
		private(set) float $percent,
	) {
		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException("Invalid percent: $percent.");
		}
	}
}
