<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Stats\Exceptions\InvalidAverageWeightException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class MovesetRatedPokemon
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidAverageWeightException if $averageWeight is invalid.
	 */
	public function __construct(
		private UsageRatedPokemonId $usageRatedPokemonId,
		private float $averageWeight,
	) {
		if ($averageWeight < 0) {
			throw new InvalidAverageWeightException(
				'Invalid average weight: ' . $averageWeight
			);
		}
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
	 * Get the average weight.
	 *
	 * @return float
	 */
	public function getAverageWeight() : float
	{
		return $this->averageWeight;
	}
}
