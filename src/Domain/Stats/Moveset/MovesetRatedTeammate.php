<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class MovesetRatedTeammate
{
	private UsageRatedPokemonId $usageRatedPokemonId;
	private PokemonId $teammateId;
	private float $percent;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonId $usageRatedPokemonId
	 * @param PokemonId $teammateId
	 * @param float $percent
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		UsageRatedPokemonId $usageRatedPokemonId,
		PokemonId $teammateId,
		float $percent
	) {
		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException('Invalid percent: ' . $percent);
		}

		$this->usageRatedPokemonId = $usageRatedPokemonId;
		$this->teammateId = $teammateId;
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
	 * Get the Pokémon id of the teammate.
	 *
	 * @return PokemonId
	 */
	public function getTeammateId() : PokemonId
	{
		return $this->teammateId;
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
