<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\EvCalculator;

use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class EvCalculatorSubmitModel
{
	private(set) array $evs = [];


	public function __construct(
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly NatureRepositoryInterface $natureRepository,
		private readonly StatRepositoryInterface $statRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
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

		$dexPokemon = $this->dexPokemonRepository->getById(
			$versionGroup->getId(),
			$pokemon->id,
			new LanguageId(LanguageId::ENGLISH),
		);
		$baseStats = $dexPokemon->baseStats;

		// Initialize the array of possible EVs.
		$possibleEvs = [];
		foreach ($stats as $stat) {
			$statIdentifier = $stat->identifier;
			$possibleEvs[$statIdentifier] = range(0, $versionGroup->getMaxEvsPerStat());
		}

		// Then, one stat at a time, one level at a time, rule out as many of
		// those EVs as possible.
		foreach ($stats as $stat) {
			$statIdentifier = $stat->identifier;

			$base = $baseStats[$statIdentifier];
			$iv = (int) ($ivs[$statIdentifier] ?? 0);

			$natureModifier = $this->statCalculator->getNatureModifier(
				$stat->id,
				$nature->increasedStatId,
				$nature->decreasedStatId,
			);

			foreach ($atLevel as $l) {
				$level = (int) ($l['level'] ?? 0);
				$final = (int) ($l['finalStats'][$statIdentifier] ?? 0);

				if (!$level || !$final) {
					continue; // Skip if there are missing required fields.
				}

				foreach ($possibleEvs[$statIdentifier] ?? [] as $possibleEvIndex => $possibleEv) {
					$calculated = $this->statCalculator->gen3Stat(
						$stat->id,
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

		// If any stat has a nonzero minimum, those minimums can be subtracted
		// from the overall pool of 510 available EVs. If that remainder is less
		// than 252, we might be able to rule out some upper bounds on EV ranges.
		$minimumEvs = [];
		foreach ($stats as $stat) {
			$statIdentifier = $stat->identifier;
			if ($possibleEvs[$statIdentifier]) {
				$minimumEvs[] = min($possibleEvs[$statIdentifier]);
			}
		}
		$availableEvs = 510 - array_sum($minimumEvs);
		foreach ($stats as $stat) {
			$statIdentifier = $stat->identifier;
			if ($possibleEvs[$statIdentifier]) {
				$minimumEv = min($possibleEvs[$statIdentifier]);
				foreach ($possibleEvs[$statIdentifier] as $possibleEvIndex => $possibleEv) {
					if ($possibleEv > $minimumEv && $possibleEv > $availableEvs) {
						unset($possibleEvs[$statIdentifier][$possibleEvIndex]);
					}
				}
			}
		}

		// We're done. Compile the results.
		foreach ($stats as $stat) {
			$statIdentifier = $stat->identifier;

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
			while ($i + 1 < count($evs) && $evs[$i] + 1 === $evs[$i + 1]) {
				$i++;
			}
			$end = $evs[$i];

			$chunks[] = $start === $end
				? "$start"
				: "$start-$end";
		}

		return implode(', ', $chunks);
	}
}
