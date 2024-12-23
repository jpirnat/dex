<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Stats\Exceptions\InvalidAverageWeightException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final readonly class MovesetRatedPokemon
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidAverageWeightException if $averageWeight is invalid.
	 */
	public function __construct(
		private(set) UsageRatedPokemonId $usageRatedPokemonId,
		private(set) float $averageWeight,
	) {
		if ($averageWeight < 0) {
			throw new InvalidAverageWeightException(
				"Invalid average weight: $averageWeight."
			);
		}
	}
}
