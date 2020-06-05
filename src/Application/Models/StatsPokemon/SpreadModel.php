<?php
declare(strict_types=1);

namespace Jp\Dex\Application\Models\StatsPokemon;

use DateTime;
use Jp\Dex\Domain\Calculators\StatCalculator;
use Jp\Dex\Domain\Formats\Format;
use Jp\Dex\Domain\Languages\LanguageId;
use Jp\Dex\Domain\Pokemon\PokemonId;
use Jp\Dex\Domain\Spreads\StatsPokemonSpreadRepositoryInterface;
use Jp\Dex\Domain\Stats\BaseStatRepositoryInterface;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;

final class SpreadModel
{
	private StatsPokemonSpreadRepositoryInterface $statsPokemonSpreadRepository;
	private BaseStatRepositoryInterface $baseStatRepository;
	private StatCalculator $statCalculator;

	private array $spreads = [];
	private array $stats = [];


	/**
	 * Constructor.
	 *
	 * @param StatsPokemonSpreadRepositoryInterface $statsPokemonSpreadRepository
	 * @param BaseStatRepositoryInterface $baseStatRepository
	 * @param StatCalculator $statCalculator
	 */
	public function __construct(
		StatsPokemonSpreadRepositoryInterface $statsPokemonSpreadRepository,
		BaseStatRepositoryInterface $baseStatRepository,
		StatCalculator $statCalculator
	) {
		$this->statsPokemonSpreadRepository = $statsPokemonSpreadRepository;
		$this->baseStatRepository = $baseStatRepository;
		$this->statCalculator = $statCalculator;
	}


	/**
	 * Get stat and spread data for the stats Pokémon page.
	 *
	 * @param DateTime $month
	 * @param Format $format
	 * @param int $rating
	 * @param PokemonId $pokemonId
	 * @param LanguageId $languageId
	 *
	 * @return void
	 */
	public function setData(
		DateTime $month,
		Format $format,
		int $rating,
		PokemonId $pokemonId,
		LanguageId $languageId
	) : void {
		$generationId = $format->getGenerationId();

		// Get stat Pokémon spreads.
		$spreads = $this->statsPokemonSpreadRepository->getByMonth(
			$month,
			$format->getId(),
			$rating,
			$pokemonId,
			$languageId
		);

		// Get the Pokémon's base stats.
		$baseStats = $this->baseStatRepository->getByGenerationAndPokemon(
			$generationId,
			$pokemonId
		);

		// Get this generation's stats.
		$statIds = StatId::getByGeneration($generationId);
		$idsToIdentifiers = [
			StatId::HP => 'hp',
			StatId::ATTACK => 'atk',
			StatId::DEFENSE => 'def',
			StatId::SPEED => 'spe',
			StatId::SPECIAL => 'spc',
			StatId::SPECIAL_ATTACK => 'spa',
			StatId::SPECIAL_DEFENSE => 'spd',
		];
		if ($generationId->value() === 1) {
			$this->stats = [
				['value' => 'hp', 'name' => 'HP'],
				['value' => 'atk', 'name' => 'Atk'],
				['value' => 'def', 'name' => 'Def'],
				['value' => 'spc', 'name' => 'Spc'],
				['value' => 'spe', 'name' => 'Spe'],
			];
		} else {
			$this->stats = [
				['value' => 'hp', 'name' => 'HP'],
				['value' => 'atk', 'name' => 'Atk'],
				['value' => 'def', 'name' => 'Def'],
				['value' => 'spa', 'name' => 'SpA'],
				['value' => 'spd', 'name' => 'SpD'],
				['value' => 'spe', 'name' => 'Spe'],
			];
		}

		// Assume the Pokémon has perfect IVs.
		$ivSpread = new StatValueContainer();
		$perfectIv = $this->statCalculator->getPerfectIv($generationId);
		foreach ($statIds as $statId) {
			$ivSpread->add(new StatValue($statId, $perfectIv));
		}

		// Calculate the Pokémon's stats for each spread.
		$this->spreads = [];
		foreach ($spreads as $spread) {
			$evSpread = $spread->getEvs();
			$increasedStatId = $spread->getIncreasedStatId();
			$decreasedStatId = $spread->getDecreasedStatId();

			// Get this spread's calculated stats.
			if ($generationId->value() === 1 || $generationId->value() === 2) {
				// Pokémon Showdown simplifies the stat formula for gens 1 and 2.
				// The real formula takes the square root of the EV. So, we need
				// to give the formula the square of the EV from Showdown.
				$evSpread = new StatValueContainer();
				$calcEvSpread = new StatValueContainer();
				foreach ($statIds as $statId) {
					// For Special, use what was imported as Special Attack.
					$actingStatId = $statId->value() !== StatId::SPECIAL
						? $statId
						: new StatId(StatId::SPECIAL_ATTACK);
					$value = $spread->getEvs()->get($actingStatId)->getValue();

					$evSpread->add(new StatValue($statId, $value));
					$calcEvSpread->add(new StatValue($statId, $value ** 2));
				}

				$statSpread = $this->statCalculator->all1(
					$generationId,
					$baseStats,
					$ivSpread,
					$calcEvSpread,
					$format->getLevel()
				);
			} else {
				$statSpread = $this->statCalculator->all3(
					$baseStats,
					$ivSpread,
					$evSpread,
					$format->getLevel(),
					$increasedStatId,
					$decreasedStatId
				);
			}

			// Convert stat arrays to stat objects.
			$increasedStatId = $increasedStatId !== null
				? $increasedStatId->value()
				: null;
			$decreasedStatId = $decreasedStatId !== null
				? $decreasedStatId->value()
				: null;
			$increasedStat = $idsToIdentifiers[$increasedStatId] ?? null;
			$decreasedStat = $idsToIdentifiers[$decreasedStatId] ?? null;

			$evs = [];
			$stats = [];
			foreach ($statIds as $statId) {
				$identifier = $idsToIdentifiers[$statId->value()];
				$evs[$identifier] = $evSpread->get($statId)->getValue();
				$stats[$identifier] = $statSpread->get($statId)->getValue();
			}

			$this->spreads[] = [
				'nature' => $spread->getNatureName(),
				'increasedStat' => $increasedStat,
				'decreasedStat' => $decreasedStat,
				'evs' => $evs,
				'percent' => $spread->getPercent(),
				'stats' => $stats,
			];
		}
	}


	/**
	 * Get the stats.
	 *
	 * @return array
	 */
	public function getStats() : array
	{
		return $this->stats;
	}

	/**
	 * Get the spreads.
	 *
	 * @return array
	 */
	public function getSpreads() : array
	{
		return $this->spreads;
	}
}
