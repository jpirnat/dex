<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;

class StatCalculator
{
	/**
	 * Calculate a Pokémon's HP stat in generations 1 or 2.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 *
	 * @return int
	 */
	public function hp1(int $base, int $iv, int $ev, int $level)
	{
		return (int) floor(((($base + $iv) * 2 + floor(ceil(sqrt($ev)) / 4)) * $level) / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 1 or 2.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 *
	 * @return int
	 */
	public function other1(int $base, int $iv, int $ev, int $level)
	{
		return (int) floor(((($base + $iv) * 2 + floor(ceil(sqrt($ev))/4)) * $level) / 100) + 5;
	}

	/**
	 * Calculate a Pokémon's HP stat in generations 3 and above.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 *
	 * @return int
	 */
	public function hp3(int $base, int $iv, int $ev, int $level)
	{
		// Shedinja hack.
		if ($base === 1) {
			return 1;
		}

		return (int) floor(((2 * $base + $iv + floor($ev / 4)) * $level) / 100) + $level + 10;
	}

	/**
	 * Calculate a Pokémon's non-HP stat in generations 3 and above.
	 *
	 * @param int $base
	 * @param int $iv
	 * @param int $ev
	 * @param int $level
	 * @param float $natureModifier 1.0, 0.9, or 1.1
	 *
	 * @return int
	 */
	public function other3(int $base, int $iv, int $ev, int $level, float $natureModifier)
	{
		return (int) floor((floor(((2 * $base + $iv + floor($ev / 4)) * $level) / 100) + 5) * $natureModifier);
	}

	/**
	 * Calculate a Pokémon's stats in generations 3 and above.
	 *
	 * @param StatValueContainer $baseStats
	 * @param StatValueContainer $ivSpread
	 * @param StatValueContainer $evSpread
	 * @param int $level
	 * @param Nature $nature
	 *
	 * @return StatValueContainer
	 */
	public function all3(
		StatValueContainer $baseStats,
		StatValueContainer $ivSpread,
		StatValueContainer $evSpread,
		int $level,
		Nature $nature
	) : StatValueContainer {
		$statSpread = new StatValueContainer();
		
		// Calculate HP.
		$statId = new StatId(StatId::HP);
		$value = $this->hp3(
			(int) $baseStats->get($statId)->getValue(),
			(int) $ivSpread->get($statId)->getValue(),
			(int) $evSpread->get($statId)->getValue(),
			$level
		);
		$statSpread->add(new StatValue($statId, $value));

		// Calculate other stats.
		$otherStatIds = [
			new StatId(StatId::ATTACK),
			new StatId(StatId::DEFENSE),
			new StatId(StatId::SPECIAL_ATTACK),
			new StatId(StatId::SPECIAL_DEFENSE),
			new StatId(StatId::SPEED),
		];

		foreach ($otherStatIds as $otherStatId) {
			$value = $this->other3(
				(int) $baseStats->get($otherStatId)->getValue(),
				(int) $ivSpread->get($otherStatId)->getValue(),
				(int) $evSpread->get($otherStatId)->getValue(),
				$level,
				$this->getNatureModifier($nature, $otherStatId)
			);

			$statSpread->add(new StatValue($otherStatId, $value));
		}

		return $statSpread;
	}

	/**
	 * Get the nature modifier for this stat.
	 *
	 * @param Nature $nature
	 * @param StatId $statId
	 *
	 * @return float
	 */
	private function getNatureModifier(Nature $nature, StatId $statId) : float
	{
		if ($nature->getIncreasedStatId() === null) {
			return 1;
		}

		if ($nature->getIncreasedStatId()->value() === $statId->value()) {
			return 1.1;
		}

		if ($nature->getDecreasedStatId()->value() === $statId->value()) {
			return .9;
		}

		return 1;
	}
}
