<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\EvCalculator;

use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class EvCalculatorSubmitModel
{
	private array $evs = [];


	public function __construct(
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly NatureRepositoryInterface $natureRepository,
		private readonly StatRepositoryInterface $statRepository,
		private readonly BaseStatRepositoryInterface $baseStatRepository,
		private readonly StatCalculator $statCalculator,
	) {}


	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		string $natureIdentifier,
		array $ivs,
		array $atLevel,
	) : void {
		$this->evs = [];

		try {
			$versionGroup = $this->vgRepository->getByIdentifier($vgIdentifier);
			$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
			$nature = $this->natureRepository->getByIdentifier($natureIdentifier);
		} catch (VersionGroupNotFoundException
			| PokemonNotFoundException
			| NatureNotFoundException
		) {
			return;
		}

		$stats = $this->statRepository->getByVersionGroup($versionGroup->getId());

		$baseStats = $this->baseStatRepository->getByPokemon(
			$versionGroup->getId(),
			$pokemon->getId(),
		);

		// Initialize the array of possible EVs.
		$possibleEvs = [];
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();
			$possibleEvs[$statIdentifier] = range(0, 252, 4);
		}

		// Then, one stat at a time, one level at a time, rule out as many of
		// those EVs as possible.
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$base = (int) $baseStats[$statIdentifier];
			$iv = (int) ($ivs[$statIdentifier] ?? 0);

			$natureModifier = $this->statCalculator->getNatureModifier(
				$stat->getId(),
				$nature->getIncreasedStatId(),
				$nature->getDecreasedStatId(),
			);

			foreach ($atLevel as $l) {
				$level = (int) ($l['level'] ?? 0);
				$final = (int) ($l['finalStats'][$statIdentifier] ?? 0);

				if (!$level || !$final) {
					continue; // Skip if there are missing required fields.
				}

				foreach ($possibleEvs[$statIdentifier] ?? [] as $possibleEvIndex => $possibleEv) {
					$calculated = $this->statCalculator->gen3Stat(
						$stat->getId(),
						$base,
						$iv,
						$possibleEv,
						$level,
						$natureModifier,
					);
					if ($calculated !== $final) {
						unset($possibleEvs[$statIdentifier][$possibleEvIndex]);
					}
				}
			}
		}

		// We're done. Compile the results.
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$this->evs[$statIdentifier] = $this->formatPossibleEvs($possibleEvs[$statIdentifier]);
		}
	}

	/**
	 * Format an array of possible IVs for a single stat into a readable string.
	 * For example: [0, 4, 8, 20, 32, 36] becomes "0-11, 20-23, 32-39".
	 *
	 * @param int[] $evs
	 */
	private function formatPossibleEvs(array $evs) : string
	{
		$evs = array_values($evs); // Standardize the array indexes.

		if ($evs === []) {
			return 'Not Possible!';
		}

		$chunks = [];

		for ($i = 0; $i < count($evs); $i++) {
			$start = $evs[$i];
			while ($i + 1 < count($evs) && $evs[$i] + 4 === $evs[$i + 1]) {
				$i++;
			}
			$end = $evs[$i] < 252 // TODO: Use max EV per version group.
				? $evs[$i] + 3
				: $evs[$i];

			$chunks[] = "$start-$end";
		}

		return implode(', ', $chunks);
	}


	public function getEvs() : array
	{
		return $this->evs;
	}
}
