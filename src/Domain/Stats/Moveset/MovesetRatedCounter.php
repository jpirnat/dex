<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final class MovesetRatedCounter
{
	private UsageRatedPokemonId $usageRatedPokemonId;
	private PokemonId $counterId;
	private float $number1;
	private float $number2;
	private float $number3;
	private float $percentKnockedOut;
	private float $percentSwitchedOut;

	/**
	 * Constructor.
	 *
	 * @param UsageRatedPokemonId $usageRatedPokemonId
	 * @param PokemonId $counterId
	 * @param float $number1
	 * @param float $number2
	 * @param float $number3
	 * @param float $percentKnockedOut
	 * @param float $percentSwitchedOut
	 *
	 * @throws InvalidPercentException if $percentKnockedOut is invalid or if
	 *     $percentSwitchedOut is invalid.
	 */
	public function __construct(
		UsageRatedPokemonId $usageRatedPokemonId,
		PokemonId $counterId,
		float $number1,
		float $number2,
		float $number3,
		float $percentKnockedOut,
		float $percentSwitchedOut
	) {
		// TODO: validation for number1, number2, and number3.

		if ($percentKnockedOut < 0 || $percentKnockedOut > 100) {
			throw new InvalidPercentException(
				'Invalid percent knocked out: ' . $percentKnockedOut
			);
		}

		if ($percentSwitchedOut < 0 || $percentSwitchedOut > 100) {
			throw new InvalidPercentException(
				'Invalid percent switched out: ' . $percentSwitchedOut
			);
		}

		$this->usageRatedPokemonId = $usageRatedPokemonId;
		$this->counterId = $counterId;
		$this->number1 = $number1;
		$this->number2 = $number2;
		$this->number3 = $number3;
		$this->percentKnockedOut = $percentKnockedOut;
		$this->percentSwitchedOut = $percentSwitchedOut;
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
	 * Get the Pokémon id of the counter.
	 *
	 * @return PokemonId
	 */
	public function getCounterId() : PokemonId
	{
		return $this->counterId;
	}

	/**
	 * Get the numeric score for the counter. This is calculated by the formula
	 * (number2 - 4 * number3). According to Antar, it is "an attempt to remove
	 * bias towards low-probability match ups."
	 *
	 * @return float
	 */
	public function getNumber1() : float
	{
		return $this->number1;
	}

	/**
	 * Get the number2. This is the percent of [encounters between the Pokémon
	 * and the counter that ended in one of them being knocked out or switched
	 * out] where the Pokémon is the one that was knocked out or switched out.
	 *
	 * @return float
	 */
	public function getNumber2() : float
	{
		return $this->number2;
	}

	/**
	 * Get the number3. This is the standard deviation for number2.
	 *
	 * @return float
	 */
	public function getNumber3() : float
	{
		return $this->number3;
	}

	/**
	 * Get the percent knocked out.
	 *
	 * @return float
	 */
	public function getPercentKnockedOut() : float
	{
		return $this->percentKnockedOut;
	}

	/**
	 * Get the percent switched out.
	 *
	 * @return float
	 */
	public function getPercentSwitchedOut() : float
	{
		return $this->percentSwitchedOut;
	}
}
