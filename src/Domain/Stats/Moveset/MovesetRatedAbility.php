<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Abilities\AbilityId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class MovesetRatedAbility
{
	private UsageRatedPokemonId $usageRatedPokemonId;
	private AbilityId $abilityId;
	private float $percent;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonId $usageRatedPokemonId
	 * @param AbilityId $abilityId
	 * @param float $percent
	 *
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		UsageRatedPokemonId $usageRatedPokemonId,
		AbilityId $abilityId,
		float $percent
	) {
		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException('Invalid percent: ' . $percent);
		}

		$this->usageRatedPokemonId = $usageRatedPokemonId;
		$this->abilityId = $abilityId;
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
	 * Get the ability id.
	 *
	 * @return AbilityId
	 */
	public function getAbilityId() : AbilityId
	{
		return $this->abilityId;
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
