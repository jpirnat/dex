<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final readonly class MovesetRatedTeammate
{
	/**
	 * Constructor.
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		private UsageRatedPokemonId $usageRatedPokemonId,
		private PokemonId $teammateId,
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
	 * Get the Pokémon id of the teammate.
	 */
	public function getTeammateId() : PokemonId
	{
		return $this->teammateId;
	}

	/**
	 * Get the percent.
	 */
	public function getPercent() : float
	{
		return $this->percent;
	}
}
