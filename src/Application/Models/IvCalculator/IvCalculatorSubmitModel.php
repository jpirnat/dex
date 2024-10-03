<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\IvCalculator;

use Jp\Dex\Domain\Calculators\HiddenPowerCalculator;
use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Characteristics\CharacteristicNotFoundException;
use Jp\Dex\Domain\Characteristics\CharacteristicRepositoryInterface;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
use Jp\Dex\Domain\Types\TypeRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class IvCalculatorSubmitModel
{
	private array $ivs = [];


	public function __construct(
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly NatureRepositoryInterface $natureRepository,
		private readonly CharacteristicRepositoryInterface $characteristicRepository,
		private readonly TypeRepositoryInterface $typeRepository,
		private readonly StatRepositoryInterface $statRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly StatCalculator $statCalculator,
		private readonly HiddenPowerCalculator $hiddenPowerCalculator,
	) {}


	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		string $natureIdentifier,
		string $characteristicIdentifier,
		string $hpTypeIdentifier,
		array $atLevel,
	) : void {
		$this->ivs = [];

		try {
			$versionGroup = $this->vgRepository->getByIdentifier($vgIdentifier);
			$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
			$nature = $this->natureRepository->getByIdentifier($natureIdentifier);
			$characteristic = $characteristicIdentifier !== ''
				? $this->characteristicRepository->getByIdentifier($characteristicIdentifier)
				: null;
			$hpType = $hpTypeIdentifier !== ''
				? $this->typeRepository->getByIdentifier($hpTypeIdentifier)
				: null;
		} catch (VersionGroupNotFoundException
			| PokemonNotFoundException
			| NatureNotFoundException
			| CharacteristicNotFoundException
		) {
			return;
		}

		$stats = $this->statRepository->getByVersionGroup($versionGroup->getId());

		$dexPokemon = $this->dexPokemonRepository->getById(
			$versionGroup->getId(),
			$pokemon->getId(),
			new LanguageId(LanguageId::ENGLISH),
		);
		$baseStats = $dexPokemon->getBaseStats();

		// Initialize the array of possible IVs.
		$possibleIvs = [];
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();
			$possibleIvs[$statIdentifier] = range(0, 31);
		}

		// Then, one stat at a time, one level at a time, rule out as many of
		// those IVs as possible.
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$base = (int) $baseStats[$statIdentifier];

			$natureModifier = $this->statCalculator->getNatureModifier(
				$stat->getId(),
				$nature->getIncreasedStatId(),
				$nature->getDecreasedStatId(),
			);

			foreach ($atLevel as $l) {
				$level = (int) ($l['level'] ?? 0);
				$ev = (int) ($l['evs'][$statIdentifier] ?? 0);
				$final = (int) ($l['finalStats'][$statIdentifier] ?? 0);

				if (!$level || !$final) {
					continue; // Skip if there are missing required fields.
				}

				foreach ($possibleIvs[$statIdentifier] ?? [] as $possibleIvIndex => $possibleIv) {
					$calculated = $this->statCalculator->gen3Stat(
						$stat->getId(),
						$base,
						$possibleIv,
						$ev,
						$level,
						$natureModifier,
					);
					if ($calculated !== $final) {
						unset($possibleIvs[$statIdentifier][$possibleIvIndex]);
					}
				}
			}
		}

		// Use characteristic to further narrow down the options.
		if ($characteristic && $versionGroup->hasCharacteristics()) {
			$highestStatId = $characteristic->getHighestStatId();
			$highestStat = $stats[$highestStatId->value()];
			$highestStatIdentifier = $highestStat->getIdentifier();

			$ivModFive = $characteristic->getIvModFive();

			foreach ($possibleIvs[$highestStatIdentifier] ?? [] as $possibleIvIndex => $possibleIv) {
				if ($possibleIv % 5 !== $ivModFive) {
					unset($possibleIvs[$highestStatIdentifier][$possibleIvIndex]);
				}
			}

			if (count($possibleIvs[$highestStatIdentifier])) {
				$highestPossibleIv = max($possibleIvs[$highestStatIdentifier]);

				foreach ($stats as $stat) {
					$statIdentifier = $stat->getIdentifier();
					if ($statIdentifier === $highestStatIdentifier) {
						continue;
					}

					foreach ($possibleIvs[$statIdentifier] ?? [] as $possibleIvIndex => $possibleIv) {
						if ($possibleIv > $highestPossibleIv) {
							unset($possibleIvs[$statIdentifier][$possibleIvIndex]);
						}
					}
				}
			}

			// And here's some more advanced sleuthing:
			// The minimum possible IV of the highest stat must be higher than
			// the maximum of the minimum possible IVs of all the other stats.
			// (Example: The characteristic says the Attack is 1, 6, 11, 16, 21,
			// 26, or 31. The Defense is known to be 30 or 31. Therefore, the
			// Attack can only be 31.)
			$minimumIvs = [];
			foreach ($stats as $stat) {
				$statIdentifier = $stat->getIdentifier();
				if ($statIdentifier === $highestStatIdentifier) {
					continue;
				}
				if ($possibleIvs[$statIdentifier]) {
					$minimumIvs[$statIdentifier] = min($possibleIvs[$statIdentifier]);
				}
			}
			$maximumOfMinimumIvs = max($minimumIvs);

			foreach ($possibleIvs[$highestStatIdentifier] ?? [] as $possibleIvIndex => $possibleIv) {
				if ($possibleIv < $maximumOfMinimumIvs) {
					unset($possibleIvs[$highestStatIdentifier][$possibleIvIndex]);
				}
			}
		}

		// Use Hidden Power type to further narrow down the options.
		if ($hpType && $versionGroup->hasIvBasedHiddenPower()) {
			// We don't need to test every combination of possible IVs; just
			// every combination of evenness and oddness among possible IVs.

			// For each stat, keep track of the possible sets of least significant bits.
			// [0] if only even possible IVs remain.
			// [1] if only odd possible IVs remain.
			// [0, 1] if a combination of even and odd possible IVs remain.
			// [] if no possible IVs remain.
			$statBits = [];

			// As the bit combinations are checked, if any produce the correct type,
			// put them all in here.
			$possibleBits = [];

			foreach ($stats as $stat) {
				$statIdentifier = $stat->getIdentifier();

				$statBits[$statIdentifier] = [];
				foreach ($possibleIvs[$statIdentifier] as $possibleIv) {
					$statBits[$statIdentifier][$possibleIv % 2] = 1;
				}
				$statBits[$statIdentifier] = array_keys($statBits[$statIdentifier]);

				$possibleBits[$statIdentifier] = [];
			}

			foreach ($statBits['hp'] ?? [] as $hpBit) {
				foreach ($statBits['attack'] ?? [] as $atkBit) {
					foreach ($statBits['defense'] ?? [] as $defBit) {
						foreach ($statBits['speed'] ?? [] as $speBit) {
							foreach ($statBits['special-attack'] ?? [] as $spaBit) {
								foreach ($statBits['special-defense'] ?? [] as $spdBit) {
									$calculatedIndex = $this->hiddenPowerCalculator->gen3TypeIndex(
										$hpBit,
										$atkBit,
										$defBit,
										$speBit,
										$spaBit,
										$spdBit,
									);
									if ($calculatedIndex === $hpType->getHiddenPowerIndex()) {
										$possibleBits['hp'][$hpBit] = 1;
										$possibleBits['attack'][$atkBit] = 1;
										$possibleBits['defense'][$defBit] = 1;
										$possibleBits['speed'][$speBit] = 1;
										$possibleBits['special-attack'][$spaBit] = 1;
										$possibleBits['special-defense'][$spdBit] = 1;
									}
								}
							}
						}
					}
				}
			}

			foreach ($stats as $stat) {
				$statIdentifier = $stat->getIdentifier();

				$possibleBits[$statIdentifier] = array_keys($possibleBits[$statIdentifier]);

				foreach ($possibleIvs[$statIdentifier] ?? [] as $possibleIvIndex => $possibleIv) {
					if (!in_array($possibleIv % 2, $possibleBits[$statIdentifier])) {
						unset($possibleIvs[$statIdentifier][$possibleIvIndex]);
					}
				}
			}

			if ($characteristic && $versionGroup->hasCharacteristics()) {
				// The "maximum of minimums" characteristic check can be run again
				// to rule out more possible IVs after the Hidden Power checks.
				$minimumIvs = [];
				foreach ($stats as $stat) {
					$statIdentifier = $stat->getIdentifier();
					if ($statIdentifier === $highestStatIdentifier) {
						continue;
					}
					$minimumIvs[$statIdentifier] = min($possibleIvs[$statIdentifier]);
				}
				$maximumOfMinimumIvs = max($minimumIvs);

				foreach ($possibleIvs[$highestStatIdentifier] ?? [] as $possibleIvIndex => $possibleIv) {
					if ($possibleIv < $maximumOfMinimumIvs) {
						unset($possibleIvs[$highestStatIdentifier][$possibleIvIndex]);
					}
				}
			}
		}

		// We're done. Compile the results.
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$this->ivs[$statIdentifier] = $this->formatPossibleIvs($possibleIvs[$statIdentifier]);
		}
	}

	/**
	 * Format an array of possible IVs for a single stat into a readable string.
	 * For example: [0, 1, 2, 4, 5, 7, 9, 10] becomes "0-2, 4-5, 7, 9-10".
	 *
	 * @param int[] $ivs
	 */
	private function formatPossibleIvs(array $ivs) : string
	{
		$ivs = array_values($ivs); // Standardize the array indexes.

		if ($ivs === []) {
			return 'Not Possible!';
		}

		$chunks = [];

		for ($i = 0; $i < count($ivs); $i++) {
			$start = $ivs[$i];
			while ($i + 1 < count($ivs) && $ivs[$i] + 1 === $ivs[$i + 1]) {
				$i++;
			}
			$end = $ivs[$i];

			$chunks[] = $start === $end
				? "$start"
				: "$start-$end";
		}

		return implode(', ', $chunks);
	}


	public function getIvs() : array
	{
		return $this->ivs;
	}
}
