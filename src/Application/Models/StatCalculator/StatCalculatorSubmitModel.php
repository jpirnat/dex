<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatCalculator;

use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Natures\NatureNotFoundException;
use Jp\Dex\Domain\Natures\NatureRepositoryInterface;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonNotFoundException;
use Jp\Dex\Domain\Pokemon\PokemonRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
use Jp\Dex\Domain\Versions\VersionGroupNotFoundException;
use Jp\Dex\Domain\Versions\VersionGroupRepositoryInterface;

final class StatCalculatorSubmitModel
{
	private(set) array $finalStats = [];
	private(set) int $cp = 0;


	public function __construct(
		private readonly VersionGroupRepositoryInterface $vgRepository,
		private readonly PokemonRepositoryInterface $pokemonRepository,
		private readonly NatureRepositoryInterface $natureRepository,
		private readonly StatRepositoryInterface $statRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly StatCalculator $calculator,
	) {}


	public function setData(
		string $vgIdentifier,
		string $pokemonIdentifier,
		string $natureIdentifier,
		string $level,
		string $friendship,
		array $ivs,
		array $evs,
		array $avs,
		array $effortLevels,
	) : void {
		$this->finalStats = [];
		$this->cp = 0;

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

		$level = (int) $level;
		$friendship = (int) $friendship;

		$stats = $this->statRepository->getByVersionGroup($versionGroup->id);

		$dexPokemon = $this->dexPokemonRepository->getById(
			$versionGroup->id,
			$pokemon->id,
			new LanguageId(LanguageId::ENGLISH),
		);
		$baseStats = $dexPokemon->baseStats;

		match ($versionGroup->statFormulaType) {
			'gen1' => $this->gen1Stats(
				$stats,
				$baseStats,
				$level,
				$ivs,
				$evs,
			),
			'gen3' => $this->gen3Stats(
				$stats,
				$baseStats,
				$level,
				$nature,
				$ivs,
				$evs,
			),
			'lgpe' => $this->letsGoStats(
				$stats,
				$baseStats,
				$level,
				$nature,
				$friendship,
				$ivs,
				$avs,
			),
			'legends' => $this->legendsStats(
				$stats,
				$baseStats,
				$level,
				$nature,
				$effortLevels,
			),
		};
	}

	private function gen1Stats(
		array $stats,
		array $baseStats,
		int $level,
		array $ivs,
		array $evs,
	) : void {
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$base = (int) ($baseStats[$statIdentifier] ?? 0);
			$dv = (int) ($ivs[$statIdentifier] ?? 0);
			$statexp = (int) ($evs[$statIdentifier] ?? 0);

			$finalStat = match ($stat->getId()->value()) {
				StatId::HP => $this->calculator->gen1Hp($base, $dv, $statexp, $level),
				default => $this->calculator->gen1Other($base, $dv, $statexp, $level),
			};

			$this->finalStats[$statIdentifier] = $finalStat;
		}
	}

	private function gen3Stats(
		array $stats,
		array $baseStats,
		int $level,
		Nature $nature,
		array $ivs,
		array $evs,
	) : void {
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$base = (int) ($baseStats[$statIdentifier] ?? 0);
			$iv = (int) ($ivs[$statIdentifier] ?? 0);
			$ev = (int) ($evs[$statIdentifier] ?? 0);

			$natureModifier = $this->calculator->getNatureModifier(
				$stat->getId(),
				$nature->increasedStatId,
				$nature->decreasedStatId,
			);

			$finalStat = match ($stat->getId()->value()) {
				StatId::HP => $this->calculator->gen3Hp($base, $iv, $ev, $level),
				default => $this->calculator->gen3Other($base, $iv, $ev, $level, $natureModifier),
			};

			$this->finalStats[$statIdentifier] = $finalStat;
		}
	}

	private function letsGoStats(
		array $stats,
		array $baseStats,
		int $level,
		Nature $nature,
		int $friendship,
		array $ivs,
		array $avs,
	) : void {
		$friendshipModifier = $this->calculator->letsGoFriendshipModifier($friendship);

		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$base = (int) ($baseStats[$statIdentifier] ?? 0);
			$iv = (int) ($ivs[$statIdentifier] ?? 0);
			$av = (int) ($avs[$statIdentifier] ?? 0);

			$natureModifier = $this->calculator->getNatureModifier(
				$stat->getId(),
				$nature->increasedStatId,
				$nature->decreasedStatId,
			);

			$finalStat = match ($stat->getId()->value()) {
				StatId::HP => $this->calculator->letsGoHp($base, $iv, $av, $level),
				default => $this->calculator->letsGoOther($base, $iv, $av, $level, $natureModifier, $friendshipModifier),
			};

			$this->finalStats[$statIdentifier] = $finalStat;
		}

		$this->cp = $this->calculator->letsGoCp($level, $this->finalStats, $avs);
	}

	private function legendsStats(
		array $stats,
		array $baseStats,
		int $level,
		Nature $nature,
		array $effortLevels,
	) : void {
		foreach ($stats as $stat) {
			$statIdentifier = $stat->getIdentifier();

			$base = (int) ($baseStats[$statIdentifier] ?? 0);
			$effortLevel = (int) ($effortLevels[$statIdentifier] ?? 0);

			$natureModifier = $this->calculator->getNatureModifier(
				$stat->getId(),
				$nature->increasedStatId,
				$nature->decreasedStatId,
			);

			$finalStat = match ($stat->getId()->value()) {
				StatId::HP => $this->calculator->legendsHp($base, $level, $effortLevel),
				default => $this->calculator->legendsOther($base, $level, $effortLevel, $natureModifier),
			};

			$this->finalStats[$statIdentifier] = $finalStat;
		}
	}
}
