<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\EvCalculator;

use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
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
		string $level,
		string $natureIdentifier,
		array $ivs,
		array $finalStats,
	) : void {
		$this->evs = [];

		$level = (int) $level;

		$versionGroup = $this->vgRepository->getByIdentifier($vgIdentifier);
		$pokemon = $this->pokemonRepository->getByIdentifier($pokemonIdentifier);
		$nature = $this->natureRepository->getByIdentifier($natureIdentifier);

		$stats = $this->statRepository->getByVersionGroup($versionGroup->getId());

		$baseStats = $this->baseStatRepository->getByPokemon(
			$versionGroup->getId(),
			$pokemon->getId(),
		);

		$possibleEvs = [];

		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			if (($ivs[$statIdentifier] ?? '') === ''
				|| ($finalStats[$statIdentifier] ?? '') === ''
			) {
				continue; // Skip stats without user input.
			}

			$base = (int) $baseStats[$statIdentifier];
			$iv = (int) $ivs[$statIdentifier];
			$final = (int) $finalStats[$statIdentifier];

			$natureModifier = $this->statCalculator->getNatureModifier(
				$stat->getId(),
				$nature->getIncreasedStatId(),
				$nature->getDecreasedStatId(),
			);

			foreach (range(0, 252, 4) as $ev) {
				$calculated = $this->statCalculator->gen3Stat(
					$stat->getId(),
					$base,
					$iv,
					$ev,
					$level,
					$natureModifier,
				);
				if ($calculated === $final) {
					$possibleEvs[$statIdentifier][] = $ev;
				}
			}
		}

		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			if (($ivs[$statIdentifier] ?? '') === ''
				|| ($finalStats[$statIdentifier] ?? '') === ''
			) {
				$this->evs[$statIdentifier] = '';
				continue;
			}

			if (count($possibleEvs[$statIdentifier] ?? []) === 0) {
				$this->evs[$statIdentifier] = 'Not Possible!';
				continue;
			}

			$min = min($possibleEvs[$statIdentifier] ?? [0]);
			$max = max($possibleEvs[$statIdentifier] ?? [0]);
			if ($min !== $max) {
				$this->evs[$statIdentifier] = "$min - $max";
				continue;
			}

			$this->evs[$statIdentifier] = $max;
		}
	}


	public function getEvs() : array
	{
		return $this->evs;
	}
}
