<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Jp\Dex\Domain\Versions\GenerationId;

final readonly class StatCalculator
{
	private const PERFECT_IV_GEN_1 = 15;
	private const PERFECT_IV_GEN_3 = 31;
	private const MAX_EV_GEN_1 = 65535;
	private const MAX_EV_GEN_3 = 252;

	/**
	 * Calculate a Pokémon's HP stat in generations 1 or 2.
	 */
	public function hp1(int $base, int $iv, int $ev, int $level) : int
	{
		return (int) floor(((($base + $iv) * 2 + floor(ceil(sqrt($ev)) / 4)) * $level) / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 1 or 2.
	 */
	public function other1(int $base, int $iv, int $ev, int $level) : int
	{
		return (int) floor(((($base + $iv) * 2 + floor(ceil(sqrt($ev))/4)) * $level) / 100) + 5;
	}

	/**
	 * Calculate a Pokémon's stats in generations 1 or 2.
	 */
	public function all1(
		GenerationId $generationId,
		StatValueContainer $baseStats,
		StatValueContainer $ivSpread,
		StatValueContainer $evSpread,
		int $level,
	) : StatValueContainer {
		$statSpread = new StatValueContainer();

		$statIds = StatId::getByGeneration($generationId);
		foreach ($statIds as $statId) {
			if ($statId->value() === StatId::HP) {
				// Calculate HP.
				$value = $this->hp1(
					(int) $baseStats->get($statId)->getValue(),
					(int) $ivSpread->get($statId)->getValue(),
					(int) $evSpread->get($statId)->getValue(),
					$level,
				);
				$statSpread->add(new StatValue($statId, $value));
				continue;
			}

			$value = $this->other1(
				(int) $baseStats->get($statId)->getValue(),
				(int) $ivSpread->get($statId)->getValue(),
				(int) $evSpread->get($statId)->getValue(),
				$level,
			);
			$statSpread->add(new StatValue($statId, $value));
		}

		return $statSpread;
	}

	/**
	 * Calculate a Pokémon's HP stat in generations 3 and above.
	 */
	public function hp3(int $base, int $iv, int $ev, int $level) : int
	{
		// Shedinja hack.
		if ($base === 1) {
			return 1;
		}

		return (int) floor(((2 * $base + $iv + floor($ev / 4)) * $level) / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 3 and above.
	 */
	public function other3(int $base, int $iv, int $ev, int $level, float $natureModifier) : int
	{
		return (int) floor((floor(((2 * $base + $iv + floor($ev / 4)) * $level) / 100) + 5) * $natureModifier);
	}

	/**
	 * Calculate a Pokémon's stats in generations 3 and above.
	 */
	public function all3(
		StatValueContainer $baseStats,
		StatValueContainer $ivSpread,
		StatValueContainer $evSpread,
		int $level,
		?StatId $increasedStatId,
		?StatId $decreasedStatId,
	) : StatValueContainer {
		$statSpread = new StatValueContainer();

		$statIds = StatId::getByGeneration(new GenerationId(3));
		foreach ($statIds as $statId) {
			if ($statId->value() === StatId::HP) {
				// Calculate HP.
				$value = $this->hp3(
					(int) $baseStats->get($statId)->getValue(),
					(int) $ivSpread->get($statId)->getValue(),
					(int) $evSpread->get($statId)->getValue(),
					$level,
				);
				$statSpread->add(new StatValue($statId, $value));
				continue;
			}

			$value = $this->other3(
				(int) $baseStats->get($statId)->getValue(),
				(int) $ivSpread->get($statId)->getValue(),
				(int) $evSpread->get($statId)->getValue(),
				$level,
				$this->getNatureModifier($statId, $increasedStatId, $decreasedStatId),
			);
			$statSpread->add(new StatValue($statId, $value));
		}

		return $statSpread;
	}

	/**
	 * Get the nature modifier for this stat.
	 */
	private function getNatureModifier(StatId $statId, ?StatId $increasedStatId, ?StatId $decreasedStatId) : float
	{
		if ($increasedStatId === null) {
			return 1;
		}

		if ($statId->value() === $increasedStatId->value()) {
			return 1.1;
		}

		if ($statId->value() === $decreasedStatId->value()) {
			return .9;
		}

		return 1;
	}

	/**
	 * Get the perfect IV value for this generation.
	 */
	public function getPerfectIv(GenerationId $generationId) : int
	{
		$generation = $generationId->value();

		if ($generation === 1 || $generation === 2) {
			return self::PERFECT_IV_GEN_1;
		}

		return self::PERFECT_IV_GEN_3;
	}

	/**
	 * Get the max EV value for this generation.
	 */
	public function getMaxEv(GenerationId $generationId) : int
	{
		$generation = $generationId->value();

		if ($generation === 1 || $generation === 2) {
			return self::MAX_EV_GEN_1;
		}

		return self::MAX_EV_GEN_3;
	}
}
