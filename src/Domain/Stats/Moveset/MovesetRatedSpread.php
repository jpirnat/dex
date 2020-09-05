<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Natures\NatureId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidCountException;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class MovesetRatedSpread
{
	private UsageRatedPokemonId $usageRatedPokemonId;
	private NatureId $natureId;
	private StatValueContainer $evSpread;
	private float $percent;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonId $usageRatedPokemonId
	 * @param NatureId $natureId
	 * @param StatValueContainer $evSpread
	 * @param float $percent
	 *
	 * @throws InvalidCountException if any EV spread values are invalid.
	 * @throws InvalidPercentException if $percent is invalid
	 */
	public function __construct(
		UsageRatedPokemonId $usageRatedPokemonId,
		NatureId $natureId,
		StatValueContainer $evSpread,
		float $percent
	) {
		foreach ($evSpread->getAll() as $statValue) {
			if ($statValue->getValue() < 0 || $statValue->getValue() > 255) {
				throw new InvalidCountException(
					'Invalid number of EVs for stat id '
					. $statValue->getStatId()->value() . '.'
				);
			}
		}

		if ($percent < 0 || $percent > 100) {
			throw new InvalidPercentException('Invalid percent: ' . $percent);
		}

		$this->usageRatedPokemonId = $usageRatedPokemonId;
		$this->natureId = $natureId;
		$this->evSpread = $evSpread;
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
	 * Get the nature id.
	 *
	 * @return NatureId
	 */
	public function getNatureId() : NatureId
	{
		return $this->natureId;
	}

	/**
	 * Get the EV spread.
	 *
	 * @return StatValueContainer
	 */
	public function getEvSpread() : StatValueContainer
	{
		return $this->evSpread;
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
