<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use DateTime;
use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\DexPokemonRepositoryInterface;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Spreads\StatsPokemonSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;

final class SpreadModel
{
	private(set) array $spreads = [];


	public function __construct(
		private readonly StatsPokemonSpreadRepositoryInterface $statsPokemonSpreadRepository,
		private readonly StatRepositoryInterface $statRepository,
		private readonly DexPokemonRepositoryInterface $dexPokemonRepository,
		private readonly StatCalculator $statCalculator,
	) {}


	/**
	 * Get stat and spread data for the stats Pokémon page.
	 */
	public function setData(
		DateTime $month,
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId,
	) : void {
		$generationId = $format->generationId;

		$stats = $this->statRepository->getByVersionGroup(
			$format->versionGroupId,
		);

		// Get stat Pokémon spreads.
		$spreads = $this->statsPokemonSpreadRepository->getByMonth(
			$month,
			$format->id,
			$rating,
			$pokemonId,
			$languageId,
		);

		// Get the Pokémon's base stats.
		$pokemon = $this->dexPokemonRepository->getById(
			$format->versionGroupId,
			$pokemonId,
			$languageId,
		);

		// Convert the base stats data structure for use in the stat calculator.
		$baseStats = new StatValueContainer();
		foreach ($stats as $stat) {
			$baseStats->add(new StatValue($stat->getId(), $pokemon->baseStats[$stat->getIdentifier()]));
		}

		$attack = new StatId(StatId::ATTACK);
		$speed = new StatId(StatId::SPEED);

		// Calculate the Pokémon's stats for each spread.
		$this->spreads = [];
		foreach ($spreads as $spread) {
			$evSpread = $spread->evs;
			$increasedStatId = $spread->increasedStatId;
			$decreasedStatId = $spread->decreasedStatId;

			// Assume the Pokémon has perfect IVs.
			$ivSpread = new StatValueContainer();
			$perfectIv = $this->statCalculator->getPerfectIv($generationId);
			foreach ($stats as $stat) {
				$ivSpread->add(new StatValue($stat->getId(), $perfectIv));
			}
			// If it's a minus Attack nature with 0 Attack EVs, assume 0 IV.
			if ($decreasedStatId?->value() === StatId::ATTACK && !$evSpread->get($attack)->getValue()) {
				$ivSpread->add(new StatValue($attack, 0));
			}
			// If it's a minus Speed nature with 0 Speed EVs, assume 0 IV.
			if ($decreasedStatId?->value() === StatId::SPEED && !$evSpread->get($speed)->getValue()) {
				$ivSpread->add(new StatValue($speed, 0));
			}

			// Get this spread's calculated stats.
			if ($generationId->value() === 1 || $generationId->value() === 2) {
				// Pokémon Showdown simplifies the stat formula for gens 1 and 2.
				// The real formula takes the square root of the EV. So, we need
				// to give the formula the square of the EV from Showdown.
				$evSpread = new StatValueContainer();
				$calcEvSpread = new StatValueContainer();
				foreach ($stats as $stat) {
					// For Special, use what was imported as Special Attack.
					$actingStatId = $stat->getId()->value() !== StatId::SPECIAL
						? $stat->getId()
						: new StatId(StatId::SPECIAL_ATTACK);
					$value = $spread->evs->get($actingStatId)->getValue();

					$evSpread->add(new StatValue($stat->getId(), $value));
					$calcEvSpread->add(new StatValue($stat->getId(), $value ** 2));
				}

				$statSpread = $this->statCalculator->all1(
					$generationId,
					$baseStats,
					$ivSpread,
					$calcEvSpread,
					$format->level,
				);
			} else {
				$statSpread = $this->statCalculator->all3(
					$baseStats,
					$ivSpread,
					$evSpread,
					$format->level,
					$increasedStatId,
					$decreasedStatId,
				);
			}

			// Convert stat arrays to stat objects.
			$increasedStatId = $increasedStatId?->value();
			$decreasedStatId = $decreasedStatId?->value();
			$increasedStat = $stats[$increasedStatId]?->getIdentifier() ?? null;
			$decreasedStat = $stats[$decreasedStatId]?->getIdentifier() ?? null;

			$evs = [];
			$finalStats = [];
			foreach ($stats as $stat) {
				$identifier = $stat->getIdentifier();
				$evs[$identifier] = $evSpread->get($stat->getId())->getValue();
				$finalStats[$identifier] = $statSpread->get($stat->getId())->getValue();
			}

			$this->spreads[] = [
				'nature' => $spread->natureName,
				'increasedStat' => $increasedStat,
				'decreasedStat' => $decreasedStat,
				'evs' => $evs,
				'percent' => $spread->percent,
				'stats' => $finalStats,
			];
		}
	}
}
