<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\IvCalculator;

use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Characteristics\CharacteristicNotFoundException;
use Jp\Dex\Domain\Characteristics\CharacteristicRepositoryInterface;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class IvCalculatorSubmitModel
{
	private array $ivs = [];


	public function __construct(
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly NatureRepositoryInterface $natureRepository,
		private readonly CharacteristicRepositoryInterface $characteristicRepository,
		private readonly StatRepositoryInterface $statRepository,
		private readonly BaseStatRepositoryInterface $baseStatRepository,
		private readonly StatCalculator $statCalculator,
	) {}


	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		string $natureIdentifier,
		string $characteristicIdentifier,
		array $atLevel,
	) : void {
		$this->ivs = [];

		$versionGroup = $this->vgRepository->getByIdentifier($vgIdentifier);

		try {
			$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
			$nature = $this->natureRepository->getByIdentifier($natureIdentifier);
			$characteristic = $characteristicIdentifier !== ''
				? $this->characteristicRepository->getByIdentifier($characteristicIdentifier)
				: null;
		} catch (PokemonNotFoundException
			| NatureNotFoundException
			| CharacteristicNotFoundException
		) {
			return;
		}

		$stats = $this->statRepository->getByVersionGroup($versionGroup->getId());

		$baseStats = $this->baseStatRepository->getByPokemon(
			$versionGroup->getId(),
			$pokemon->getId(),
		);

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
		if ($characteristic) {
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
				$minimumIvs[$statIdentifier] = min($possibleIvs[$statIdentifier]);
			}
			$maximumOfMinimumIvs = max($minimumIvs);

			foreach ($possibleIvs[$highestStatIdentifier] ?? [] as $possibleIvIndex => $possibleIv) {
				if ($possibleIv < $maximumOfMinimumIvs) {
					unset($possibleIvs[$highestStatIdentifier][$possibleIvIndex]);
				}
			}
		}

		// Use Hidden Power type to further narrow down the options.
		// TODO

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
