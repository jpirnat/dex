<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Stats\Moveset;

use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Stats\Exceptions\InvalidPercentException;
use Jp\Dex\Domain\Stats\Usage\UsageRatedPokemonId;

final readonly class MovesetRatedCounter
{
	/**
	 * @throws InvalidPercentException if $percentKnockedOut is invalid or if
	 *     $percentSwitchedOut is invalid.
	 */
	public function __construct(
		private(set) UsageRatedPokemonId $usageRatedPokemonId,
		private(set) PokemonId $counterId,
		private(set) float $number1,
		private(set) float $number2,
		private(set) float $number3,
		private(set) float $percentKnockedOut,
		private(set) float $percentSwitchedOut,
	) {
		// TODO: validation for number1, number2, and number3.

		if ($percentKnockedOut < 0 || $percentKnockedOut > 100) {
			throw new InvalidPercentException(
				"Invalid percent knocked out: $percentKnockedOut."
			);
		}

		if ($percentSwitchedOut < 0 || $percentSwitchedOut > 100) {
			throw new InvalidPercentException(
				"Invalid percent switched out: $percentSwitchedOut."
			);
		}
	}
}
