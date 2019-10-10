<?php
declare(strict_types=1);

namespace Jp\Dex\Domain\Calculators;

use Jp\Dex\Domain\Natures\Nature;
use Jp\Dex\Domain\Stats\StatId;
use Jp\Dex\Domain\Stats\StatValue;
use Jp\Dex\Domain\Stats\StatValueContainer;
use Jp\Dex\Domain\Versions\GenerationId;

final class StatCalculator
{
	/** @var int $PERFECT_IV_GEN_1 */
	private const PERFECT_IV_GEN_1 = 15;

	/** @var int $PERFECT_IV_GEN_3 */
	private const PERFECT_IV_GEN_3 = 31;

	/** @var int $MAX_EV_GEN_1 */
	private const MAX_EV_GEN_1 = 65535;

	/** @var int $MAX_EV_GEN_3 */
	private const MAX_EV_GEN_3 = 252;

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
	 * Calculate a Pokémon's stats in generations 1 or 2.
	 *
	 * @param GenerationId $generationId
	 * @param StatValueContainer $baseStats
	 * @param StatValueContainer $ivSpread
	 * @param StatValueContainer $evSpread
	 * @param int $level
	 *
	 * @return StatValueContainer
	 */
	public function all1(
		GenerationId $generationId,
		StatValueContainer $baseStats,
		StatValueContainer $ivSpread,
		StatValueContainer $evSpread,
		int $level
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
					$level
				);
				$statSpread->add(new StatValue($statId, $value));
				continue;
			}

			$value = $this->other1(
				(int) $baseStats->get($statId)->getValue(),
				(int) $ivSpread->get($statId)->getValue(),
				(int) $evSpread->get($statId)->getValue(),
				$level
			);
			$statSpread->add(new StatValue($statId, $value));
		}

		return $statSpread;
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

		$statIds = StatId::getByGeneration(new GenerationId(3));
		foreach ($statIds as $statId) {
			if ($statId->value() === StatId::HP) {
				// Calculate HP.
				$value = $this->hp3(
					(int) $baseStats->get($statId)->getValue(),
					(int) $ivSpread->get($statId)->getValue(),
					(int) $evSpread->get($statId)->getValue(),
					$level
				);
				$statSpread->add(new StatValue($statId, $value));
				continue;
			}

			$value = $this->other3(
				(int) $baseStats->get($statId)->getValue(),
				(int) $ivSpread->get($statId)->getValue(),
				(int) $evSpread->get($statId)->getValue(),
				$level,
				$this->getNatureModifier($nature, $statId)
			);
			$statSpread->add(new StatValue($statId, $value));
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

	/**
	 * Get the perfect IV value for this generation.
	 *
	 * @param GenerationId $generationId
	 *
	 * @return int
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
	 *
	 * @param GenerationId $generationId
	 *
	 * @return int
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
